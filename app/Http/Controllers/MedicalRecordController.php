<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordDiagnosis;
use App\Models\MedicalRecordPharmacy;
use App\Models\MedicalRecordSymptom;
use App\Models\MedicalRecordVision;
use App\Models\Product;
use App\Models\ProductSubcategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use DB;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:medical-record-list|medical-record-create|medical-record-edit|medical-record-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:medical-record-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:medical-record-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:medical-record-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $mrecords = MedicalRecord::with('consultation')->whereDate('created_at', Carbon::today())->withTrashed()->latest()->get();
        return view('backend.medical-record.index', compact('mrecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $consultation = Consultation::with('patient', 'doctor')->findOrFail(decrypt($id));
        $mrecord = MedicalRecord::where('consultation_id', decrypt($id))->withTrashed()->first();
        $types = ProductSubcategory::where('category', 'pharmacy')->where('attribute', 'type')->orderBy('name')->pluck('name', 'id');
        $products = Product::where('category', 'pharmacy')->orderBy('name')->pluck('name', 'id');
        if (empty($mrecord)) :
            return view('backend.medical-record.create', compact('consultation', 'types', 'products'));
        else :
            return view('backend.medical-record.edit', compact('mrecord', 'consultation', 'types', 'products'));
        endif;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'symptoms' => 'required',
            'diagnosis' => 'required',
            'doctor_recommondation' => 'required',
        ]);
        $input = $request->only(array('patient_history', 'allergic_drugs', 'doctor_recommondation', 'surgery_advised', 'review_date'));
        $input['consultation_id'] = decrypt($request->consultation_id);
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $input['surgery_advised'] = isset($request->surgery_advised) ? 1 : 0;
        try {
            DB::transaction(function () use ($input, $request) {
                $mrecord = MedicalRecord::create($input);
                if ($request->symptoms) :
                    $symptoms = explode(",", $request->symptoms);
                    foreach ($symptoms as $key => $symptom) :
                        $sdata[] = [
                            'medical_record_id' => $mrecord->id,
                            'name' => $symptom,
                        ];
                    endforeach;
                endif;
                if ($request->diagnosis) :
                    $diagnosis = explode(",", $request->diagnosis);
                    foreach ($diagnosis as $key => $diagnos) :
                        $ddata[] = [
                            'medical_record_id' => $mrecord->id,
                            'name' => $diagnos,
                        ];
                    endforeach;
                endif;

                MedicalRecordSymptom::insert($sdata);
                MedicalRecordDiagnosis::insert($ddata);
                MedicalRecordVision::insert([
                    'medical_record_id' => $mrecord->id,
                    're_sph' => $request->re_sph,
                    're_cyl' => $request->re_cyl,
                    're_axis' => $request->re_axis,
                    're_add' => $request->re_add,
                    're_va' => $request->re_va,
                    're_nv' => $request->re_nv,
                    'le_sph' => $request->le_sph,
                    'le_cyl' => $request->le_cyl,
                    'le_axis' => $request->le_axis,
                    'le_add' => $request->le_add,
                    'le_va' => $request->le_va,
                    'le_nv' => $request->le_nv,
                ]);

                if ($request->product_id[0] > 0) :
                    foreach ($request->product_id as $key => $item) :
                        $mdata[] = [
                            'medical_record_id' => $mrecord->id,
                            'product_type' => $request->product_type[$key],
                            'product_id' => $item,
                            'dosage' => $request->dosage[$key],
                            'duration' => $request->duration[$key],
                            'eye' => $request->eye[$key],
                            'qty' => $request->qty[$key],
                            'notes' => $request->notes[$key],
                        ];
                    endforeach;
                    MedicalRecordPharmacy::insert($mdata);
                endif;
            });
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())->withInput($request->all());
        };
        return redirect()->route('mrecords')
            ->with('success', 'Medical Record has been created successfully');
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
        $mrecord = MedicalRecord::findOrFail(decrypt($id));
        $consultation = Consultation::with('patient', 'doctor')->findOrFail($mrecord->consultation_id);
        $types = ProductSubcategory::where('category', 'pharmacy')->where('attribute', 'type')->orderBy('name')->pluck('name', 'id');
        $products = Product::where('category', 'pharmacy')->orderBy('name')->pluck('name', 'id');
        return view('backend.medical-record.edit', compact('mrecord', 'consultation', 'types', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'symptoms' => 'required',
            'diagnosis' => 'required',
            'doctor_recommondation' => 'required',
        ]);
        $input = $request->only(array('patient_history', 'allergic_drugs', 'doctor_recommondation', 'surgery_advised', 'review_date'));
        $input['updated_by'] = $request->user()->id;
        $input['surgery_advised'] = isset($request->surgery_advised) ? 1 : 0;
        try {
            DB::transaction(function () use ($input, $request, $id) {
                $mrecord = MedicalRecord::findOrFail($id);
                $mrecord->update($input);
                if ($request->symptoms) :
                    $symptoms = explode(",", $request->symptoms);
                    foreach ($symptoms as $key => $symptom) :
                        $sdata[] = [
                            'medical_record_id' => $mrecord->id,
                            'name' => $symptom,
                        ];
                    endforeach;
                endif;
                if ($request->diagnosis) :
                    $diagnosis = explode(",", $request->diagnosis);
                    foreach ($diagnosis as $key => $diagnos) :
                        $ddata[] = [
                            'medical_record_id' => $mrecord->id,
                            'name' => $diagnos,
                        ];
                    endforeach;
                endif;
                MedicalRecordSymptom::where('medical_record_id', $id)->delete();
                MedicalRecordDiagnosis::where('medical_record_id', $id)->delete();
                MedicalRecordVision::where('medical_record_id', $id)->delete();
                MedicalRecordSymptom::insert($sdata);
                MedicalRecordDiagnosis::insert($ddata);
                MedicalRecordVision::insert([
                    'medical_record_id' => $mrecord->id,
                    're_sph' => $request->re_sph,
                    're_cyl' => $request->re_cyl,
                    're_axis' => $request->re_axis,
                    're_add' => $request->re_add,
                    're_va' => $request->re_va,
                    're_nv' => $request->re_nv,
                    'le_sph' => $request->le_sph,
                    'le_cyl' => $request->le_cyl,
                    'le_axis' => $request->le_axis,
                    'le_add' => $request->le_add,
                    'le_va' => $request->le_va,
                    'le_nv' => $request->le_nv,
                ]);

                if ($request->product_id[0] > 0) :
                    foreach ($request->product_id as $key => $item) :
                        $mdata[] = [
                            'medical_record_id' => $mrecord->id,
                            'product_type' => $request->product_type[$key],
                            'product_id' => $item,
                            'dosage' => $request->dosage[$key],
                            'duration' => $request->duration[$key],
                            'eye' => $request->eye[$key],
                            'qty' => $request->qty[$key],
                            'notes' => $request->notes[$key],
                        ];
                    endforeach;
                    MedicalRecordPharmacy::where('medical_record_id', $mrecord->id)->delete();
                    MedicalRecordPharmacy::insert($mdata);
                endif;
            });
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        };
        return redirect()->route('mrecords')
            ->with('success', 'Medical Record has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        MedicalRecord::findOrFail(decrypt($id))->delete();
        return redirect()->route('mrecords')
            ->with('success', 'Medical Record has been deleted successfully');
    }
}
