<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;

class ProductSolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $products, $brands;

    function __construct()
    {
        $this->middleware('permission:product-solution-list|product-solution-create|product-solution-edit|product-solution-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-solution-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-solution-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-solution-delete', ['only' => ['destroy']]);

        $this->products = Product::withTrashed()->where('category', 'solution')->orderBy('name')->get();
        $this->brands = ProductSubcategory::where('category', 'solution')->where('attribute', 'brand')->get();
    }

    public function index()
    {
        $products = $this->products;
        return view('backend.product.solution.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = $this->brands;
        return view('backend.product.solution.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'brand_id' => 'required',
            'selling_price' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $input['category'] = 'solution';
        Product::create($input);
        return redirect()->route('product.solution')->with("success", "Product created successfully!");
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
        $brands = $this->brands;
        return view('backend.product.solution.edit', compact('brands', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'brand_id' => 'required',
            'selling_price' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        Product::findOrFail($id)->update($input);
        return redirect()->route('product.solution')->with("success", "Product updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::findOrFail(decrypt($id))->delete();
        return redirect()->route('product.solution')->with("success", "Product deleted successfully!");
    }
}
