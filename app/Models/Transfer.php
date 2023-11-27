<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['transfer_date' => 'datetime'];

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function isTransferAccepted()
    {
        return ($this->transfer_status == 0) ? "<span class='badge badge-warning'>Pending</span>" : "<span class='badge badge-info'>Accepted</span>";
    }

    public function details()
    {
        return $this->hasMany(TransferDetails::class, 'transfer_id', 'id');
    }

    public function frombranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id', 'id');
    }

    public function tobranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id', 'id');
    }
}
