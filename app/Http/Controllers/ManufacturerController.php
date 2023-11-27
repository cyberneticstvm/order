<?php

namespace App\Http\Controllers;

use App\Models\Manufaturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct(){
        $this->middleware('permission:manufacturer-list|manufacturer-create|manufacturer-edit|manufacturer-delete', ['only' => ['index','store']]);
        $this->middleware('permission:manufacturer-create', ['only' => ['create','store']]);
        $this->middleware('permission:manufacturer-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:manufacturer-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $manufacturers = Manufaturer::withTrashed()->orderBy('name', 'ASC')->get();
        return view('backend.manufacturer.index', compact('manufacturers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.manufacturer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
         ]);
         $input = $request->all();
         $input['created_by'] = $request->user()->id;
         $input['updated_by'] = $request->user()->id;
         Manufaturer::create($input);
         return redirect()->route('manufacturers')->with("success", "Manufacturer created successfully!");
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
        $manufacturer = Manufaturer::findOrFail(decrypt($id));
        return view('backend.manufacturer.edit', compact('manufacturer'));
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
         Manufaturer::findOrFail($id)->update($input);
         return redirect()->route('manufacturers')->with("success", "Manufacturer updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Manufaturer::findOrFail(decrypt($id))->delete();
        return redirect()->route('manufacturers')->with("success", "Manufacturer deleted successfully!");
    }
}
