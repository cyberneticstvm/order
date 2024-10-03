<?php

namespace App\Http\Controllers;

use App\Models\PaymentMode;
use App\Models\Vehicle;
use App\Models\VehiclePayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdvertisementController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:vehicle-list|vehicle-create|vehicle-edit|vehicle-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:vehicle-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:vehicle-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:vehicle-delete', ['only' => ['destroy']]);
        $this->middleware('permission:vehicle-payment-create', ['only' => ['payment', 'paymentSave']]);
        $this->middleware('permission:vehicle-payment-delete', ['only' => ['paymentDelete']]);
    }

    function index()
    {
        $vehicles = Vehicle::where('branch_id', Session::get('branch'))->withTrashed()->latest()->get();
        return view('backend.ads.vehicle.index', compact('vehicles'));
    }

    function create()
    {
        return view('backend.ads.vehicle.create');
    }

    function store(Request $request)
    {
        $this->validate($request, [
            'owner_name' => 'required',
            'reg_number' => 'required|unique:vehicles,reg_number',
            'contact_number' => 'required|numeric|digits:10',
            'fee' => 'required',
            'payment_terms' => 'required',
        ]);
        $input = $request->all();
        $input['branch_id'] = Session::get('branch');
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        Vehicle::create($input);
        return redirect()->route('vehicles')->with("success", "Vehicle created successfully");
    }

    public function edit(string $id)
    {
        $vehicle = Vehicle::findOrFail(decrypt($id));
        return view('backend.ads.vehicle.edit', compact('vehicle'));
    }

    function update(Request $request, string $id)
    {
        $this->validate($request, [
            'owner_name' => 'required',
            'reg_number' => 'required|unique:vehicles,reg_number,' . $id,
            'contact_number' => 'required|numeric|digits:10',
            'fee' => 'required',
            'payment_terms' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        Vehicle::findOrFail($id)->update($input);
        return redirect()->route('vehicles')->with("success", "Vehicle updated successfully");
    }

    function destroy(string $id)
    {
        Vehicle::findOrFail(decrypt($id))->delete();
        return redirect()->route('vehicles')->with("success", "Vehicle deleted successfully");
    }

    function payment(string $id)
    {
        $vehicle = Vehicle::findOrFail(decrypt($id));
        $payment = VehiclePayment::where('vehicle_id', $vehicle->id)->latest()->first();
        $diff = Carbon::now()->diffInDays(Carbon::parse($payment?->created_at));
        /*if ($payment && $diff < $vehicle->payment_terms):
            return redirect()->back()->with("error", $vehicle->payment_terms - $diff . " days left to make the next payment");
        endif;*/
        $payments = VehiclePayment::withTrashed()->where('vehicle_id', $vehicle->id)->latest()->get();
        $pmodes = PaymentMode::orderBy('name')->get();
        return view('backend.ads.vehicle.payment', compact('vehicle', 'diff', 'payments', 'pmodes'));
    }

    function paymentSave(Request $request, string $id)
    {
        $this->validate($request, [
            'amount' => 'required',
            'payment_mode' => 'required',
        ]);
        $vehicle = Vehicle::findOrFail($id);
        $payment = VehiclePayment::where('vehicle_id', $vehicle->id)->latest()->first();
        $diff = Carbon::now()->diffInDays(Carbon::parse($payment?->created_at));
        if ($payment && $diff < $vehicle->payment_terms):
            return redirect()->back()->with("error", $vehicle->payment_terms - $diff . " days left to make the next payment");
        endif;
        $input = $request->all();
        $input['vehicle_id'] = $id;
        $input['branch_id'] = Session::get('branch');
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        VehiclePayment::create($input);
        return redirect()->route('vehicles')->with("success", "Payment recorded successfully");
    }

    function paymentDelete(string $id)
    {
        VehiclePayment::findOrFail(decrypt($id))->delete();
        return redirect()->route('vehicles')->with("success", "Payment deleted successfully");
    }

    function fetchVehicle()
    {
        return view("backend.extras.fetch-vehicle");
    }

    function fetchVehicleDetails(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required|numeric|digits:10',
        ]);
    }
}
