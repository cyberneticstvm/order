<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Power;
use App\Models\Spectacle;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $optometrists, $doctors, $powers, $mobile;
    public function __construct()
    {
        $this->middleware('permission:customer-list|customer-create|customer-edit|customer-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:customer-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:customer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customer-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {

            $this->optometrists = User::role('Optometrist')->pluck('name', 'id');
            $this->doctors = User::role('Doctor')->pluck('name', 'id');
            $this->powers = Power::all();
            $this->mobile = '8547311622';

            return $next($request);
        });
    }

    public function index()
    {
        $customers = Spectacle::leftJoin('customers', 'spectacles.customer_id', 'customers.id')->selectRaw('customers.*, spectacles.id as specid, spectacles.created_at as stime')->where('spectacles.branch_id', Session::get('branch'))->whereDate('spectacles.created_at', Carbon::today())->latest()->get();
        //$customers = Customer::where('branch_id', Session::get('branch'))->whereDate('updated_at', Carbon::today())->latest()->get();
        return view('backend.customer.index', compact('customers'));
    }

    public function fetch(Request $request)
    {
        $this->validate($request, [
            'search_term' => 'required',
            'source' => 'required',
        ]);
        $source = $request->source;
        if ($request->source == 'hospital') :
            $mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', $request->search_term)->first();
            if ($mrecord) :
                $patient = DB::connection('mysql1')->table('patient_registrations')->where('id', $mrecord->patient_id)->first();
                return view('backend.customer.proceed', compact('mrecord', 'patient', 'source'));
            else :
                return redirect()->back()->with('error', 'No records found')->withInput($request->all());
            endif;
        else :
            $patient = Customer::selectRaw("name as patient_name, address, id as patient_id")->where('id', $request->search_term)->firstOrFail();
            $mrecord = [];
            return view('backend.customer.proceed', compact('mrecord', 'patient', 'source'));
        endif;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id, $source)
    {
        $optometrists = $this->optometrists;
        $doctors = $this->doctors;
        $powers = $this->powers;
        $mrecord = [];
        $patient = [];
        $spectacle = [];
        $cid = 0;
        if ($source == 'hospital') :
            $mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', decrypt($id))->first();
            $spectacle = DB::connection('mysql1')->table('spectacles')->where('medical_record_id', decrypt($id))->first();
            $patient = DB::connection('mysql1')->table('patient_registrations')->where('id', $mrecord->patient_id ?? 0)->first();
        elseif ($source == 'store') :
            $patient = Customer::selectRaw("id, name as patient_name, age, address, mobile as mobile_number, alt_mobile, gstin, company_name")->where('id', decrypt($id))->firstOrFail();
            $cid = $patient->id;
            $spectacle = Spectacle::where('customer_id', $patient->id)->latest()->first();
        endif;
        return view('backend.customer.create', compact('optometrists', 'doctors', 'powers', 'mrecord', 'spectacle', 'patient', 'source', 'cid'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $cid = $request->cid;
        try {
            if ($request->cid == 0) :
                $customer = Customer::create([
                    'name' => $request->name,
                    'age' => $request->age,
                    'address' => $request->address,
                    'mobile' => ($request->mobile) ?? $this->mobile,
                    'alt_mobile' => $request->alt_mobile,
                    'gstin' => $request->gstin,
                    'company_name' => $request->company_name,
                    'branch_id' => Session::get('branch'),
                    'mrn' => $request->mrn,
                ]);
                $cid = $customer->id;
            else :
                Customer::findOrFail($request->cid)->update([
                    'name' => $request->name,
                    'age' => $request->age,
                    'address' => $request->address,
                    'mobile' => ($request->mobile) ?? $this->mobile,
                    'alt_mobile' => $request->alt_mobile,
                    'gstin' => $request->gstin,
                    'company_name' => $request->company_name,
                    'updated_at' => Carbon::now(),
                ]);
            endif;
            if (!$request->rx) :
                Spectacle::create([
                    'customer_id' => $cid,
                    're_sph' => $request->re_sph,
                    're_cyl' => $request->re_cyl,
                    're_axis' => $request->re_axis,
                    're_add' => $request->re_add,
                    're_va' => $request->re_va,
                    're_pd' => $request->re_pd,
                    're_int_add' => $request->re_int_add,
                    'le_sph' => $request->le_sph,
                    'le_cyl' => $request->le_cyl,
                    'le_axis' => $request->le_axis,
                    'le_add' => $request->le_add,
                    'le_va' => $request->le_va,
                    'le_pd' => $request->le_pd,
                    'le_int_add' => $request->le_int_add,
                    'a_size' => $request->a_size,
                    'b_size' => $request->b_size,
                    'dbl' => $request->dbl,
                    'fh' => $request->fh,
                    'ed' => $request->ed,
                    'vd' => $request->vd,
                    'w_angle' => $request->w_angle,
                    'doctor' => $request->doctor,
                    'optometrist' => $request->optometrist,
                    'branch_id' => Session::get('branch'),
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
            endif;
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('customer.register')->with("success", "Prescription saved successfully!");
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
        $customer = Customer::findOrFail(decrypt($id));
        return view('backend.customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        Customer::findOrFail($id)->update($input);
        return redirect()->route('customer.register')->with("success", "Customer updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Customer::findOrFail(decrypt($id))->delete();
        return redirect()->route('customer.register')->with("success", "Customer deleted successfully!");
    }

    public function editSpectacle(string $id)
    {
        $optometrists = $this->optometrists;
        $doctors = $this->doctors;
        $powers = $this->powers;
        $spectacle = Spectacle::where('customer_id', decrypt($id))->whereDate('created_at', Carbon::today())->where('branch_id', Session::get('branch'))->firstOrFail();
        $customer = Customer::findOrFail(decrypt($id));
        return view('backend.customer.spectacle', compact('spectacle', 'customer', 'doctors', 'optometrists', 'powers'));
    }

    public function updateSpectacle(Request $request, string $id)
    {
        $spectacle = Spectacle::findOrFail($id);
        $ocount = Order::where('customer_id', $spectacle->customer_id)->whereDate('created_at', Carbon::today())->count('id');
        if ($ocount == 1) :
            return redirect()->back()->with("error", "Cant update the prescription since the Order has already been placed.")->withInput($request->all());
        else :
            $input = $request->all();
            $input['updated_by'] = $request->user()->id;
            $spectacle->update($input);
        endif;
        return redirect()->route('customer.register')->with("success", "Spectacle prescription updated successfully!");
    }
}
