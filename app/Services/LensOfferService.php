<?php

namespace App\Services;

use App\Models\OfferCategory;
use App\Models\OfferProduct;
use App\Models\Product;
use App\Models\ProductCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LensOfferService
{
    public function resolve(int $branchId, array $productIds, array $quantities): array
    {
        $lines = collect($productIds)->map(function ($productId, $key) use ($quantities) {
            return [
                'product_id' => (int) $productId,
                'qty' => max(0, (int) ($quantities[$key] ?? 0)),
            ];
        })->filter(fn ($line) => $line['product_id'] > 0);

        if ($lines->isEmpty()) {
            return $this->emptyResult();
        }

        $products = Product::whereIn('id', $lines->pluck('product_id')->unique())
            ->get(['id', 'category', 'selling_price'])
            ->keyBy('id');
        $lensLines = $lines->filter(fn ($line) =>
            $line['qty'] > 0 && $products->get($line['product_id'])?->category === 'lens'
        );
        $selectedFrameIds = $lines
            ->filter(fn ($line) => $products->get($line['product_id'])?->category === 'frame')
            ->pluck('product_id')
            ->unique();
        $framesByCollection = ProductCollection::whereIn('product_id', $selectedFrameIds)
            ->get(['product_id', 'collection_id'])
            ->keyBy('collection_id');

        if ($lensLines->isEmpty() || $framesByCollection->isEmpty()) {
            return $this->emptyResult();
        }

        $assignments = OfferProduct::query()
            ->with('offer')
            ->where('branch_id', $branchId)
            ->whereIn('product_id', $lensLines->pluck('product_id')->unique())
            ->whereHas('offer', fn ($query) => $this->activeLensOfferQuery($query, $branchId))
            ->get()
            ->filter(fn ($assignment) => $framesByCollection->has($assignment->offer->collection_id));

        if ($assignments->isEmpty()) {
            return $this->emptyResult();
        }

        $offerIds = $assignments->pluck('offer_category_id')->unique();
        if ($offerIds->count() > 1) {
            return $this->conflictResult('Products from more than one lens offer are selected. Only one offer is allowed per order.');
        }

        $offer = $assignments->first()->offer;
        $assignmentByLens = $assignments->keyBy('product_id');
        $discount = 0;
        $primaryLensId = null;
        $linkedFrameId = null;

        foreach ($lensLines as $line) {
            $assignment = $assignmentByLens->get($line['product_id']);
            if (!$assignment) {
                continue;
            }

            $product = $products->get($line['product_id']);
            $discount += ((float) $product->selling_price * $line['qty'] * (float) $offer->discount_percentage) / 100;
            $primaryLensId ??= $product->id;

            $linkedFrameId ??= $framesByCollection->get($offer->collection_id)?->product_id;
        }

        return [
            'status' => 'applied',
            'message' => 'Lens offer applied through the selected frame collection.',
            'offer_category_id' => $offer->id,
            'offer_name' => $offer->name,
            'lens_product_id' => $primaryLensId,
            'frame_product_id' => $linkedFrameId ? (int) $linkedFrameId : null,
            'discount_percentage' => (float) $offer->discount_percentage,
            'discount' => round($discount, 2),
        ];
    }

    public function activeLegacyOfferIds(int $branchId, array $productIds): Collection
    {
        return OfferProduct::query()
            ->where('branch_id', $branchId)
            ->whereIn('product_id', array_filter(array_map('intval', $productIds)))
            ->whereHas('product', fn ($query) => $query->where('category', 'frame'))
            ->whereHas('offer', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->where('offer_type', 'legacy')
                    ->where('valid_from', '<=', Carbon::now())
                    ->where('valid_to', '>=', Carbon::now());
            })->pluck('offer_category_id')->unique();
    }

    private function activeLensOfferQuery($query, int $branchId)
    {
        return $query->where('branch_id', $branchId)
            ->where('offer_type', 'lens_discount')
            ->where('discount_percentage', '>', 0)
            ->where('valid_from', '<=', Carbon::now())
            ->where('valid_to', '>=', Carbon::now());
    }

    private function emptyResult(): array
    {
        return [
            'status' => 'none',
            'message' => null,
            'offer_category_id' => null,
            'offer_name' => null,
            'lens_product_id' => null,
            'frame_product_id' => null,
            'discount_percentage' => 0,
            'discount' => 0,
        ];
    }

    private function conflictResult(string $message): array
    {
        return array_merge($this->emptyResult(), [
            'status' => 'conflict',
            'message' => $message,
        ]);
    }
}
