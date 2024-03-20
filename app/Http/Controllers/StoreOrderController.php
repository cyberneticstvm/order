<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\MedicalRecord;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Power;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StoreOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $orders, $products, $pmodes, $padvisers;

    public function __construct()
    {
        $this->middleware('permission:store-order-list|store-order-create|store-order-edit|store-order-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:store-order-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:store-order-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:store-order-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {
            $this->orders = Order::where('category', 'store')->when(Auth::user()->roles->first()->id != 1, function ($q) {
                return $q->where('branch_id', Session::get('branch'));
            })->whereDate('created_at', Carbon::today())->withTrashed()->latest()->get();
            return $next($request);
        });

        $this->products = Product::whereIn('category', ['lens', 'frame', 'service'])->orderBy('name')->get();
        $this->pmodes = PaymentMode::orderBy('name')->get();
        $this->padvisers = User::orderBy('name')->get();
    }
    public function index()
    {
        $orders = Order::where('category', 'store')->where('branch_id', Session::get('branch'))->whereDate('created_at', Carbon::today())->withTrashed()->latest()->get();
        return view('backend.order.store.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $products = Product::selectRaw("id, category, CONCAT_WS('-', name, code) AS name")->whereIn('category', ['lens', 'frame', 'service'])->orderBy('name')->get();
        $pmodes = $this->pmodes;
        $padvisers = $this->padvisers;
        $states = State::all();
        /*$consultation = Consultation::with('patient')->find(decrypt($id));*/
        $mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', decrypt($id))->first();
        $spectacle = DB::connection('mysql1')->table('spectacles')->where('medical_record_id', decrypt($id))->first();
        $patient = DB::connection('mysql1')->table('patient_registrations')->where('id', $mrecord->patient_id ?? 0)->first();
        $powers = Power::all();
        return view('backend.order.store.create', compact('products', 'patient', 'pmodes', 'padvisers', 'mrecord', 'spectacle', 'powers', 'states'));
    }

    public function fetch(Request $request)
    {
        $this->validate($request, [
            'medical_record_number' => 'required',
        ]);
        /*$consultation = Consultation::with('patient')->findOrFail($request->medical_record_number);*/
        $mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', $request->medical_record_number)->first();
        if ($mrecord) :
            $patient = DB::connection('mysql1')->table('patient_registrations')->where('id', $mrecord->patient_id)->first();
            return view('backend.order.store.proceed', compact('mrecord', 'patient'));
        else :
            return redirect()->back()->with('error', 'No records found')->withInput($request->all());
        endif;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'order_date' => 'required',
            'name' => 'required',
            'age' => 'required',
            'place' => 'required',
            'mobile' => 'required|numeric|digits:10',
            'product_adviser' => 'required',
            'expected_delivery_date' => 'required',
            'order_status' => 'required',
            'order_total' => 'required|numeric|min:0|not_in:0',
            'invoice_total' => 'required|numeric|min:0|not_in:0',
            'product_id' => 'present|array'
        ]);
        /*if (!settings()->allow_sales_at_zero_qty) :
            $status = checkOrderedProductsAvailability($request);
        endif;*/
        try {
            DB::transaction(function () use ($request) {
                $order = Order::create([
                    'order_date' => $request->order_date,
                    'consultation_id' => $request->consultation_id,
                    'name' => $request->name,
                    'age' => $request->age,
                    'place' => $request->place,
                    'mobile' => $request->mobile,
                    'invoice_number' => NULL,
                    'category' => 'store',
                    'branch_id' => branch()->id,
                    'order_total' => $request->order_total,
                    'invoice_total' => $request->invoice_total,
                    'discount' => $request->discount,
                    'advance' => $request->advance,
                    'balance' => $request->balance,
                    'order_status' => $request->order_status,
                    'case_type' => $request->case_type,
                    'product_adviser' => $request->product_adviser,
                    'expected_delivery_date' => $request->expected_delivery_date,
                    'order_note' => $request->order_note,
                    'lab_note' => $request->lab_note,
                    'invoice_note' => $request->invoice_note,
                    'gstin' => $request->gstin,
                    'company_name' => $request->company_name,
                    'type' => $request->type,
                    'state' => $request->state,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $product = Product::findOrFail($item);
                    $data[] = [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'qty' => $request->qty[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total' => $request->total[$key],
                        'tax_percentage' => $product->tax_percentage,
                        'tax_amount' => $product->taxamount($request->total[$key]),
                        'eye' => $request->eye[$key],
                        'sph' => $request->sph[$key] ?? NULL,
                        'cyl' => $request->cyl[$key] ?? NULL,
                        'axis' => $request->axis[$key] ?? NULL,
                        'add' => $request->add[$key] ?? NULL,
                        'dia' => $request->dia[$key],
                        'ipd' => $request->ipd[$key],
                        'int_add' => $request->int_add[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                OrderDetail::insert($data);
                if ($request->advance > 0) :
                    Payment::create([
                        'consultation_id' => $request->consultation_id,
                        'patient_id' => Consultation::find($request->consultation_id)?->patient_id,
                        'order_id' => $order->id,
                        'payment_type' => 'advance',
                        'amount' => $request->advance,
                        'payment_mode' => $request->payment_mode,
                        'notes' => 'Advance received against order number ' . $order->branch->code . '/' . $order->id,
                        'branch_id' => branch()->id,
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                endif;
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('store.order')->with("success", "Order placed successfully!");
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
        $products = Product::selectRaw("id, category, CONCAT_WS('-', name, code) AS name")->whereIn('category', ['lens', 'frame', 'service'])->orderBy('name')->get();
        $pmodes = $this->pmodes;
        $padvisers = $this->padvisers;
        $order = Order::with('details')->findOrFail(decrypt($id));
        $powers = Power::all();
        $states = State::all();
        return view('backend.order.store.edit', compact('products', 'pmodes', 'padvisers', 'order', 'powers', 'states'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'order_date' => 'required',
            'name' => 'required',
            'age' => 'required',
            'place' => 'required',
            'mobile' => 'required|numeric|digits:10',
            'product_adviser' => 'required',
            'expected_delivery_date' => 'required',
            'order_status' => 'required',
            'order_total' => 'required|numeric|min:0|not_in:0',
            'invoice_total' => 'required|numeric|min:0|not_in:0',
            'product_id' => 'present|array'
        ]);
        try {
            DB::transaction(function () use ($request, $id) {
                Order::findOrFail($id)->update([
                    'order_date' => $request->order_date,
                    'name' => $request->name,
                    'age' => $request->age,
                    'place' => $request->place,
                    'mobile' => $request->mobile,
                    'order_total' => $request->order_total,
                    'invoice_total' => $request->invoice_total,
                    'discount' => $request->discount,
                    'advance' => $request->advance,
                    'balance' => $request->balance,
                    'order_status' => $request->order_status,
                    'case_type' => $request->case_type,
                    'product_adviser' => $request->product_adviser,
                    'expected_delivery_date' => $request->expected_delivery_date,
                    'order_note' => $request->order_note,
                    'lab_note' => $request->lab_note,
                    'invoice_note' => $request->invoice_note,
                    'gstin' => $request->gstin,
                    'company_name' => $request->company_name,
                    'type' => $request->type,
                    'state' => $request->state,
                    'updated_by' => $request->user()->id,
                ]);
                OrderDetail::where('order_id', $id)->delete();
                Payment::where('order_id', $id)->where('payment_type', 'advance')->forceDelete();
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $product = Product::findOrFail($item);
                    $data[] = [
                        'order_id' => $id,
                        'product_id' => $product->id,
                        'qty' => $request->qty[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total' => $request->total[$key],
                        'tax_percentage' => $product->tax_percentage,
                        'tax_amount' => $product->taxamount($request->total[$key]),
                        'eye' => $request->eye[$key],
                        'sph' => $request->sph[$key] ?? NULL,
                        'cyl' => $request->cyl[$key] ?? NULL,
                        'axis' => $request->axis[$key] ?? NULL,
                        'add' => $request->add[$key] ?? NULL,
                        'dia' => $request->dia[$key],
                        'ipd' => $request->ipd[$key],
                        'int_add' => $request->int_add[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                OrderDetail::insert($data);
                if ($request->advance > 0) :
                    $order = Order::findOrFail($id);
                    Payment::create([
                        'consultation_id' => $request->consultation_id,
                        'patient_id' => Consultation::find($request->consultation_id)?->patient_id,
                        'order_id' => $id,
                        'payment_type' => 'advance',
                        'amount' => $request->advance,
                        'payment_mode' => $request->payment_mode,
                        'notes' => 'Advance received against order number ' . $order->branch->code . '/' . $order->id,
                        'branch_id' => branch()->id,
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                endif;
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('store.order')->with("success", "Order updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Order::findOrFail(decrypt($id))->delete();
        return redirect()->back()->with('success', 'Order has been deleted successfully!');
    }
}
