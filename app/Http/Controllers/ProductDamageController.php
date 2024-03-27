<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductDamageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:product-damage-list|product-damage-create|product-damage-edit|product-damage-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-damage-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-damage-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-damage-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
