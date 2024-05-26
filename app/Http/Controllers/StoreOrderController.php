<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\LabOrder;
use App\Models\MedicalRecord;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Power;
use App\Models\Product;
use App\Models\Registration;
use App\Models\Spectacle;
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
            $this->orders = Order::whereIn('category', ['store', 'solution'])->when(Auth::user()->roles->first()->id != 1, function ($q) {
                return $q->where('branch_id', Session::get('branch'));
            })->whereDate('created_at', Carbon::today())->withTrashed()->latest()->get();

            $this->padvisers = User::leftJoin('user_branches as ub', 'users.id', 'ub.user_id')->select('users.id', 'users.name')->where('ub.branch_id', Session::get('branch'))->role('Sales Advisor')->get();

            return $next($request);
        });

        $this->products = Product::whereIn('category', ['lens', 'frame', 'service'])->orderBy('name')->get();
        $this->pmodes = PaymentMode::orderBy('name')->get();
    }
    public function index()
    {
        $orders = Order::whereIn('category', ['store', 'solution'])->where('branch_id', Session::get('branch'))->whereDate('created_at', Carbon::today())->withTrashed()->where('order_status', '!=', 'delivered')->latest()->get();
        return view('backend.order.store.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id, $type)
    {
        $products = Product::selectRaw("id, category, CONCAT_WS('-', name, code) AS name")->whereIn('category', ['lens', 'frame', 'service'])->orderBy('name')->get();
        $pmodes = $this->pmodes;
        $padvisers = $this->padvisers;
        $states = State::all();
        $registration = Registration::findOrFail(decrypt($id));
        $patient = Customer::findOrFail($registration->customer_id);
        $spectacle = Spectacle::where('registration_id', $registration->id)->latest()->first();
        $powers = Power::all();
        $store_prescriptions = Spectacle::where('customer_id', $patient->id)->selectRaw("CONCAT_WS(' / ', 'CID', customer_id, DATE_FORMAT(created_at, '%d/%b/%Y')) AS cid, id")->get();
        return view(($type == 1) ? 'backend.order.store.create' : 'backend.order.solution.create', compact('products', 'patient', 'pmodes', 'padvisers', 'powers', 'states', 'registration', 'store_prescriptions', 'spectacle'));
    }

    public function fetch(Request $request)
    {
        /*$this->validate($request, [
            'medical_record_number' => 'required',
            'type' => 'required',
        ]);
        $consultation = Consultation::with('patient')->findOrFail($request->medical_record_number);
        $mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', $request->medical_record_number)->first();
        if ($mrecord) :
            $type = $request->type;
            $patient = DB::connection('mysql1')->table('patient_registrations')->where('id', $mrecord->patient_id)->first();
            return view('backend.order.store.proceed', compact('mrecord', 'patient', 'type'));
        else :
            return redirect()->back()->with('error', 'No records found')->withInput($request->all());
        endif;*/
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'order_date' => 'required',
            'name' => 'required',
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
                    'customer_id' => $request->customer_id,
                    'order_sequence' => NULL,
                    'order_date' => $request->order_date,
                    'consultation_id' => $request->consultation_id,
                    'registration_id' => $request->registration_id,
                    'spectacle_id' => $request->spectacle_id,
                    'name' => $request->name,
                    'age' => $request->age,
                    'place' => $request->place,
                    'mobile' => $request->mobile,
                    'alt_mobile' => $request->alt_mobile,
                    'int_add' => $request->int_add,
                    'a_size' => $request->a_size,
                    'b_size' => $request->b_size,
                    'dbl' => $request->dbl,
                    'fh' => $request->fh,
                    'ed' => $request->ed,
                    'vd' => $request->vd,
                    'w_angle' => $request->w_angle,
                    'special_lab_note' => $request->special_lab_note,
                    'invoice_number' => NULL,
                    'category' => 'store',
                    'branch_id' => branch()->id,
                    'order_total' => $request->order_total,
                    'invoice_total' => $request->invoice_total,
                    'discount' => $request->discount,
                    'advance' => $request->advance,
                    'credit_used' => $request->credit_used,
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
                        'ipd' => $request->ipd[$key],
                        'va' => $request->va[$key],
                        'fitting' => $request->fitting[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                OrderDetail::insert($data);
                if ($request->advance > 0) :
                    Payment::create([
                        'consultation_id' => $request->consultation_id,
                        'patient_id' => $request->customer_id,
                        'order_id' => $order->id,
                        'payment_type' => 'advance',
                        'amount' => $request->advance,
                        'payment_mode' => $request->payment_mode,
                        'notes' => 'Advance received against order number ' . $order->ono(),
                        'branch_id' => branch()->id,
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                endif;
                Registration::where('id', $request->registration_id)->update(['order_id' => $order->id]);
                if ($request->credit_used > 0) :
                    CustomerAccount::create([
                        'customer_id' => $order->customer_id,
                        'voucher_id' => $order->id,
                        'type' => 'debit',
                        'category' => 'order',
                        'amount' => $request->credit_used,
                        'remarks' => 'Credit used against order number' . $order->ono(),
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                endif;
                if ($request->advance > $request->invoice_total) :
                    CustomerAccount::create([
                        'customer_id' => $order->customer_id,
                        'voucher_id' => $order->id,
                        'type' => 'credit',
                        'category' => 'order',
                        'amount' => $request->advance - $request->invoice_total,
                        'remarks' => 'Excess amount credited against order number' . $order->ono(),
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                endif;
                recordOrderEvent($order->id, 'Order has been created');
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
        $order = Order::where('id', decrypt($id))->whereNull('invoice_number')->firstOrFail();
        $products = Product::selectRaw("id, category, CONCAT_WS('-', name, code) AS name")->whereIn('category', ['lens', 'frame', 'service'])->orderBy('name')->get();
        $pmodes = $this->pmodes;
        $padvisers = $this->padvisers;
        $store_prescriptions = Spectacle::where('customer_id', $order->customer_id)->selectRaw("CONCAT_WS(' / ', 'CID', customer_id, DATE_FORMAT(created_at, '%d/%b/%Y')) AS cid, id")->get();
        $powers = Power::all();
        $states = State::all();
        return view('backend.order.store.edit', compact('products', 'pmodes', 'padvisers', 'order', 'powers', 'states', 'store_prescriptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'order_date' => 'required',
            'name' => 'required',
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
                $order = Order::findOrFail($id);
                if (isProductChanged($order->id, $request->product_id)) :
                    LabOrder::where('order_id', $order->id)->delete();
                endif;
                Order::findOrFail($id)->update([
                    'spectacle_id' => $request->spectacle_id,
                    'order_date' => $request->order_date,
                    'name' => $request->name,
                    'age' => $request->age,
                    'place' => $request->place,
                    'mobile' => $request->mobile,
                    'alt_mobile' => $request->alt_mobile,
                    'int_add' => $request->int_add,
                    'a_size' => $request->a_size,
                    'b_size' => $request->b_size,
                    'dbl' => $request->dbl,
                    'fh' => $request->fh,
                    'ed' => $request->ed,
                    'vd' => $request->vd,
                    'w_angle' => $request->w_angle,
                    'special_lab_note' => $request->special_lab_note,
                    'order_total' => $request->order_total,
                    'invoice_total' => $request->invoice_total,
                    'discount' => $request->discount,
                    'advance' => $request->advance,
                    'credit_used' => $request->credit_used,
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
                        'ipd' => $request->ipd[$key] ?? NULL,
                        'va' => $request->va[$key],
                        'fitting' => $request->fitting[$key],
                        'created_at' => $order->created_at ?? Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                dd($data);
                die;
                OrderDetail::insert($data);
                if ($request->advance > 0) :
                    /*Payment::where('order_id', $id)->where('payment_type', 'advance')->update([
                        'amount' => $request->advance,
                        'payment_mode' => $request->payment_mode,
                        'updated_by' => $request->user()->id,
                        'updated_at' => Carbon::now(),
                    ]);*/
                    Payment::updateOrCreate(
                        ['order_id' => $id, 'payment_type' => 'advance'],
                        [
                            'consultation_id' => $order->consultation_id,
                            'patient_id' => $order->customer_id,
                            'order_id' => $id,
                            'amount' => $request->advance,
                            'payment_mode' => $request->payment_mode,
                            'payment_type' => 'advance',
                            'notes' => 'Advance received against order number ' . $order->ono(),
                            'branch_id' => branch()->id,
                            'created_by' => $order->created_by,
                            'updated_by' => $request->user()->id,
                            'created_at' => $order->created_at,
                            'updated_at' => Carbon::now(),
                        ]
                    );
                endif;
                if ($request->credit_used > 0) :
                    $caccount = CustomerAccount::where('category', 'order')->where('type', 'debit')->where('voucher_id', $order->id)->first();
                    if ($caccount)
                        $caccount->forcedelete();
                    CustomerAccount::insert([
                        'customer_id' => $order->customer_id,
                        'voucher_id' => $order->id,
                        'type' => 'debit',
                        'category' => 'order',
                        'amount' => $request->credit_used,
                        'remarks' => "Credit has been used against order ID " . $order->id,
                        'created_at' => ($caccount?->created_at) ?? Carbon::now(),
                        'created_by' => ($caccount?->created_by) ?? $request->user()->id,
                        'updated_by' => $request->user()->id,
                        'updated_at' => Carbon::now(),
                    ]);
                endif;
                if ($request->advance > $request->invoice_total) :
                    $caccount = CustomerAccount::where('category', 'order')->where('type', 'credit')->where('voucher_id', $order->id)->first();
                    if ($caccount)
                        $caccount->forcedelete();
                    CustomerAccount::create([
                        'customer_id' => $order->customer_id,
                        'voucher_id' => $order->id,
                        'type' => 'credit',
                        'category' => 'order',
                        'amount' => $request->advance - $request->invoice_total,
                        'remarks' => 'Excess amount credited against order number' . $order->ono(),
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                endif;
                recordOrderEvent($order->id, 'Order has been edited');
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
        $order = Order::findOrFail(decrypt($id));
        if (LabOrder::where('order_id', $order->id)->exists()) :
            return redirect()->back()->with('error', 'Order has already been assigned to Lab!');
        else :
            cancelOrder($order->id);
            return redirect()->back()->with('success', 'Order has been deleted successfully!');
        endif;
    }
}
