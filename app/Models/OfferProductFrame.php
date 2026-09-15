<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferProductFrame extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function offerProduct()
    {
        return $this->belongsTo(OfferProduct::class);
    }

    public function frame()
    {
        return $this->belongsTo(Product::class, 'frame_product_id');
    }
}
