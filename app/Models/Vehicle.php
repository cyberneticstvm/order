<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function payment()
    {
        return $this->hasMany(VehiclePayment::class, 'vehicle_id', 'id')->latest();
    }

    public function vstatus()
    {
        return (Carbon::now()->diffInDays(Carbon::parse($this->payment()?->first()?->created_at)) < $this->payment_terms) ? "<span class='text-success'>Active</span>" : "<span class='text-danger'>Inactive</span>";
    }

    public function isVehicleActive()
    {
        return (Carbon::now()->diffInDays(Carbon::parse($this->payment()?->first()?->created_at)) < $this->payment_terms) ? true : false;
    }

    public function daysLeft()
    {
        return $this->payment_terms - Carbon::now()->diffInDays(Carbon::parse($this->payment()?->first()?->created_at));
    }
}
