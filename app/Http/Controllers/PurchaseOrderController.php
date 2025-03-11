<?php

namespace App\Http\Controllers;

use App\Models\PaymentMode;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PurchaseOrderController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:po-list|po-create|po-edit|po-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:po-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:po-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:po-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pos = PurchaseOrder::withTrashed()->latest()->get();
        return view('backend.po.index', compact('pos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pmodes = PaymentMode::pluck('name', 'id');
        return view('backend.po.create', compact('pmodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'to' => 'required',
            'for' => 'required',
            'customer' => 'required',
            'date' => 'required',
            'po_number' => 'required|unique:purchase_orders,po_number',
        ]);
        //try {
        DB::transaction(function () use ($request) {
            $input = $request->except(array('products', 'qty', 'rate', 'tax_percentage', 'tax_amount', 'total'));
            $data = [];
            $input['branch_id'] = Session::get('branch');
            $input['created_by'] = $request->user()->id;
            $input['updated_by'] = $request->user()->id;
            $po = PurchaseOrder::create($input);
            foreach ($request->products as $key => $item):
                $data[] = [
                    'po_id' => $po->id,
                    'product' => $item,
                    'qty' => $request->qty[$key],
                    'rate' => $request->rate[$key] ?? 0,
                    'tax_percentage' => $request->tax_percentage[$key] ?? 0,
                    'tax_amount' => $request->tax_amount[$key] ?? 0,
                    'total' => $request->total[$key] ?? 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            endforeach;
            PurchaseOrderDetail::insert($data);
        });
        //} catch (Exception $e) {
        //return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        //}
        return redirect()->route('po')->with("success", "PO created successfully!");
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
        $po = PurchaseOrder::findOrFail(decrypt($id));
        $pmodes = PaymentMode::pluck('name', 'id');
        return view('backend.po.edit', compact('po', 'pmodes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'to' => 'required',
            'for' => 'required',
            'customer' => 'required',
            'date' => 'required',
            'po_number' => 'required|unique:purchase_orders,po_number,' . $id,
        ]);
        try {
            DB::transaction(function () use ($request, $id) {
                $input = $request->except(array('products', 'qty', 'rate', 'tax_percentage', 'tax_amount', 'total'));
                $data = [];
                $input['updated_by'] = $request->user()->id;
                $po = PurchaseOrder::findOrFail($id);
                $po->update($input);
                foreach ($request->products as $key => $item):
                    $data[] = [
                        'po_id' => $po->id,
                        'product' => $item,
                        'qty' => $request->qty[$key],
                        'rate' => $request->rate[$key] ?? 0,
                        'tax_percentage' => $request->tax_percentage[$key] ?? 0,
                        'tax_amount' => $request->tax_amount[$key] ?? 0,
                        'total' => $request->total[$key] ?? 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                PurchaseOrderDetail::where('po_id', $po->id)->forceDelete();
                PurchaseOrderDetail::insert($data);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('po')->with("success", "PO updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $po = PurchaseOrder::findOrFail(decrypt($id));
        PurchaseOrderDetail::where('po_id', $po->id)->delete();
        $po->delete();
        return redirect()->route('po')->with("success", "PO deleted successfully!");
    }
}
