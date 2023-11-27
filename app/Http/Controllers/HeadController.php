<?php

namespace App\Http\Controllers;

use App\Models\Head;
use Illuminate\Http\Request;

class HeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:head-list|head-create|head-edit|head-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:head-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:head-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:head-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $heads = Head::withTrashed()->latest()->get();
        return view('backend.head.index', compact('heads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.head.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'category' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        Head::create($input);
        return redirect()->route('heads')
            ->with('success', 'Head has been created successfully');
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
        $head = Head::findOrFail(decrypt($id));
        return view('backend.head.edit', compact('head'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'category' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        Head::findOrFail($id)->update($input);
        return redirect()->route('heads')
            ->with('success', 'Head has been created successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Head::findOrFail(decrypt($id))->delete();
        return redirect()->route('heads')
            ->with('success', 'Head has been deleted successfully');
    }
}
