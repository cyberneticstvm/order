<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $products;

    function __construct()
    {
        $this->middleware('permission:product-service-list|product-service-create|product-service-edit|product-service-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-service-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-service-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-service-delete', ['only' => ['destroy']]);

        $this->products = Product::withTrashed()->where('category', 'service')->orderBy('name')->get();
    }
    public function index()
    {
        $products = $this->products;
        return view('backend.product.service.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.product.service.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'selling_price' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $input['category'] = 'service';
        Product::create($input);
        return redirect()->route('product.service')->with("success", "Product created successfully!");
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
        $product = Product::findOrFail(decrypt($id));
        return view('backend.product.service.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'selling_price' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        Product::findOrFail($id)->update($input);
        return redirect()->route('product.service')->with("success", "Product updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::findOrFail(decrypt($id))->delete();
        return redirect()->route('product.service')->with("success", "Product deleted successfully!");
    }
}
