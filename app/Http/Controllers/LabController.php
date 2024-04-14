<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use Illuminate\Http\Request;

class LabController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:lab-list|lab-create|lab-edit|lab-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:lab-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:lab-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:lab-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $labs = Lab::withTrashed()->get();
        return view('backend.lab.index', compact('labs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.lab.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:labs,name',
        ]);
        Lab::create([
            'name' => $request->name,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        return redirect()->route('labs')->with("success", "Lab created successfully");
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
        $lab = Lab::findOrFail(decrypt($id));
        return view('backend.lab.edit', compact('lab'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:labs,name,' . $id,
        ]);
        Lab::findOrFail($id)->update([
            'name' => $request->name,
        ]);
        return redirect()->route('labs')->with("success", "Lab created successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Lab::findOrFail(decrypt($id))->delete();
        return redirect()->route('labs')->with("success", "Lab deleted successfully");
    }
}
