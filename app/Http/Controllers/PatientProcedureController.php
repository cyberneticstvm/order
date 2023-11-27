<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\PatientProcedure;
use App\Models\PatientProcedureDetail;
use App\Models\Procedure;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PatientProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $procedures, $procs;

    function __construct()
    {
        $this->middleware('permission:patient-procedure-list|patient-procedure-create|patient-procedure-edit|patient-procedure-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:patient-procedure-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:patient-procedure-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:patient-procedure-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {
            $this->procedures = PatientProcedure::when(Auth::user()->roles->first()->id != 1, function ($q) {
                return $q->where('branch_id', Session::get('branch'));
            })->whereDate('created_at', Carbon::today())->withTrashed()->latest()->get();
            return $next($request);
        });

        $this->procs = Procedure::orderBy('name')->pluck('name', 'id');
    }

    public function index()
    {
        $procedures = $this->procedures;
        return view('backend.consultation.procedure.index', compact('procedures'));
    }

    public function fetch(Request $request)
    {
        $this->validate($request, [
            'medical_record_number' => 'required',
        ]);
        $consultation = Consultation::with('patient')->findOrFail($request->medical_record_number);
        return view('backend.consultation.procedure.proceed', compact('consultation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $consultation = Consultation::with('patient')->find(decrypt($id));
        $procs = $this->procs;
        return view('backend.consultation.procedure.create', compact('consultation', 'procs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'findings' => 'required',
            'procedures' => 'required',
        ]);
        try {
            DB::transaction(function () use ($request) {
                $proc = PatientProcedure::create([
                    'consultation_id' => $request->consultation_id,
                    'patient_id' => $request->patient_id,
                    'branch_id' => $request->branch_id,
                    'findings' => $request->findings,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
                foreach ($request->procedures as $key => $item) :
                    $procedure = Procedure::findOrFail($item);
                    $data[] = [
                        'patient_procedure_id' => $proc->id,
                        'procedure_id' => $procedure->id,
                        'fee' => $procedure->fee,
                    ];
                endforeach;
                PatientProcedureDetail::insert($data);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('patient.procedures')->with("success", "Procedure for the patient created successfully!");
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
        $procedure = PatientProcedure::with('patient')->findOrFail(decrypt($id));
        $procs = $this->procs;
        return view('backend.consultation.procedure.edit', compact('procedure', 'procs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'findings' => 'required',
            'procedures' => 'required',
        ]);
        try {
            DB::transaction(function () use ($request, $id) {
                PatientProcedure::findOrFail($id)->update([
                    'findings' => $request->findings,
                    'updated_by' => $request->user()->id,
                ]);
                foreach ($request->procedures as $key => $item) :
                    $procedure = Procedure::findOrFail($item);
                    $data[] = [
                        'patient_procedure_id' => $id,
                        'procedure_id' => $procedure->id,
                        'fee' => $procedure->fee,
                    ];
                endforeach;
                PatientProcedureDetail::where('patient_procedure_id', $id)->delete();
                PatientProcedureDetail::insert($data);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('patient.procedures')->with("success", "Procedure for the patient updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        PatientProcedure::findOrFail(decrypt($id))->delete();
        return redirect()->route('patient.procedures')
            ->with('success', 'Procedure for the patient deleted successfully');
    }
}
