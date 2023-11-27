<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationType;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:consultation-list|consultation-create|consultation-edit|consultation-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:consultation-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:consultation-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:consultation-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $consultations = Consultation::whereDate('created_at', Carbon::today())->where('branch_id', Session::get('branch'))->withTrashed()->latest()->get();
        return view('backend.consultation.index', compact('consultations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($pid)
    {
        $ctypes = ConsultationType::pluck('name', 'id');
        $depts = Department::pluck('name', 'id');
        $doctors = Doctor::pluck('name', 'id');
        $patient = Patient::findOrFail(decrypt($pid));
        return view('backend.consultation.create', compact('ctypes', 'depts', 'doctors', 'patient'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'patient_id' => 'required',
            'consultation_type' => 'required',
            'department_id' => 'required',
            'doctor_id' => 'required',
        ]);
        $exists = Consultation::where('patient_id', $request->patient_id)->whereDate('created_at', Carbon::today())->first();
        if ($exists) :
            return redirect()->back()->with("error", "Patient already have a MRN today - " . $exists->mrn)->withInput($request->all());
        else :
            Consultation::create([
                'mrn' => mrn()->mrid,
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'doctor_fee' => getDocFee($request->doctor_id, $request->patient_id, $request->consultation_type),
                'department_id' => $request->department_id,
                'consultation_type' => $request->consultation_type,
                'review' => 1,
                'branch_id' => branch()->id,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            return redirect()->route('consultations')->with('success', 'Consultation has been created successfully!');
        endif;
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
        $consultation = Consultation::with('patient')->findOrFail(decrypt($id));
        $ctypes = ConsultationType::pluck('name', 'id');
        $depts = Department::pluck('name', 'id');
        $doctors = Doctor::pluck('name', 'id');
        return view('backend.consultation.edit', compact('consultation', 'ctypes', 'depts', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'consultation_type' => 'required',
            'department_id' => 'required',
            'doctor_id' => 'required',
        ]);
        $pid = Consultation::findOrFail($id)->patient_id;
        Consultation::findOrFail($id)->update([
            'doctor_id' => $request->doctor_id,
            'doctor_fee' => getDocFee($request->doctor_id, $pid, $request->consultation_type),
            'department_id' => $request->department_id,
            'consultation_type' => $request->consultation_type,
        ]);
        return redirect()->route('consultations')->with('success', 'Consultation updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Consultation::findOrFail(decrypt($id))->delete();
        return redirect()->back()->with('success', 'Consultation deleted successfully!');
    }
}
