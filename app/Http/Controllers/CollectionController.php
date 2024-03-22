<?php

namespace App\Http\Controllers;

use App\Models\ProductSubcategory;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:collection-list|collection-create|collection-edit|collection-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:collection-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:collection-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:collection-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $collections = ProductSubcategory::all();
        return view('backend.collection.index', compact('collections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.collection.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        ProductSubcategory::insert([
            'name' => $request->name,
            'category' => $request->category,
            'attribute' => $request->attribute,
        ]);
        return redirect()->route('collections')
            ->with('success', 'Collection has been created successfully');
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
        $collection = ProductSubcategory::findOrFail(decrypt($id));
        return view('backend.collection.edit', compact('collection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        ProductSubcategory::findOrFail($id)->update([
            'name' => $request->name,
            'category' => $request->category,
            'attribute' => $request->attribute,
        ]);
        return redirect()->route('collections')
            ->with('success', 'Collection has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ProductSubcategory::findOrFail($id)->delete();
        return redirect()->route('collections')
            ->with('success', 'Collection has been deleted successfully');
    }
}
