<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
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

            return $next($request);
        });

        $this->products = Product::selectRaw("id, category, CONCAT_WS('-', name, code) AS name")->whereIn('category', ['solution', 'accessory'])->orderBy('name')->get();
        $this->pmodes = PaymentMode::orderBy('name')->get();
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $products = $this->products;
        $pmodes = $this->pmodes;
        $padvisers = $this->padvisers;
        $states = State::all();
        /*$consultation = Consultation::with('patient')->find(decrypt($id));
        $mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', decrypt($id))->first();
        $patient = DB::connection('mysql1')->table('patient_registrations')->where('id', $mrecord->patient_id ?? 0)->first();*/
        $patient = Customer::findOrFail(decrypt($id));
        return view('backend.order.solution.create', compact('products', 'patient', 'pmodes', 'padvisers', 'states'));
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
                    'order_date' => $request->order_date,
                    'consultation_id' => $request->consultation_id,
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
                        'created_at' => $order->created_at ?? Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                OrderDetail::insert($data);
                if ($request->advance > 0) :
                    /*$p = Payment::where('order_id', $id)->where('payment_type', 'advance')->latest()->first();
                    Payment::where('order_id', $id)->where('payment_type', 'advance')->forceDelete();
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
                        'created_at' => $p->created_at ?? Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);*/
                    Payment::where('order_id', $id)->where('payment_type', 'advance')->update([
                        'amount' => $request->advance,
                        'payment_mode' => $request->payment_mode,
                        'updated_by' => $request->user()->id,
                        'updated_at' => Carbon::now(),
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
