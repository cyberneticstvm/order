<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoyaltyCardSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function productType()
    {
        return $this->belongsTo(ProductSubcategory::class, 'type_id', 'id');
    }
}
