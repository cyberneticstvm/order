<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\OfferCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    private $branches;

    function __construct()
    {
        $this->middleware('permission:offer-category-list|offer-category-create|offer-category-edit|offer-category-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:offer-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:offer-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:offer-category-delete', ['only' => ['destroy']]);

        $this->branches = Branch::where('type', 'branch')->get();
    }

    function index()
    {
        $categories = OfferCategory::withTrashed()->get();
        return view('backend.offer.category.index', compact('categories'));
    }

    function create()
    {
        $branches = $this->branches;
        return view('backend.offer.category.create', compact('branches'));
    }

    function store(Request $request)
    {
        $this->validate($request, [
            'discount_percentage' => 'nullable|numeric|max:100',
            'buy_number' => 'nullable|numeric|max:100',
            'get_number' => 'nullable|numeric|max:100',
            'branch_id' => 'required',
            'valid_from' => 'required|date|after_or_equal:today',
            'valid_to' => 'required|date|after_or_equal:valid_from',
        ]);
        try {
            $input = $request->all();
            $input['valid_from'] = Carbon::parse($request->valid_from)->startOfDay();
            $input['valid_to'] = Carbon::parse($request->valid_to)->endOfDay();
            $input['created_by'] = $request->user()->id;
            $input['updated_by'] = $request->user()->id;
            OfferCategory::create($input);
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('offer.category.list')->with('success', 'Offer category added successfully!');
    }

    function edit(string $id)
    {
        $branches = $this->branches;
        $category = OfferCategory::findOrFail(decrypt($id));
        return view('backend.offer.category.edit', compact('category', 'branches'));
    }

    function update(Request $request, string $id)
    {
        $this->validate($request, [
            'discount_percentage' => 'nullable|numeric|max:100',
            'buy_number' => 'nullable|numeric|max:100',
            'get_number' => 'nullable|numeric|max:100',
            'branch_id' => 'required',
            'valid_from' => 'required|date|after_or_equal:today',
            'valid_to' => 'required|date|after_or_equal:valid_from',
        ]);
        try {
            $input = $request->all();
            $input['valid_from'] = Carbon::parse($request->valid_from)->startOfDay();
            $input['valid_to'] = Carbon::parse($request->valid_to)->endOfDay();
            $input['updated_by'] = $request->user()->id;
            OfferCategory::findOrFail($id)->update($input);
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('offer.category.list')->with('success', 'Offer category updated successfully!');
    }

    function destroy(string $id)
    {
        OfferCategory::findOrFail(decrypt($id))->delete();
        return redirect()->route('offer.category.list')->with('success', 'Offer category deleted successfully!');
    }
}
