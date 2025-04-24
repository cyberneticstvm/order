<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\Registration;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SolutionOrderController extends Controller
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

            $this->padvisers = User::leftJoin('user_branches as ub', 'users.id', 'ub.user_id')->select('users.id', 'users.name')->where('ub.branch_id', Session::get('branch'))->role('Sales Advisor')->get();

            $this->products = getInventory(Session::get('branch'), 0, 'solution')->where('balanceQty', '>', 0);

            return $next($request);
        });

        //$this->products = Product::selectRaw("id, category, CONCAT_WS('-', name, code) AS name")->whereIn('category', ['solution', 'accessory'])->orderBy('name')->get();

        $this->pmodes = PaymentMode::orderBy('name')->get();
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id, $type)
    {
        $products = $this->products;
        $pmodes = $this->pmodes;
        $padvisers = $this->padvisers;
        $states = State::all();
        /*$consultation = Consultation::with('patient')->find(decrypt($id));
        $mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', decrypt($id))->first();
        $patient = DB::connection('mysql1')->table('patient_registrations')->where('id', $mrecord->patient_id ?? 0)->first();*/
        $registration = Registration::findOrFail(decrypt($id));
        $patient = Customer::findOrFail($registration->customer_id);
        return view('backend.order.solution.create', compact('products', 'patient', 'pmodes', 'padvisers', 'states', 'registration'));
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
        try {
            DB::transaction(function () use ($request) {
                $order = Order::create([
                    'customer_id' => $request->customer_id,
                    'order_sequence' => NULL,
                    'order_date' => $request->order_date,
                    'consultation_id' => $request->consultation_id,
                    'registration_id' => $request->registration_id,
                    'name' => $request->name,
                    'age' => $request->age,
                    'place' => $request->place,
                    'mobile' => $request->mobile,
                    'alt_mobile' => $request->alt_mobile,
                    'invoice_number' => NULL,
                    'category' => 'solution',
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
                        'batch_number' => $request->batch_number[$key],
                        'expiry_date' => $request->expiry_date[$key],
                        'qty' => $request->qty[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total' => $request->total[$key],
                        'tax_percentage' => $product->tax_percentage,
                        'tax_amount' => $product->taxamount($request->total[$key]),
                        'eye' => 'solution',
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
                Registration::where('id', $request->registration_id)->update(['order_id' => $order->id]);
                recordOrderEvent($order->id, 'Order has been created');
                sendWAMessageWithLink($order, 'receipt');
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
        $products = $this->products;
        $pmodes = $this->pmodes;
        $padvisers = $this->padvisers;
        $states = State::all();
        return view('backend.order.solution.edit', compact('products', 'pmodes', 'padvisers', 'order', 'states'));
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
                Order::findOrFail($id)->update([
                    'order_date' => $request->order_date,
                    'name' => $request->name,
                    'age' => $request->age,
                    'place' => $request->place,
                    'mobile' => $request->mobile,
                    'alt_mobile' => $request->alt_mobile,
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
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $product = Product::findOrFail($item);
                    $data[] = [
                        'order_id' => $id,
                        'product_id' => $product->id,
                        'batch_number' => $request->batch_number[$key],
                        'expiry_date' => $request->expiry_date[$key],
                        'qty' => $request->qty[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total' => $request->total[$key],
                        'tax_percentage' => $product->tax_percentage,
                        'tax_amount' => $product->taxamount($request->total[$key]),
                        'eye' => 'solution',
                        'created_at' => $order->created_at ?? Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                OrderDetail::insert($data);
                if ($request->advance > 0) :
                    /*Payment::where('order_id', $id)->where('payment_type', 'advance')->update([
                        'amount' => $request->advance,
                        'payment_mode' => $request->payment_mode,
                        'updated_by' => $request->user()->id,
                        'updated_at' => Carbon::now(),
                    ]);*/
                    $p = Payment::where('order_id', $id)->where('payment_type', 'advance');
                    $created_by = $request->user()->id;
                    $created_at = Carbon::now();
                    if ($p->exists()) :
                        $created_by = $p->first()->created_by;
                        $created_at = $p->first()->created_at;
                    endif;
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
                            'created_by' => $created_by,
                            'updated_by' => $request->user()->id,
                            'created_at' => $created_at,
                            'updated_at' => Carbon::now(),
                        ]
                    );
                endif;
                if ($request->credit_used > 0) :
                    CustomerAccount::where('category', 'order')->where('type', 'debit')->where('voucher_id', $id)->update([
                        'amount' => $request->credit_used,
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
        cancelOrder($order->id);
        return redirect()->back()->with('success', 'Order has been deleted successfully!');
    }
}
