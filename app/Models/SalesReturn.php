<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function orderBranch()
    {
        return $this->belongsTo(Branch::class, 'order_branch', 'id');
    }

    public function returnBranch()
    {
        return $this->belongsTo(Branch::class, 'returned_branch', 'id');
    }

    public function details()
    {
        return $this->hasMany(SalesReturnDetail::class, 'return_id', 'id');
    }
}
