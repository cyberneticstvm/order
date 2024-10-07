<?php

namespace App\Http\Controllers;

use App\Models\BranchOpto;
use App\Models\Customer;
use App\Models\Power;
use App\Models\Registration;
use App\Models\Spectacle;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SpectacleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $optometrists, $doctors, $powers;

    function __construct()
    {
        $this->middleware('permission:spectacle-list|spectacle-create|spectacle-edit|spectacle-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:spectacle-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:spectacle-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:spectacle-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {

            //$this->optometrists = User::role('Optometrist')->pluck('name', 'id');
            /*$this->optometrists = User::leftJoin('user_branches as ub', 'users.id', 'ub.user_id')->select('users.id', 'users.name')->where('ub.branch_id', Session::get('branch'))->role('Optometrist')->pluck('name', 'id');
            $this->doctors = User::role('Doctor')->pluck('name', 'id');*/
            $this->optometrists = BranchOpto::leftJoin('users AS u', 'u.id', 'branch_optos.user_id')->where('branch_optos.branch_id', Session::get('branch'))->where('branch_optos.designation', 'Optometrist')->pluck('u.name', 'u.id');
            $this->doctors = BranchOpto::leftJoin('users AS u', 'u.id', 'branch_optos.user_id')->where('branch_optos.branch_id', Session::get('branch'))->where('branch_optos.designation', 'Doctor')->pluck('u.name', 'u.id');
            $this->powers = Power::all();

            return $next($request);
        });
    }

    public function index()
    {
        $spectacles = Spectacle::whereDate('created_at', Carbon::today())->when(!in_array(Auth::user()->roles->first()->name, ['Administrator']), function ($q) {
            return $q->where('branch_id', Session::get('branch'));
        })->withTrashed()->latest()->get();
        return view('backend.spectacle.index', compact('spectacles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id, string $type)
    {
        $registration = Registration::findOrFail(decrypt($id));
        $customer = Customer::findOrFail($registration->customer_id);
        $optometrists = $this->optometrists;
        $doctors = $this->doctors;
        $powers = $this->powers;
        $spectacle = [];
        $mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', $customer->mrn)->first();
        $mrns = DB::connection('mysql1')->table('patient_medical_records')->where('patient_id', $mrecord->patient_id ?? 0)->pluck('id');
        $hospital_prescriptions = DB::connection('mysql1')->table('spectacles')->selectRaw("CONCAT_WS(' / ', 'MRN', medical_record_id, DATE_FORMAT(created_at, '%d/%b/%Y')) AS mrn, id")->whereIn('medical_record_id', $mrns)->get();
        /*try {
            $secret = apiSecret();
            $mrn = $customer->mrn;
            $url = api_url() . "/api/mrecord/" . $mrn . "/" . $secret;
            $json = file_get_contents($url);
            $data = json_decode($json);
            $hospital_prescriptions = $data->prescription;
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }*/
        return view('backend.spectacle.create', compact('registration', 'optometrists', 'doctors', 'powers', 'spectacle', 'hospital_prescriptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'registration_id' => 'required',
        ]);
        $input = $request->except(array('spectacle_id'));
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $input['branch_id'] = Session::get('branch');
        Spectacle::create($input);
        return redirect()->route('spectacles')->with("success", "Spectacle prescription recorded successfully");
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
        $spectacle = Spectacle::findOrFail(decrypt($id));
        $optometrists = $this->optometrists;
        $doctors = $this->doctors;
        $powers = $this->powers;
        return view('backend.spectacle.edit', compact('spectacle', 'optometrists', 'doctors', 'powers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        /*$this->validate($request, [
            'optometrist' => 'required',
        ]);*/
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        Spectacle::findOrFail($id)->update($input);
        return redirect()->route('spectacles')->with("success", "Spectacle prescription updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Spectacle::findOrFail(decrypt($id))->delete();
        return redirect()->route('spectacles')->with("success", "Spectacle prescription deleted successfully");
    }
}
