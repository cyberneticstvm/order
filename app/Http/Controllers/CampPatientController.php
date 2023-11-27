<?php

namespace App\Http\Controllers;

use App\Models\Camp;
use App\Models\CampPatient;
use App\Models\CampPatientVision;
use Exception;
use Illuminate\Http\Request;
use DB;

class CampPatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct(){
        $this->middleware('permission:camp-patient-list|camp-patient-create|camp-patient-edit|camp-patient-delete', ['only' => ['index','store']]);
        $this->middleware('permission:camp-patient-create', ['only' => ['create','store']]);
        $this->middleware('permission:camp-patient-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:camp-patient-delete', ['only' => ['destroy']]);
    }

    public function index($id)
    {
        $patients = CampPatient::where('camp_id', decrypt($id))->whereNull('patient_id')->withTrashed()->latest()->get();
        $camp = Camp::findOrFail(decrypt($id));
        return view('backend.camp.patient.index', compact('patients', 'camp'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $camp = Camp::findOrFail(decrypt($id));
        return view('backend.camp.patient.create', compact('camp'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'camp_id' => 'required',
            'name' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'place' => 'required',
            'mobile' => 'required|numeric|digits:10',
        ]);
        try{
            DB::transaction(function() use ($request) {
                $patient = CampPatient::create([
                    'camp_id' => $request->camp_id,
                    'name' => $request->name,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'place' => $request->place,
                    'mobile' => $request->mobile,
                    'further_investigation_advised' => isset($request->further_investigation_advised) ? 1 : 0,
                    'galsses_advised' => isset($request->galsses_advised) ? 1 : 0,
                    'yearly_eye_test_advised' => isset($request->yearly_eye_test_advised) ? 1 : 0,
                    'surgery_advised' => isset($request->surgery_advised) ? 1 : 0,
                    'review_date' => $request->review_date,
                    'notes' => $request->notes,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);

                CampPatientVision::create([
                    'camp_patient_id' => $patient->id,
                    're_vb' => $request->re_vb,
                    're_sph' => $request->re_sph,
                    're_cyl' => $request->re_cyl,
                    're_axis' => $request->re_axis,
                    're_add' => $request->re_add,
                    're_va' => $request->re_va,
                    'le_vb' => $request->le_vb,
                    'le_sph' => $request->le_sph,
                    'le_cyl' => $request->le_cyl,
                    'le_axis' => $request->le_axis,
                    'le_add' => $request->le_add,
                    'le_va' => $request->le_va,
                ]);
            });
        }catch(Exception $e){
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        };       
        return redirect()->route('camp.patients', encrypt($request->camp_id))->with('success', 'Patient has been created successfully!');
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
        $campatient = CampPatient::with('camp', 'vision')->findOrFail(decrypt($id));
        return view('backend.camp.patient.edit', compact('campatient'));
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
        try{
            DB::transaction(function() use ($request, $id) {
                CampPatient::findOrFail($id)->update([
                    'name' => $request->name,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'place' => $request->place,
                    'mobile' => $request->mobile,
                    'further_investigation_advised' => isset($request->further_investigation_advised) ? 1 : 0,
                    'galsses_advised' => isset($request->galsses_advised) ? 1 : 0,
                    'yearly_eye_test_advised' => isset($request->yearly_eye_test_advised) ? 1 : 0,
                    'surgery_advised' => isset($request->surgery_advised) ? 1 : 0,
                    'review_date' => $request->review_date,
                    'notes' => $request->notes,
                    'updated_by' => $request->user()->id,
                ]);

                CampPatientVision::findOrFail($request->camp_patient_vision_id)->update([
                    're_vb' => $request->re_vb,
                    're_sph' => $request->re_sph,
                    're_cyl' => $request->re_cyl,
                    're_axis' => $request->re_axis,
                    're_add' => $request->re_add,
                    're_va' => $request->re_va,
                    'le_vb' => $request->le_vb,
                    'le_sph' => $request->le_sph,
                    'le_cyl' => $request->le_cyl,
                    'le_axis' => $request->le_axis,
                    'le_add' => $request->le_add,
                    'le_va' => $request->le_va,
                ]);
            });
        }catch(Exception $e){
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        };
        return redirect()->route('camp.patients', encrypt(CampPatient::findOrFail($id)->camp_id))->with('success', 'Patient has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        CampPatient::findOrFail(decrypt($id))->delete();
        return redirect()->route('camp.patients', encrypt($id))->with('success', 'Patient has been deleted successfully!');
    }
}
