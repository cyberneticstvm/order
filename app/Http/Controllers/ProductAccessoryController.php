<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;

class ProductAccessoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $products, $ptypes;

    function __construct()
    {
        $this->middleware('permission:product-accessory-list|product-accessory-create|product-accessory-edit|product-accessory-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-accessory-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-accessory-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-accessory-delete', ['only' => ['destroy']]);

        $this->products = Product::withTrashed()->where('category', 'accessory')->orderBy('name')->get();
        $this->ptypes = ProductSubcategory::where('category', 'accessory')->get();
    }

    public function index()
    {
        $products = $this->products;
        return view('backend.product.accessory.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ptypes = $this->ptypes;
        return view('backend.product.accessory.create', compact('ptypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'type_id' => 'required',
            'selling_price' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $input['category'] = 'accessory';
        $product = Product::create($input);
        if (getSubDomain() == 'store')
            $saspdct = addProductToSASStore($product);
        return redirect()->route('product.accessory')->with("success", "Product created successfully!");
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
        $ptypes = $this->ptypes;
        return view('backend.product.accessory.edit', compact('ptypes', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'type_id' => 'required',
            'selling_price' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        $product = Product::findOrFail($id);
        $product->update($input);
        if (getSubDomain() == 'store')
            $saspdct = addProductToSASStore($product);
        return redirect()->route('product.accessory')->with("success", "Product updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::findOrFail(decrypt($id))->delete();
        return redirect()->route('product.accessory')->with("success", "Product deleted successfully!");
    }
}
