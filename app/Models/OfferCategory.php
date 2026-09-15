<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['valid_from' => 'datetime', 'valid_to' => 'datetime'];

    public function statusKey(): string
    {
        if ($this->deleted_at) {
            return 'deleted';
        }

        if ($this->valid_from && now()->lt($this->valid_from)) {
            return 'upcoming';
        }

        if ($this->valid_to && now()->gt($this->valid_to)) {
            return 'expired';
        }

        return 'active';
    }

    public function status(): string
    {
        return match ($this->statusKey()) {
            'deleted' => "<span class='badge badge-danger'>Deleted</span>",
            'upcoming' => "<span class='badge badge-info'>Upcoming</span>",
            'expired' => "<span class='badge badge-warning'>Expired</span>",
            default => "<span class='badge badge-success'>Active</span>",
        };
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(OfferProduct::class, 'offer_category_id');
    }

    public function collection()
    {
        return $this->belongsTo(ProductSubcategory::class, 'collection_id');
    }

    public function isLensDiscount(): bool
    {
        return $this->offer_type === 'lens_discount';
    }
}
