<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentMode;
use Carbon\Carbon;
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
            'medical_record_number' => 'required',
        ]);
        $consultation = Consultation::with('patient')->findOrFail($request->medical_record_number);
        return view('backend.payment.proceed', compact('consultation'));
    }

    public function create($id)
    {
        $pmodes = $this->pmodes;
        $consultation = Consultation::find(decrypt($id));
        $patient = Patient::with('consultation')->find($consultation?->patient_id);
        $payments = Payment::where('patient_id', $patient?->id)->orderByDesc('created_at')->get();
        return view('backend.payment.create', compact('pmodes', 'patient', 'payments', 'consultation'));
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
        Payment::create([
            'consultation_id' => $request->consultation_id,
            'patient_id' => $request->patient_id,
            'payment_type' => $request->payment_type,
            'payment_mode' => $request->payment_mode,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'branch_id' => branch()->id,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
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
