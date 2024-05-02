<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function lab()
    {
        return $this->belongsTo(Branch::class, 'lab_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id', 'id');
    }

    public function mainBranch()
    {
        return ($this->lab_id == 0) ? "text-danger fw-bold" : "";
    }
}
