<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function manufacturer()
    {
        return $this->belongsTo(Manufaturer::class, 'manufacturer_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(ProductSubcategory::class, 'type_id', 'id');
    }

    public function shape()
    {
        return $this->belongsTo(ProductSubcategory::class, 'shape_id', 'id');
    }

    public function coating()
    {
        return $this->belongsTo(ProductSubcategory::class, 'coating_id', 'id');
    }

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function taxamount($total)
    {
        return $total - ($total - (($total * $this->tax_percentage) / 100));
    }
}
