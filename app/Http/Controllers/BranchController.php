<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct(){
        $this->middleware('permission:branch-list|branch-create|branch-edit|branch-delete', ['only' => ['index','store']]);
        $this->middleware('permission:branch-create', ['only' => ['create','store']]);
        $this->middleware('permission:branch-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:branch-delete', ['only' => ['destroy']]);
   }

    public function index()
    {
        $branches = Branch::withTrashed()->get();
        return view('backend.branch.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(Branch::count() < settings()->branch_limit || settings()->branch_limit == 0)
            return view('backend.branch.create');
        return redirect()->route('branches')
            ->with('error','Allowed branch limit reached!');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:branches,name',
            'code' => 'required|unique:branches,name',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email:rfc,dns,filter',
            'address' => 'required',
            'gstin' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        Branch::create($input);
        return redirect()->route('branches')
                        ->with('success','Branch created successfully');
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
        $branch = Branch::findOrFail(decrypt($id));
        return view('backend.branch.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:branches,name,'.$id,
            'code' => 'required|unique:branches,name,'.$id,
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email:rfc,dns,filter',
            'address' => 'required',
            'gstin' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        $branch = Branch::findOrFail($id);
        $branch->update($input);
        return redirect()->route('branches')
                        ->with('success','Branch updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Branch::findOrFail(decrypt($id))->delete();
        return redirect()->route('branches')
                        ->with('success','Branch deleted successfully');
    }
}
