<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDamage extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function frombranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch', 'id');
    }

    public function tobranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }
}
