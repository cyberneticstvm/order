<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spectacle extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['stime' => 'datetime'];

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function optometrists()
    {
        return $this->belongsTo(User::class, 'id', 'optometrist');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'id', 'doctor');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
