<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['transfer_date' => 'datetime', 'accepted_at' => 'datetime', 'created_at' => 'datetime'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'transfer_id', 'id');
    }
}
