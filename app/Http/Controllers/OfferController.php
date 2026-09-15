<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\OfferCategory;
use App\Models\OfferProduct;
use App\Models\Product;
use App\Models\ProductSubcategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OfferController extends Controller
{
    private $branches, $products;

    function __construct()
    {
        $this->middleware('permission:offer-category-list|offer-category-create|offer-category-edit|offer-category-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:offer-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:offer-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:offer-category-delete', ['only' => ['destroy']]);

        $this->branches = Branch::where('type', 'branch')->get();
        $this->products = Product::whereIn('category', ['frame'])->selectRaw("id, CONCAT_WS('-', name, code) AS name")->orderBy('name')->pluck('name', 'id');
    }

    function index()
    {
        $categories = OfferCategory::with('collection')->withTrashed()->get();
        $products = $this->products;
        return view('backend.offer.category.index', compact('categories', 'products'));
    }

    function create()
    {
        $branches = $this->branches;
        $collection = ProductSubcategory::where('attribute', 'collection')->pluck('name', 'id');
        return view('backend.offer.category.create', compact('branches', 'collection'));
    }

    function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255', Rule::unique('offer_categories')->where('branch_id', $request->branch_id)],
            'offer_type' => 'required|in:legacy,lens_discount',
            'discount_percentage' => [
                'nullable', 'numeric', 'min:0', 'max:100', 'required_if:offer_type,lens_discount',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->offer_type === 'lens_discount' && (float) $value <= 0) {
                        $fail('The discount percentage must be greater than zero for a Lens Discount offer.');
                    }
                },
            ],
            'buy_number' => 'nullable|numeric|max:100',
            'get_number' => 'nullable|numeric|max:100',
            'branch_id' => 'required|exists:branches,id',
            'collection_id' => ['nullable', 'required_if:offer_type,lens_discount', Rule::exists('product_subcategories', 'id')->where('attribute', 'collection')],
            'valid_from' => 'required|date|after_or_equal:today',
            'valid_to' => 'required|date|after_or_equal:valid_from',
        ]);
        try {
            $input = $request->all();
            $input['valid_from'] = Carbon::parse($request->valid_from)->startOfDay();
            $input['valid_to'] = Carbon::parse($request->valid_to)->endOfDay();
            $input['collection_id'] = $request->filled('collection_id') ? (int) $request->collection_id : null;
            $input['created_by'] = $request->user()->id;
            $input['updated_by'] = $request->user()->id;
            $this->normaliseLensOffer($input);
            $this->ensureCollectionScheduleAvailable(null, $input);
            OfferCategory::create($input);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('offer.category.list')->with('success', 'Offer category added successfully!');
    }

    function edit(string $id)
    {
        $branches = $this->branches;
        $category = OfferCategory::findOrFail(decrypt($id));
        $collection = ProductSubcategory::where('attribute', 'collection')->pluck('name', 'id');
        return view('backend.offer.category.edit', compact('category', 'branches', 'collection'));
    }

    function update(Request $request, string $id)
    {
        $category = OfferCategory::findOrFail($id);
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255', Rule::unique('offer_categories')->where('branch_id', $request->branch_id)->ignore($category->id)],
            'offer_type' => 'required|in:legacy,lens_discount',
            'discount_percentage' => [
                'nullable', 'numeric', 'min:0', 'max:100', 'required_if:offer_type,lens_discount',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->offer_type === 'lens_discount' && (float) $value <= 0) {
                        $fail('The discount percentage must be greater than zero for a Lens Discount offer.');
                    }
                },
            ],
            'buy_number' => 'nullable|numeric|max:100',
            'get_number' => 'nullable|numeric|max:100',
            'branch_id' => 'required|exists:branches,id',
            'collection_id' => ['nullable', 'required_if:offer_type,lens_discount', Rule::exists('product_subcategories', 'id')->where('attribute', 'collection')],
            'valid_from' => 'required|date|after_or_equal:today',
            'valid_to' => 'required|date|after_or_equal:valid_from',
        ]);
        try {
            $input = $request->all();
            $input['valid_from'] = Carbon::parse($request->valid_from)->startOfDay();
            $input['valid_to'] = Carbon::parse($request->valid_to)->endOfDay();
            $input['collection_id'] = $request->filled('collection_id') ? (int) $request->collection_id : null;
            $input['updated_by'] = $request->user()->id;
            $this->normaliseLensOffer($input);
            $this->ensureCollectionScheduleAvailable($category->id, $input);
            $this->ensureUpdatedLensOfferDoesNotOverlap($category, $input);
            $category->update($input);
            if ($category->isLensDiscount()) {
                $category->products()->update(['branch_id' => $category->branch_id]);
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('offer.category.list')->with('success', 'Offer category updated successfully!');
    }

    function destroy(string $id)
    {
        OfferCategory::findOrFail(decrypt($id))->delete();
        OfferProduct::where('offer_category_id', decrypt($id))->delete();
        return redirect()->route('offer.category.list')->with('success', 'Offer category deleted successfully!');
    }

    function restore(string $id)
    {
        $category = OfferCategory::withTrashed()->findOrFail(decrypt($id));
        if ($category->isLensDiscount() && $this->trashedLensOfferHasConflict($category)) {
            return redirect()->back()->with('error', 'This lens offer now overlaps another active lens offer. Change its schedule or products before restoring it.');
        }

        $category->restore();
        OfferProduct::withTrashed()->where('offer_category_id', $category->id)->restore();
        return redirect()->route('offer.category.list')->with('success', 'Offer category restored successfully!');
    }

    private function normaliseLensOffer(array &$input): void
    {
        if (($input['offer_type'] ?? 'legacy') === 'lens_discount') {
            $input['buy_number'] = 0;
            $input['get_number'] = 0;
        }
    }

    private function ensureUpdatedLensOfferDoesNotOverlap(OfferCategory $category, array $input): void
    {
        if (($input['offer_type'] ?? 'legacy') !== 'lens_discount' || !$category->products()->exists()) {
            return;
        }

        if ($category->products()->whereHas('product', fn ($query) => $query->where('category', '!=', 'lens'))->exists()) {
            throw ValidationException::withMessages([
                'offer_type' => 'This category contains non-lens products. Remove them before changing it to Lens Discount.',
            ]);
        }

        $lensIds = $category->products()->pluck('product_id');
        $hasConflict = OfferProduct::query()
            ->whereIn('product_id', $lensIds)
            ->where('branch_id', $input['branch_id'])
            ->where('offer_category_id', '!=', $category->id)
            ->whereHas('offer', function ($query) use ($input) {
                $query->where('offer_type', 'lens_discount')
                    ->where('valid_from', '<=', $input['valid_to'])
                    ->where('valid_to', '>=', $input['valid_from']);
            })->exists();

        if ($hasConflict) {
            throw ValidationException::withMessages([
                'valid_from' => 'The updated schedule overlaps another lens offer containing one of these lenses.',
            ]);
        }
    }

    private function trashedLensOfferHasConflict(OfferCategory $category): bool
    {
        $products = OfferProduct::withTrashed()->where('offer_category_id', $category->id)->get();
        $overlappingOfferIds = OfferCategory::query()
            ->where('id', '!=', $category->id)
            ->where('branch_id', $category->branch_id)
            ->where('offer_type', 'lens_discount')
            ->where('valid_from', '<=', $category->valid_to)
            ->where('valid_to', '>=', $category->valid_from)
            ->pluck('id');

        if ($overlappingOfferIds->isEmpty()) {
            return false;
        }

        if (OfferCategory::whereIn('id', $overlappingOfferIds)->where('collection_id', $category->collection_id)->exists()) {
            return true;
        }

        if ($products->isEmpty()) {
            return false;
        }

        return OfferProduct::whereIn('offer_category_id', $overlappingOfferIds)
            ->whereIn('product_id', $products->pluck('product_id'))
            ->exists();
    }

    private function ensureCollectionScheduleAvailable(?int $ignoreId, array $input): void
    {
        if (($input['offer_type'] ?? 'legacy') !== 'lens_discount') {
            return;
        }

        $exists = OfferCategory::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('offer_type', 'lens_discount')
            ->where('branch_id', $input['branch_id'])
            ->where('collection_id', $input['collection_id'])
            ->where('valid_from', '<=', $input['valid_to'])
            ->where('valid_to', '>=', $input['valid_from'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'collection_id' => 'This collection already has an overlapping lens offer for the selected branch.',
            ]);
        }
    }
}
