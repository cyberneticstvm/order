<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'id');
    }
}
