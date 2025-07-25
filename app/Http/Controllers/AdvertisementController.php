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
        $this->middleware('permission:fetch-vehicle-for-payment', ['only' => ['vehicleFetchForPayment', 'vehicleFetchForPaymentUpdate']]);
    }

    function index($status)
    {
        $vehicles = Vehicle::where('branch_id', Session::get('branch'))->withTrashed()->latest()->get();
        return view('backend.ads.vehicle.index', compact('vehicles', 'status'));
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
            'place' => 'required',
            'contact_number' => 'required|numeric|digits:10',
            'card_issued' => 'required',
        ]);
        try {
            $input = $request->all();
            $input['vcode'] = uniqueCode(Vehicle::class, 'vcode', '', 1000, 9999);
            $input['fee'] = 0;
            $input['payment_terms'] = 0;
            $input['branch_id'] = Session::get('branch');
            $input['branch_id'] = Session::get('branch');
            $input['created_by'] = $request->user()->id;
            $input['updated_by'] = $request->user()->id;
            Vehicle::create($input);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('vehicles', 'Active')->with("success", "Vehicle created successfully");
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
            'place' => 'required',
            'contact_number' => 'required|numeric|digits:10',
            'card_issued' => 'required',
        ]);
        try {
            $input = $request->all();
            $input['updated_by'] = $request->user()->id;
            Vehicle::findOrFail($id)->update($input);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }

        return redirect()->route('vehicles', 'Active')->with("success", "Vehicle updated successfully");
    }

    function destroy(string $id)
    {
        Vehicle::findOrFail(decrypt($id))->delete();
        return redirect()->route('vehicles', 'Active')->with("success", "Vehicle deleted successfully");
    }

    function restore(string $id)
    {
        Vehicle::withTrashed()->where('id', decrypt($id))->restore();
        return redirect()->route('vehicles', 'Active')->with("success", "Vehicle restored successfully");
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
        $days = $vehicle->daysLeft();
        $fee = $vehicle->fee;
        if ($days < 0) {
            $days = abs($days);
            $fee = number_format($fee + ($fee / 30) * $days, 2);
        }
        return view('backend.ads.vehicle.payment', compact('vehicle', 'diff', 'payments', 'pmodes', 'fee'));
    }

    function paymentSave(Request $request, string $id)
    {
        $this->validate($request, [
            'amount' => 'required',
            'payment_mode' => 'required',
        ]);
        /*$vehicle = Vehicle::findOrFail($id);
        $payment = VehiclePayment::where('vehicle_id', $vehicle->id)->latest()->first();
        $diff = Carbon::now()->diffInDays(Carbon::parse($payment?->created_at));
        if ($payment && $diff < $vehicle->payment_terms && $vehicle->daysLeft() > 0):
            return redirect()->back()->with("error", $vehicle->payment_terms - $diff . " days left to make the next payment");
        endif;*/
        $vehicle = Vehicle::findOrFail($id);
        $input = $request->except(array('upi_id', 'owner_name', 'contact_number', 'reg_number'));
        $input['vehicle_id'] = $id;
        $input['branch_id'] = Session::get('branch');
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        VehiclePayment::create($input);
        if (!$vehicle->upi_id && $request->upi_id)
            $vehicle->update([
                'upi_id' => $request->upi_id,
            ]);
        if ($vehicle->owner_name != $request->owner_name)
            $vehicle->update([
                'owner_name' => $request->owner_name,
            ]);
        if ($vehicle->contact_number != $request->contact_number)
            $vehicle->update([
                'contact_number' => $request->contact_number,
            ]);
        if ($vehicle->reg_number != $request->reg_number)
            $vehicle->update([
                'reg_number' => $request->reg_number,
            ]);

        return redirect()->route('vehicles', 'Active')->with("success", "Payment recorded successfully");
    }

    function paymentDelete(string $id)
    {
        VehiclePayment::findOrFail(decrypt($id))->delete();
        return redirect()->route('vehicles', 'Inactive')->with("success", "Payment deleted successfully");
    }

    function vehicleFetchForPayment()
    {
        $vehicles = collect();
        return view('backend.ads.vehicle.fetch', compact('vehicles'));
    }

    function vehicleFetchForPaymentUpdate(Request $request)
    {
        $this->validate($request, [
            'search_term' => 'required',
        ]);
        try {
            $inputs = array($request->search_term);
            $vehicles = Vehicle::where('vcode', $request->search_term)->orWhere('reg_number', $request->search_term)->orWhere('contact_number', $request->search_term)->get();
            if ($vehicles->isEmpty()):
                throw new Exception("No records found!");
            endif;
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return view('backend.ads.vehicle.fetch', compact('vehicles', 'inputs'));
    }
}
