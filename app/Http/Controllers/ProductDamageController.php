<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductDamage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        $products = ProductDamage::withTrashed()->where('from_branch', Session::get('branch'))->latest()->get();
        return view('backend.order.damage.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($category)
    {
        //$products = Product::where('category', $category)->orderBy('name')->get();
        $products = Product::selectRaw("id, category, CONCAT_WS('-', name, code) AS name")->whereIn('category', [$category])->orderBy('name')->get();
        return view('backend.order.damage.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'qty' => 'required',
            'description' => 'required',
        ]);
        $input = $request->all();
        $input['category'] = Product::findOrFail($request->product_id)->category;
        $input['from_branch'] = Session::get('branch');
        $input['to_branch'] = 0;
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        ProductDamage::create($input);
        return redirect()->route('product.damage.register')->with("success", "Damage added successfully");
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
        $damage = ProductDamage::findOrFail(decrypt($id));
        //$products = Product::where('category', $damage->category)->orderBy('name')->get();
        $products = Product::selectRaw("id, category, CONCAT_WS('-', name, code) AS name")->whereIn('category', [$damage->category])->orderBy('name')->get();
        return view('backend.order.damage.edit', compact('damage', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'qty' => 'required',
            'description' => 'required',
        ]);
        $input = $request->all();
        $input['category'] = Product::findOrFail($request->product_id)->category;
        $input['updated_by'] = $request->user()->id;
        ProductDamage::findOrFail($id)->update($input);
        return redirect()->route('product.damage.register')->with("success", "Damage updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ProductDamage::findOrFail(decrypt($id))->delete();
        return redirect()->route('product.damage.register')->with("success", "Damage deleted successfully");
    }
}
