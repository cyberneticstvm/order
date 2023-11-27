<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use Illuminate\Http\Request;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $procedures;

    function __construct()
    {
        $this->middleware('permission:procedure-list|procedure-create|procedure-edit|procedure-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:procedure-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:procedure-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:procedure-delete', ['only' => ['destroy']]);

        $this->procedures = Procedure::withTrashed()->orderBy('name')->get();
    }

    public function index()
    {
        $procedures = $this->procedures;
        return view('backend.procedure.index', compact('procedures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.procedure.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'fee' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        Procedure::create($input);
        return redirect()->route('procedures')->with("success", "Procedure created successfully!");
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
        $procedure = Procedure::findOrFail(decrypt($id));
        return view('backend.procedure.edit', compact('procedure'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'fee' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        Procedure::findOrFail($id)->update($input);
        return redirect()->route('procedures')->with("success", "Procedure updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Procedure::findOrFail(decrypt($id))->delete();
        return redirect()->route('procedures')->with("success", "Procedure deleted successfully!");
    }
}
