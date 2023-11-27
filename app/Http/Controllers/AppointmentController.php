<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct(){
        $this->middleware('permission:appointment-list|appointment-create|appointment-edit|appointment-delete', ['only' => ['index','store']]);
        $this->middleware('permission:appointment-create', ['only' => ['create','store']]);
        $this->middleware('permission:appointment-todays-list', ['only' => ['show']]);
        $this->middleware('permission:appointment-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:appointment-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $appointments = Appointment::whereDate('created_at', Carbon::today())->whereNull('patient_id')->withTrashed()->latest()->get();
        return view('backend.appointment.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doctors = Doctor::pluck('name', 'id');
        $branches = Branch::pluck('name', 'id');
        return view('backend.appointment.create', compact('doctors', 'branches'));
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
            'branch_id' => 'required',
            'doctor_id' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        Appointment::create($input);
        return redirect()->route('appointments')->with('success', 'Appointment has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $appointments = Appointment::whereDate('date', Carbon::today())->where('branch_id', Session::get('branch'))->whereNull('patient_id')->withTrashed()->latest()->get();
        return view('backend.appointment.list', compact('appointments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, User $user)
    {
        $appointment = Appointment::findOrFail(decrypt($id));
        if (! Gate::allows('edit-delete-appointment', [$branch = branch(), $appointment])) {
            return redirect()->back()->with('error', 'Appointment created for another branch could not be updated!');
        }
        $doctors = Doctor::pluck('name', 'id');
        $branches = Branch::pluck('name', 'id');        
        $times = collect(getAppointmentTimeList($appointment->date, $appointment->doctor_id, $appointment->branch_id));
        return view('backend.appointment.edit', compact('doctors', 'branches', 'appointment', 'times'));
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
            'branch_id' => 'required',
            'doctor_id' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);
        $appointment = Appointment::findOrFail($id);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        $appointment->update($input);
        return redirect()->route('appointments')->with('success', 'Appointment has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::findOrFail(decrypt($id));
        if (! Gate::allows('edit-delete-appointment', [$branch = branch(), $appointment])) {
            return redirect()->back()->with('error', 'Appointment created for another branch could not be deleted!');
        }
        $appointment->delete();
        return redirect()->route('appointments')->with('success', 'Appointment has been deleted successfully!');
    }
}
