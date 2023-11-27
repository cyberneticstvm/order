<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CampPatient;
use App\Models\Consultation;
use App\Models\ConsultationType;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Collator;
use DB;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:patient-list|patient-create|patient-edit|patient-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:patient-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:patient-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:patient-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $patients = Patient::whereDate('created_at', Carbon::today())->where('branch_id', Session::get('branch'))->withTrashed()->latest()->get();
        return view('backend.patient.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($type, $type_id)
    {
        $ctypes = ConsultationType::pluck('name', 'id');
        $depts = Department::pluck('name', 'id');
        $doctors = Doctor::pluck('name', 'id');
        $patient = [];
        if ($type_id > 0) :
            if ($type == 'Appointment') :
                $patient = Appointment::findOrFail($type_id);
            endif;
            if ($type == 'Camp') :
                $patient = CampPatient::findOrFail($type_id);
            endif;
        endif;
        return view('backend.patient.create', compact('ctypes', 'depts', 'doctors', 'type', 'type_id', 'patient'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'place' => 'required',
            'mobile' => 'required|numeric|digits:10',
            'consultation_type' => 'required',
            'department_id' => 'required',
            'doctor_id' => 'required',
        ]);
        try {
            $patient = Patient::where('mobile', $request->mobile)->get();
            if ($patient->isEmpty() || Session::has('exists')) :
                DB::transaction(function () use ($request) {
                    $patient = Patient::create([
                        'name' => $request->name,
                        'patient_id' => patientId()->pid,
                        'age' => $request->age,
                        'gender' => $request->gender,
                        'place' => $request->place,
                        'mobile' => $request->mobile,
                        'branch_id' => branch()->id,
                        'registration_fee' => branch()->registration_fee,
                        'type' => $request->type,
                        'type_id' => $request->type_id,
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                    Consultation::create([
                        'mrn' => mrn()->mrid,
                        'patient_id' => $patient->id,
                        'doctor_id' => $request->doctor_id,
                        'doctor_fee' => getDocFee($request->doctor_id, $patient->id, $request->consultation_type),
                        'department_id' => $request->department_id,
                        'consultation_type' => $request->consultation_type,
                        'review' => 0,
                        'branch_id' => branch()->id,
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                    if ($request->type_id > 0) :
                        if ($request->type == 'Appointment') :
                            Appointment::findOrFail($request->type_id)->update(['patient_id' => $patient->id]);
                        endif;
                        if ($request->type == 'Camp') :
                            CampPatient::findOrFail($request->type_id)->update(['patient_id' => $patient->id]);
                        endif;
                    endif;
                });
                if (Session::has('exists')) :
                    Session::forget('exists');
                endif;
            else :
                Session::put('exists', true);
                return redirect()->back()->with('warning', 'We found an existing records with provided Mobile Number')->withInput($request->all());
            endif;
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('consultations')->with('success', 'Patient has been registered successfully!');
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
        $patient = Patient::findOrFail(decrypt($id));
        return view('backend.patient.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'place' => 'required',
            'mobile' => 'required|numeric|digits:10',
        ]);
        Patient::findOrFail($id)->update([
            'name' => $request->name,
            'age' => $request->age,
            'gender' => $request->gender,
            'place' => $request->place,
            'mobile' => $request->mobile,
        ]);
        return redirect()->route('patients')->with('success', 'Patient has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Patient::findOrFail(decrypt($id))->delete();
        return redirect()->back()->with('success', 'Patient has been deleted successfully!');
    }
}
