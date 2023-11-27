<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\MedicalRecord;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\ProductSubcategory;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PharmacyOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $orders, $products, $pmodes, $types;

    public function __construct()
    {
        $this->middleware('permission:pharmacy-order-list|pharmacy-order-create|pharmacy-order-edit|pharmacy-order-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:pharmacy-order-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:pharmacy-order-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:pharmacy-order-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {
            $this->orders = Order::where('category', 'pharmacy')->when(Auth::user()->roles->first()->id != 1, function ($q) {
                return $q->where('branch_id', Session::get('branch'));
            })->whereDate('created_at', Carbon::today())->withTrashed()->latest()->get();
            return $next($request);
        });

        $this->products = Product::whereIn('category', ['pharmacy'])->orderBy('name')->pluck('name', 'id');
        $this->pmodes = PaymentMode::orderBy('name')->get();
        $this->types = ProductSubcategory::where('category', 'pharmacy')->where('attribute', 'type')->orderBy('name')->pluck('name', 'id');
    }

    public function index()
    {
        $orders = $this->orders;
        return view('backend.order.pharmacy.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $products = $this->products;
        $pmodes = $this->pmodes;
        $consultation = Consultation::with('patient')->find(decrypt($id));
        $types = $this->types;
        $mrecord = MedicalRecord::with('medicines')->where('consultation_id', $consultation?->id)->first();
        return view('backend.order.pharmacy.create', compact('products', 'consultation', 'mrecord', 'pmodes', 'types'));
    }

    public function fetch(Request $request)
    {
        $this->validate($request, [
            'medical_record_number' => 'required',
        ]);
        $consultation = Consultation::with('patient')->findOrFail($request->medical_record_number);
        return view('backend.order.pharmacy.proceed', compact('consultation'));
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
                    'invoice_number' => invoicenumber('pharmacy')->ino,
                    'category' => 'pharmacy',
                    'branch_id' => branch()->id,
                    'order_total' => $request->order_total,
                    'invoice_total' => $request->invoice_total,
                    'discount' => $request->discount,
                    'advance' => $request->advance,
                    'balance' => $request->balance,
                    'order_status' => 'delivered',
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $product = Product::findOrFail($item);
                    $data[] = [
                        'order_id' => $order->id,
                        'product_type' => $request->product_type[$key],
                        'product_id' => $product->id,
                        'batch_number' => $request->batch_number[$key],
                        'qty' => $request->qty[$key],
                        'dosage' => $request->dosage[$key],
                        'duration' => $request->duration[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total' => $request->total[$key],
                        'tax_percentage' => $product->tax_percentage,
                        'tax_amount' => $product->taxamount($request->total[$key]),
                        'eye' => $request->eye[$key],
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
                        'payment_type' => 'pharmacy',
                        'amount' => $request->advance,
                        'payment_mode' => $request->payment_mode,
                        'notes' => 'Advance received against invoice number ' . $order->invoice_number,
                        'branch_id' => branch()->id,
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                endif;
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('pharmacy.order')->with("success", "Order placed successfully!");
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
        $products = $this->products;
        $pmodes = $this->pmodes;
        $order = Order::with('details')->findOrFail(decrypt($id));
        $types = $this->types;
        return view('backend.order.pharmacy.edit', compact('products', 'pmodes', 'order', 'types'));
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
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                foreach ($request->product_id as $key => $item) :
                    $product = Product::findOrFail($item);
                    $data[] = [
                        'order_id' => $id,
                        'product_type' => $request->product_type[$key],
                        'product_id' => $product->id,
                        'batch_number' => $request->batch_number[$key],
                        'qty' => $request->qty[$key],
                        'dosage' => $request->dosage[$key],
                        'duration' => $request->duration[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total' => $request->total[$key],
                        'tax_percentage' => $product->tax_percentage,
                        'tax_amount' => $product->taxamount($request->total[$key]),
                        'eye' => $request->eye[$key],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                endforeach;
                OrderDetail::where('order_id', $id)->delete();
                OrderDetail::insert($data);
                Payment::where('order_id', $id)->where('payment_type', 'pharmacy')->delete();
                if ($request->advance > 0) :
                    Payment::create([
                        'consultation_id' => $request->consultation_id,
                        'patient_id' => Consultation::find($request->consultation_id)?->patient_id,
                        'order_id' => $id,
                        'payment_type' => 'pharmacy',
                        'amount' => $request->advance,
                        'payment_mode' => $request->payment_mode,
                        'notes' => 'Advance received against invoice number ' . Order::findOrFail($id)->invoice_number,
                        'branch_id' => branch()->id,
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                endif;
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('pharmacy.order')->with("success", "Order placed successfully!");
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
