<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\SupplierAccount;
use App\Models\Transfer;
use App\Models\TransferDetails;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseLensController extends Controller
{
    protected $purchases, $suppliers, $products;

    public function __construct()
    {
        $this->middleware('permission:purchase-lens-list|purchase-lens-create|purchase-lens-edit|purchase-lens-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:purchase-lens-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchase-lens-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchase-lens-delete', ['only' => ['destroy']]);

        $this->purchases = Purchase::where('category', 'lens')->withTrashed()->latest()->get();
        $this->suppliers = Supplier::orderBy('name')->pluck('name', 'id');
        $this->products = Product::where('category', 'lens')
            ->select('id', 'name', 'code', 'tax_percentage')
            ->orderBy('name')
            ->get();
    }

    public function index()
    {
        $purchases = $this->purchases;

        return view('backend.purchase.lens.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = $this->suppliers;
        $products = $this->products;
        $branches = Branch::where('ho_master', 1)->orderBy('name')->pluck('name', 'id');

        return view('backend.purchase.lens.create', compact('suppliers', 'products', 'branches'));
    }

    public function store(Request $request)
    {
        $this->validatePurchase($request);

        try {
            DB::transaction(function () use ($request) {
                $purchase = Purchase::create($this->purchaseData($request) + [
                    'category' => 'lens',
                    'purchase_number' => purchaseId('lens')->pid,
                    'created_by' => $request->user()->id,
                ]);

                [$details, $lineTotal] = $this->purchaseDetails($request, $purchase->id);
                $finalTotal = $this->finalTotal($request, $lineTotal);

                PurchaseDetail::insert($details);
                $this->createTransfer($request, $purchase->id);
                $this->createSupplierCredit($request->supplier_id, $purchase->id, $finalTotal);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }

        return redirect()->route('lens.purchase')->with('success', 'Purchase created successfully!');
    }

    public function edit(string $id)
    {
        $suppliers = $this->suppliers;
        $products = $this->products;
        $branches = Branch::where('ho_master', 1)->orderBy('name')->pluck('name', 'id');
        $purchase = Purchase::where('category', 'lens')->findOrFail(decrypt($id));
        $tot = $this->storedFinalTotal($purchase);

        return view('backend.purchase.lens.edit', compact('suppliers', 'products', 'branches', 'purchase', 'tot'));
    }

    public function update(Request $request, string $id)
    {
        $this->validatePurchase($request);

        try {
            DB::transaction(function () use ($request, $id) {
                $purchase = Purchase::where('category', 'lens')->findOrFail($id);
                $purchase->update($this->purchaseData($request));

                [$details, $lineTotal] = $this->purchaseDetails($request, $purchase->id);
                $finalTotal = $this->finalTotal($request, $lineTotal);

                PurchaseDetail::where('purchase_id', $purchase->id)->delete();
                PurchaseDetail::insert($details);

                $this->deleteTransfers($purchase->id);
                $this->createTransfer($request, $purchase->id);

                SupplierAccount::where('pr_id', $purchase->id)->where('type', 'cr')->delete();
                $this->createSupplierCredit($request->supplier_id, $purchase->id, $finalTotal);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }

        return redirect()->route('lens.purchase')->with('success', 'Purchase updated successfully!');
    }

    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $purchase = Purchase::where('category', 'lens')->findOrFail(decrypt($id));

                $this->deleteTransfers($purchase->id);
                SupplierAccount::where('pr_id', $purchase->id)->where('type', 'cr')->delete();
                $purchase->delete();
            });
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Purchase has been deleted successfully!');
    }

    private function validatePurchase(Request $request): void
    {
        $this->validate($request, [
            'order_date' => ['required', 'date'],
            'delivery_date' => ['required', 'date'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'branch_id' => [
                'required',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($query) => $query->where('ho_master', 1)->whereNull('deleted_at')),
            ],
            'purchase_invoice_number' => ['required', 'string', 'max:255'],
            'purchase_note' => ['nullable', 'string'],
            'other_charges' => ['nullable', 'numeric', 'min:0'],
            'other_charges_desc' => ['nullable', 'string', 'max:255'],
            'adjust_type' => ['nullable', Rule::in(['plus', 'minus'])],
            'adjust_amount' => ['nullable', 'numeric', 'min:0'],
            'adjust_desc' => ['nullable', 'string', 'max:255'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'qty' => ['required', 'array'],
            'qty.*' => ['required', 'integer', 'min:1'],
            'purchase_price' => ['required', 'array'],
            'purchase_price.*' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'array'],
            'discount.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $lineCount = count($request->product_id);
        foreach (['qty', 'purchase_price', 'discount'] as $field) {
            if (count($request->input($field, [])) !== $lineCount) {
                throw ValidationException::withMessages([$field => 'The purchase detail rows are incomplete.']);
            }
        }

        if ((float) $request->adjust_amount > 0 && !$request->adjust_type) {
            throw ValidationException::withMessages(['adjust_type' => 'Select plus or minus for the adjustment amount.']);
        }
    }

    private function purchaseData(Request $request): array
    {
        return [
            'order_date' => $request->order_date,
            'delivery_date' => $request->delivery_date,
            'supplier_id' => $request->supplier_id,
            'purchase_invoice_number' => $request->purchase_invoice_number,
            'purchase_note' => $request->purchase_note,
            'other_charges' => round((float) ($request->other_charges ?? 0), 2),
            'other_charges_desc' => $request->other_charges_desc,
            'adjust_type' => $request->adjust_type,
            'adjust_amount' => round((float) ($request->adjust_amount ?? 0), 2),
            'adjust_desc' => $request->adjust_desc,
            'branch_id' => $request->branch_id,
            'updated_by' => $request->user()->id,
        ];
    }

    private function purchaseDetails(Request $request, int $purchaseId): array
    {
        $products = Product::where('category', 'lens')
            ->whereIn('id', $request->product_id)
            ->get()
            ->keyBy('id');

        if ($products->count() !== count($request->product_id)) {
            throw ValidationException::withMessages(['product_id' => 'Every selected product must be an active lens product.']);
        }

        $details = [];
        $lineTotal = 0;

        foreach ($request->product_id as $key => $productId) {
            $product = $products->get((int) $productId);
            $qty = (int) $request->qty[$key];
            $purchasePrice = round((float) $request->purchase_price[$key], 2);
            $discount = round((float) ($request->discount[$key] ?? 0), 2);

            if ($discount > $purchasePrice) {
                throw ValidationException::withMessages([
                    "discount.$key" => 'Discount cannot be greater than the purchase price.',
                ]);
            }

            $taxPercentage = round((float) ($product->tax_percentage ?? 0), 2);
            $taxableAmount = round($qty * ($purchasePrice - $discount), 2);
            $taxAmount = round(($taxableAmount * $taxPercentage) / 100, 2);
            $total = round($taxableAmount + $taxAmount, 2);

            $details[] = [
                'purchase_id' => $purchaseId,
                'product_id' => $product->id,
                'qty' => $qty,
                'unit_price_mrp' => 0,
                'unit_price_purchase' => $purchasePrice,
                'unit_price_sales' => 0,
                'discount' => $discount,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $lineTotal += $total;
        }

        return [$details, round($lineTotal, 2)];
    }

    private function finalTotal(Request $request, float $lineTotal): float
    {
        $total = $lineTotal + (float) ($request->other_charges ?? 0);
        $adjustment = (float) ($request->adjust_amount ?? 0);
        $total += $request->adjust_type === 'minus' ? -$adjustment : $adjustment;
        $total = round($total, 2);

        if ($total < 0) {
            throw ValidationException::withMessages(['adjust_amount' => 'Adjustment cannot make the purchase total negative.']);
        }

        return $total;
    }

    private function storedFinalTotal(Purchase $purchase): float
    {
        $total = (float) $purchase->detail->sum('total') + (float) $purchase->other_charges;
        $adjustment = (float) $purchase->adjust_amount;

        return round($total + ($purchase->adjust_type === 'minus' ? -$adjustment : $adjustment), 2);
    }

    private function createTransfer(Request $request, int $purchaseId): void
    {
        $transfer = Transfer::create([
            'transfer_number' => transferId('lens')->tid,
            'category' => 'lens',
            'transfer_date' => Carbon::today(),
            'from_branch_id' => 0,
            'to_branch_id' => $request->branch_id,
            'transfer_note' => 'Purchase with id ' . $purchaseId,
            'transfer_status' => 1,
            'purchase_id' => $purchaseId,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        $details = [];
        foreach ($request->product_id as $key => $productId) {
            $details[] = [
                'transfer_id' => $transfer->id,
                'product_id' => $productId,
                'qty' => $request->qty[$key],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        TransferDetails::insert($details);
    }

    private function deleteTransfers(int $purchaseId): void
    {
        $transfers = Transfer::where('purchase_id', $purchaseId)->where('category', 'lens')->get();

        foreach ($transfers as $transfer) {
            TransferDetails::where('transfer_id', $transfer->id)->delete();
            $transfer->delete();
        }
    }

    private function createSupplierCredit(int $supplierId, int $purchaseId, float $amount): void
    {
        SupplierAccount::create([
            'supplier_id' => $supplierId,
            'pr_id' => $purchaseId,
            'amount' => $amount,
            'type' => 'cr',
        ]);
    }
}
