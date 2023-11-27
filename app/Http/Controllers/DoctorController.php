<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:doctor-list|doctor-create|doctor-edit|doctor-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:doctor-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:doctor-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:doctor-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $doctors = Doctor::withTrashed()->latest()->get();
        return view('backend.doctor.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.doctor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required|unique:doctors,code',
            'email' => 'required|email:rfc,dns,filter|unique:doctors,email',
            'mobile' => 'required|numeric|digits:10',
            'fee' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        Doctor::create($input);
        return redirect()->route('doctors')
            ->with('success', 'Doctor has been created successfully');
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
        $doctor = Doctor::findOrFail(decrypt($id));
        return view('backend.doctor.edit', compact('doctor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required|unique:doctors,code,' . $id,
            'email' => 'required|email:rfc,dns,filter|unique:doctors,email,' . $id,
            'mobile' => 'required|numeric|digits:10',
            'fee' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        $doc = Doctor::findOrFail($id);
        $doc->update($input);
        return redirect()->route('doctors')
            ->with('success', 'Doctor has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Doctor::findOrFail(decrypt($id))->delete();
        return redirect()->route('doctors')
            ->with('success', 'Doctor has been deleted successfully');
    }
}
