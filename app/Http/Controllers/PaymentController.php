<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Order;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentMode;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $payments, $pmodes;

    function __construct()
    {
        $this->middleware('permission:payment-list|payment-create|payment-edit|payment-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:payment-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:payment-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:payment-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {
            $this->payments = Payment::when(Auth::user()->roles->first()->id != 1, function ($q) {
                return $q->where('branch_id', Session::get('branch'));
            })->whereDate('created_at', Carbon::today())->withTrashed()->latest()->get();
            return $next($request);
        });

        $this->pmodes = PaymentMode::orderBy('name')->pluck('name', 'id');
    }

    public function index()
    {
        $payments = $this->payments;
        return view('backend.payment.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function fetch(Request $request)
    {
        $this->validate($request, [
            'order_number' => 'required',
        ]);
        $order = Order::where('id', $request->order_number)->where('branch_id', Session::get('branch'))->firstOrFail();
        return view('backend.payment.proceed', compact('order'));
    }

    public function create($id)
    {
        $pmodes = $this->pmodes;
        $order = Order::find(decrypt($id));
        $payments = Payment::where('order_id', decrypt($id))->orderByDesc('created_at')->get();
        return view('backend.payment.create', compact('pmodes', 'order', 'payments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
            'payment_mode' => 'required',
            'payment_type' => 'required',
        ]);
        try {
            $tot = Order::findOrFail($request->order_id);
            $paid = Payment::where('order_id', $request->order_id)->sum('amount');
            $credit = $tot->credit_used ?? 0;
            $due_amount = floatval($tot->invoice_total - ($paid + $credit));
            $amount = floatval($request->amount);
            if ($request->payment_type == 'balance' && $amount != $due_amount) :
                //throw new Exception("Balance amount should be " . $due_amount);
                if ($amount > $due_amount)
                    echo "amount gt" . $amount;
                if ($amount < $due_amount)
                    echo "amount lt";
                if ($amount == $due_amount)
                    echo "amount";
                die;
            endif;
            if ($request->payment_type != 'balance' && ($due_amount != $amount) && $request->generate_invoice) :
                throw new Exception("Please uncheck the Generate Invoice Box!");
            endif;
            Payment::create([
                'consultation_id' => $request->consultation_id,
                'patient_id' => 0,
                'order_id' => $request->order_id,
                'payment_type' => $request->payment_type,
                'payment_mode' => $request->payment_mode,
                'amount' => $amount,
                'notes' => $request->notes,
                'branch_id' => branch()->id,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            if ($request->generate_invoice) :
                Order::findOrFail($request->order_id)->update([
                    'invoice_number' => invoicenumber($request->order_id)->ino,
                    'order_sequence' => branchInvoiceNumber(),
                    'invoice_generated_by' => $request->user()->id,
                    'invoice_generated_at' => Carbon::now(),
                    'order_status' => 'delivered',
                ]);
                updateLabOrderStatus($request->order_id);
                recordOrderEvent($request->order_id, 'Invoice has been generated');
            endif;
            recordOrderEvent($request->order_id, 'Payment received');
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('patient.payments')->with("success", "Payment recorded successfully!");
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
        $payment = Payment::findOrFail(decrypt($id));
        $pmodes = $this->pmodes;
        return view('backend.payment.edit', compact('pmodes', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'amount' => 'required',
            'payment_mode' => 'required',
            'payment_type' => 'required',
        ]);
        Payment::findOrFail($id)->update([
            'payment_type' => $request->payment_type,
            'payment_mode' => $request->payment_mode,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'updated_by' => $request->user()->id,
        ]);
        recordOrderEvent(Payment::findOrFail($id)->order_id, 'Payment has been edited');
        return redirect()->route('patient.payments')->with("success", "Payment updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Payment::findOrFail(decrypt($id))->delete();
        return redirect()->route('patient.payments')->with("success", "Payment deleted successfully!");
    }
}
