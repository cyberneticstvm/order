<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderOffer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(OfferCategory::class, 'offer_category_id');
    }

    public function lens()
    {
        return $this->belongsTo(Product::class, 'lens_product_id');
    }

    public function frame()
    {
        return $this->belongsTo(Product::class, 'frame_product_id');
    }
}
