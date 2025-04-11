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

    protected $casts = ['card_issued_date' => 'datetime'];

    private $per_day_cost;

    public function __construct()
    {
        $this->per_day_cost = 3.33;
    }

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

    public function totalCredit()
    {
        return $this->hasMany(VehiclePayment::class, 'vehicle_id', 'id')->sum('amount');
    }

    public function totalDays()
    {
        return Carbon::now()->diffInDays(Carbon::parse($this->created_at));
    }

    public function vstatus()
    {
        //return (Carbon::now()->diffInDays(Carbon::parse($this->payment()?->first()?->created_at)) < $this->payment_terms) ? "<span class='text-success'>Active</span>" : "<span class='text-danger'>Inactive</span>";
        return $this->daysLeft() > 0 ? "<span class='text-success'>Active</span>" : "<span class='text-danger'>Inactive</span>";
    }

    public function isVehicleActive()
    {
        //return (Carbon::now()->diffInDays(Carbon::parse($this->payment()?->first()?->created_at)) < $this->payment_terms) ? true : false;
        return $this->daysLeft() > 0 ? true : false;
    }

    public function daysLeft()
    {
        //return $this->payment_terms - Carbon::now()->diffInDays(Carbon::parse($this->payment()?->first()?->created_at));
        return $this->totalCredit() > 0 ? floor($this->totalCredit() / $this->per_day_cost) - $this->totalDays() : 0;
    }
}
