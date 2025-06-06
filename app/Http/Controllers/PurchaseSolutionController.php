<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Transfer;
use App\Models\TransferDetails;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseSolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $purchases, $suppliers, $products;
    public function __construct()
    {
        $this->middleware('permission:purchase-solution-list|purchase-solution-create|purchase-solution-edit|purchase-solution-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:purchase-solution-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchase-solution-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchase-solution-delete', ['only' => ['destroy']]);

        $this->purchases = Purchase::where('category', 'solution')->whereDate('delivery_date', Carbon::today())->withTrashed()->latest()->get();
        $this->suppliers = Supplier::pluck('name', 'id');
        $this->products = Product::whereIn('category', ['solution'])->selectRaw("id, CONCAT_WS('-', name, code) AS name")->orderBy('name')->pluck('name', 'id');
    }

    public function index()
    {
        $purchases = $this->purchases;
        return view('backend.purchase.solution.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = $this->suppliers;
        $products = $this->products;
        $branches = Branch::where('ho_master', 1)->pluck('name', 'id');
        return view('backend.purchase.solution.create', compact('suppliers', 'products', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'order_date' => 'required',
            'delivery_date' => 'required',
            'supplier_id' => 'required',
            'branch_id' => 'required',
            'purchase_invoice_number' => 'required',
            'product_id' => 'present|array'
        ]);
        try {
            DB::transaction(function () use ($request) {
                $purchase = Purchase::create([
                    'category' => 'solution',
                    'purchase_number' => purchaseId('solution')->pid,
                    'order_date' => $request->order_date,
                    'delivery_date' => $request->delivery_date,
                    'supplier_id' => $request->supplier_id,
                    'purchase_invoice_number' => $request->purchase_invoice_number,
                    'purchase_note' => $request->purchase_note,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $data[] = [
                        'purchase_id' => $purchase->id,
                        'product_id' => $item,
                        'qty' => $request->qty[$key],
                        'unit_price_mrp' => $request->mrp[$key],
                        'unit_price_purchase' => $request->purchase_price[$key],
                        'unit_price_sales' => $request->selling_price[$key],
                        'total' => $request->total[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                PurchaseDetail::insert($data);
                $transfer = Transfer::create([
                    'transfer_number' => transferId('solution')->tid,
                    'category' => 'solution',
                    'transfer_date' => Carbon::today(),
                    'from_branch_id' => 0,
                    'to_branch_id' => $request->branch_id,
                    'transfer_note' => 'Purchase with id ' . $purchase->id,
                    'transfer_status' => 1,
                    'purchase_id' => $purchase->id,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $data[] = [
                        'transfer_id' => $transfer->id,
                        'product_id' => $item,
                        'qty' => $request->qty[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                TransferDetails::insert($data);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('solution.purchase')->with("success", "Purchase created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $suppliers = $this->suppliers;
        $products = $this->products;
        $purchase = Purchase::findOrFail(decrypt($id));
        $branches = Branch::where('ho_master', 1)->pluck('name', 'id');
        return view('backend.purchase.solution.edit', compact('suppliers', 'products', 'purchase', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'order_date' => 'required',
            'delivery_date' => 'required',
            'supplier_id' => 'required',
            'branch_id' => 'required',
            'purchase_invoice_number' => 'required',
            'product_id' => 'present|array'
        ]);
        try {
            DB::transaction(function () use ($request, $id) {
                Purchase::findOrFail($id)->update([
                    'order_date' => $request->order_date,
                    'delivery_date' => $request->delivery_date,
                    'supplier_id' => $request->supplier_id,
                    'purchase_invoice_number' => $request->purchase_invoice_number,
                    'purchase_note' => $request->purchase_note,
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $data[] = [
                        'purchase_id' => $id,
                        'product_id' => $item,
                        'qty' => $request->qty[$key],
                        'unit_price_mrp' => $request->mrp[$key],
                        'unit_price_purchase' => $request->purchase_price[$key],
                        'unit_price_sales' => $request->selling_price[$key],
                        'total' => $request->total[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                PurchaseDetail::where('purchase_id', $id)->delete();
                PurchaseDetail::insert($data);
                $t = Transfer::where('purchase_id', $id)->first();
                TransferDetails::where('transfer_id', $t->id)->delete();
                $t->delete();
                $transfer = Transfer::create([
                    'transfer_number' => transferId('solution')->tid,
                    'category' => 'solution',
                    'transfer_date' => Carbon::today(),
                    'from_branch_id' => 0,
                    'to_branch_id' => $request->branch_id,
                    'transfer_note' => 'Purchase with id ' . $id,
                    'transfer_status' => 1,
                    'purchase_id' => $id,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $data[] = [
                        'transfer_id' => $transfer->id,
                        'product_id' => $item,
                        'qty' => $request->qty[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                TransferDetails::insert($data);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('solution.purchase')->with("success", "Purchase updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $p = Purchase::findOrFail(decrypt($id));
        $t = Transfer::where('purchase_id', $p->id);
        $p->delete();
        $t->delete();
        return redirect()->back()->with('success', 'Purchase has been deleted successfully!');
    }
}
