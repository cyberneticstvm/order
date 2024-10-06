<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Power;
use App\Models\Registration;
use App\Models\Spectacle;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Monolog\Registry;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $optometrists, $doctors, $powers, $mobile, $secret;
    public function __construct()
    {
        $this->middleware('permission:customer-list|customer-create|customer-edit|customer-delete', ['only' => ['index', 'store', 'spectacles']]);
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
        $this->secret = apiSecret();
    }

    public function index()
    {
        /*$customers = Spectacle::leftJoin('customers', 'spectacles.customer_id', 'customers.id')->selectRaw('customers.*, spectacles.id as specid, spectacles.created_at as stime')->where('spectacles.branch_id', Session::get('branch'))->whereDate('spectacles.created_at', Carbon::today())->latest()->get();*/
        //$customers = Customer::where('branch_id', Session::get('branch'))->whereDate('updated_at', Carbon::today())->latest()->get();
        $registrations = Registration::where('branch_id', Session::get('branch'))->whereDate('created_at', Carbon::today())->whereNull('order_id')->latest()->get();
        return view('backend.customer.index', compact('registrations'));
    }

    public function spectacles()
    {
        $spectacles = Spectacle::whereDate('created_at', Carbon::today())->where('branch_id', Session::get('branch'))->get();
        return view('backend.customer.spectacle-register', compact('spectacles'));
    }

    public function fetch(Request $request)
    {
        $this->validate($request, [
            'search_term' => 'required',
            'source' => 'required',
        ]);
        $source = $request->source;
        if ($request->source == 'hospital') :

            /*$mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', $request->search_term)->first();*/
            $val = $request->search_term;
            $url = api_url() . "/api/mrecord/" . $val . "/" . $this->secret;
            $json = file_get_contents($url);
            $data = json_decode($json);
            $mrecord = $data->mrecord;
            if ($mrecord) :
                $patient = Customer::selectRaw("name as patient_name, address, id as patient_id")->where('mrn', $mrecord->id)->latest()->first();
                if (!$patient)
                    /*$patient = DB::connection('mysql1')->table('patient_registrations')->where('id', $mrecord->patient_id)->first();*/
                    $patient = $data->patient;
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
            /*$mrecord = DB::connection('mysql1')->table('patient_medical_records')->where('id', decrypt($id))->first();
            $spectacle = DB::connection('mysql1')->table('spectacles')->where('medical_record_id', decrypt($id))->first();*/


            $val = decrypt($id);
            $url = api_url() . "/api/mrecord/" . $val . "/" . $this->secret;
            $json = file_get_contents($url);
            $data = json_decode($json);
            $mrecord = $data->mrecord;
            $spectacle = $data->spectacle;


            $patient = Customer::selectRaw("id, name as patient_name, age, address, mobile as mobile_number, alt_mobile, gstin, company_name")->where('mrn', $mrecord->id)->latest()->first();
            if (!$patient) :
                /*$patient = DB::connection('mysql1')->table('patient_registrations')->where('id', $mrecord->patient_id ?? 0)->first();*/
                $patient = $data->patient;
            else :
                $cid = $patient->id;
            endif;
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
            if (Customer::where('mobile', $request->mobile)->exists()) :
                //return redirect()->back()->with("error", "Customer with provided mobile number has already been registered.")->withInput($request->all());
                $request->session()->put('cdata', $request->input());
                return redirect()->route('customer.exists');
            else :
                DB::transaction(function () use ($request, $cid) {
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
                    endif;
                    Registration::create([
                        'customer_id' => $cid,
                        'branch_id' => Session::get('branch'),
                    ]);
                });
            endif;
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('customer.register')->with("success", "Prescription registered successfully!");
    }

    public function existing()
    {
        $data = Session::get('cdata');
        $customers = Customer::where('mobile', $data['mobile'])->withTrashed()->get();
        return view('backend.customer.existing', compact('customers'));
    }

    public function existingproceed(Request $request)
    {
        try {
            $cid = $request->rad;
            if ($cid != '') :
                if ($cid == 0) :
                    $data = Session::get('cdata');
                    $customer = Customer::create([
                        'name' => $data['name'],
                        'age' => $data['age'],
                        'address' => $data['address'],
                        'mobile' => ($data['mobile']) ?? $this->mobile,
                        'alt_mobile' => $data['alt_mobile'],
                        'gstin' => $data['gstin'],
                        'company_name' => $data['company_name'],
                        'branch_id' => Session::get('branch'),
                        'mrn' => $data['mrn'],
                    ]);
                    $cid = $customer->id;
                endif;
                Registration::create([
                    'customer_id' => $cid,
                    'branch_id' => Session::get('branch'),
                ]);
                Session::forget('cdata');
            else :
                return redirect()->back()->with("error", "Please select a record");
            endif;
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('customer.register')->with("success", "Customer Registered successfully!");
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
        /*Customer::findOrFail(decrypt($id))->delete();*/
        Registration::findOrFail(decrypt($id))->delete();
        return redirect()->route('customer.register')->with("success", "Customer deleted successfully!");
    }


    public function editSpectacle(string $id, string $type)
    {
        $optometrists = $this->optometrists;
        $doctors = $this->doctors;
        $powers = $this->powers;
        if ($type == 'registration') :
            $registration = Registration::findOrFail(decrypt($id));
            $customer = Customer::findOrFail($registration->customer_id);
            $spectacle = Spectacle::where('registration_id', $registration->id)->first();
        else :
            $spectacle = Spectacle::findOrFail(decrypt($id));
            $registration = Registration::findOrFail($spectacle->registration_id);
            $customer = Customer::findOrFail($spectacle->customer_id);
        endif;

        $store_prescriptions = Spectacle::where('customer_id', $registration->customer_id)->selectRaw("CONCAT_WS(' / ', 'OID', order_id, DATE_FORMAT(created_at, '%d/%b/%Y')) AS oid, id")->get();

        /*$hospital_prescriptions = DB::connection('mysql1')->table('spectacles')->selectRaw("CONCAT_WS(' / ', 'MRN', medical_record_id, DATE_FORMAT(created_at, '%d/%b/%Y')) AS mrn, id")->where('medical_record_id', $customer->mrn)->get();*/



        $secret = apiSecret();
        $mrn = $customer->mrn;
        $url = api_url() . "/api/mrecord/" . $mrn . "/" . $secret;
        $json = file_get_contents($url);
        $data = json_decode($json);
        $hospital_prescriptions = $data->prescription;

        if (!$spectacle) :
            $spectacle = Spectacle::where('customer_id', $registration->customer_id)->latest()->first();
        endif;
        return view('backend.customer.spectacle', compact('spectacle', 'customer', 'doctors', 'optometrists', 'powers', 'registration', 'store_prescriptions', 'hospital_prescriptions'));
    }

    public function updateSpectacle(Request $request, string $id)
    {
        $spectacle = Spectacle::find($id);
        $input = $request->all();
        if ($spectacle) :
            $input['updated_by'] = $request->user()->id;
            $spectacle->update($input);
        else :
            $input['created_by'] = $request->user()->id;
            $input['updated_by'] = $request->user()->id;
            $input['branch_id'] = Session::get('branch');
            Spectacle::create($input);
        endif;
        return redirect()->route('customer.register')->with("success", "Spectacle prescription updated successfully!");
    }
}
