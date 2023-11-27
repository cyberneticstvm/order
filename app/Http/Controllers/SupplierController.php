<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct(){
        $this->middleware('permission:supplier-list|supplier-create|supplier-edit|supplier-delete', ['only' => ['index','store']]);
        $this->middleware('permission:supplier-create', ['only' => ['create','store']]);
        $this->middleware('permission:supplier-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:supplier-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $suppliers = Supplier::withTrashed()->orderBy('name', 'ASC')->get();
        return view('backend.supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
           'name' => 'required',
           'contact_person' => 'required',
           'email' => 'required|email:rfc,dns,filter',
           'mobile' => 'required|numeric|digits:10',
           'address' => 'required',
           'expiry_notification' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        Supplier::create($input);
        return redirect()->route('suppliers')->with("success", "Supplier created successfully!");
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
        $supplier = Supplier::findOrFail(decrypt($id));
        return view('backend.supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'contact_person' => 'required',
            'email' => 'required|email:rfc,dns,filter',
            'mobile' => 'required|numeric|digits:10',
            'address' => 'required',
            'expiry_notification' => 'required',
         ]);
         $input = $request->all();
         $input['updated_by'] = $request->user()->id;
         Supplier::findOrFail($id)->update($input);
         return redirect()->route('suppliers')->with("success", "Supplier updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Supplier::findOrFail(decrypt($id))->delete();
        return redirect()->route('suppliers')->with("success", "Supplier deleted successfully!");
    }
}
