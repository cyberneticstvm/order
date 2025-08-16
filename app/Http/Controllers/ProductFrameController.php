<?php

namespace App\Http\Controllers;

use App\Models\Manufaturer;
use App\Models\Product;
use App\Models\ProductCollection;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;

class ProductFrameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $products, $ptypes, $manufacturers;

    function __construct()
    {
        $this->middleware('permission:product-frame-list|product-frame-create|product-frame-edit|product-frame-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-frame-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-frame-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-frame-delete', ['only' => ['destroy']]);

        $this->products = Product::withTrashed()->where('category', 'frame')->orderBy('name')->get();
        $this->ptypes = ProductSubcategory::where('category', 'frame')->get();
        $this->manufacturers = Manufaturer::orderBy('name')->pluck('name', 'id');
    }

    public function index()
    {
        $products = $this->products;
        return view('backend.product.frame.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $manufacturers = $this->manufacturers;
        $ptypes = $this->ptypes;
        $collection = ProductSubcategory::get();
        return view('backend.product.frame.create', compact('ptypes', 'manufacturers', 'collection'));
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
            'shape_id' => 'required',
            'manufacturer_id' => 'required',
            'selling_price' => 'required',
            'collection_id' => 'required',
        ]);
        $input = $request->except(array('collection_id'));
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        $input['category'] = 'frame';
        $product = Product::create($input);
        $data = [];
        foreach ($request->collection_id as $key => $collection) :
            $data[] = [
                'product_id' => $product->id,
                'collection_id' => $collection,
            ];
        endforeach;
        ProductCollection::insert($data);
        return redirect()->route('product.frame')->with("success", "Product created successfully!");
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
        $collection = ProductSubcategory::get();
        $ptypes = $this->ptypes;
        return view('backend.product.frame.edit', compact('ptypes', 'manufacturers', 'product', 'collection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'type_id' => 'required',
            'shape_id' => 'required',
            'manufacturer_id' => 'required',
            'selling_price' => 'required',
            'collection_id' => 'required',
        ]);
        $input = $request->except(array('collection_id'));
        $input['updated_by'] = $request->user()->id;
        $product = Product::findOrFail($id);
        $product->update($input);
        foreach ($request->collection_id as $key => $collection) :
            $data[] = [
                'product_id' => $product->id,
                'collection_id' => $collection,
            ];
        endforeach;
        ProductCollection::where('product_id', $product->id)->delete();
        ProductCollection::insert($data);
        return redirect()->route('product.frame')->with("success", "Product updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::findOrFail(decrypt($id))->delete();
        return redirect()->route('product.frame')->with("success", "Product deleted successfully!");
    }
}
