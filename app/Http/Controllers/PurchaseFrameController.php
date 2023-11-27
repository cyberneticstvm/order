<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseFrameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $purchases, $suppliers, $products;
    public function __construct()
    {
        $this->middleware('permission:purchase-frame-list|purchase-frame-create|purchase-frame-edit|purchase-frame-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:purchase-frame-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchase-frame-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchase-frame-delete', ['only' => ['destroy']]);

        $this->purchases = Purchase::where('category', 'frame')->whereDate('delivery_date', Carbon::today())->withTrashed()->latest()->get();
        $this->suppliers = Supplier::pluck('name', 'id');
        $this->products = Product::whereIn('category', ['frame'])->orderBy('name')->pluck('name', 'id');
    }
    public function index()
    {
        $purchases = $this->purchases;
        return view('backend.purchase.frame.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = $this->suppliers;
        $products = $this->products;
        return view('backend.purchase.frame.create', compact('suppliers', 'products'));
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
            'purchase_invoice_number' => 'required',
            'product_id' => 'present|array'
        ]);
        try {
            DB::transaction(function () use ($request) {
                $purchase = Purchase::create([
                    'category' => 'frame',
                    'purchase_number' => purchaseId('frame')->pid,
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
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('frame.purchase')->with("success", "Purchase created successfully!");
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
        return view('backend.purchase.frame.edit', compact('suppliers', 'products', 'purchase'));
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
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('frame.purchase')->with("success", "Purchase updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Purchase::findOrFail(decrypt($id))->delete();
        return redirect()->back()->with('success', 'Purchase has been deleted successfully!');
    }
}
