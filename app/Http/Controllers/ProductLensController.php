<?php

namespace App\Http\Controllers;

use App\Models\Manufaturer;
use App\Models\Product;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;

class ProductLensController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $products, $ptypes, $manufacturers;

    function __construct()
    {
        $this->middleware('permission:product-lens-list|product-lens-create|product-lens-edit|product-lens-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-lens-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-lens-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-lens-delete', ['only' => ['destroy']]);
        $this->middleware('permission:product-lens-price-list', ['only' => ['lensPriceList']]);

        $this->products = Product::withTrashed()->where('category', 'lens')->orderBy('name')->get();
        $this->ptypes = ProductSubcategory::where('category', 'lens')->get();
        $this->manufacturers = Manufaturer::orderBy('name')->pluck('name', 'id');
    }

    public function lensPriceList()
    {
        $products = $this->products;
        return view('backend.product.lens.price-list', compact('products'));
    }

    public function index()
    {
        $products = $this->products;
        return view('backend.product.lens.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $manufacturers = $this->manufacturers;
        $ptypes = $this->ptypes;
        return view('backend.product.lens.create', compact('ptypes', 'manufacturers'));
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
            'manufacturer_id' => 'required',
            'selling_price' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $input['category'] = 'lens';
        Product::create($input);
        return redirect()->route('product.lens')->with("success", "Product created successfully!");
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
        $manufacturers = $this->manufacturers;
        $ptypes = $this->ptypes;
        return view('backend.product.lens.edit', compact('ptypes', 'manufacturers', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'type_id' => 'required',
            'manufacturer_id' => 'required',
            'selling_price' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        Product::findOrFail($id)->update($input);
        return redirect()->route('product.lens')->with("success", "Product updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::findOrFail(decrypt($id))->delete();
        return redirect()->route('product.lens')->with("success", "Product deleted successfully!");
    }
}
