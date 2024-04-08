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

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function optometrist()
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

    public function hasOrder($specid)
    {
        //$ocount = Order::where('customer_id', $cid)->whereDate('created_at', Carbon::today())->count('id');
        $spec = Spectacle::where('id', $specid)->whereDate('created_at', Carbon::today())->first();
        return ($spec?->order_id) ? true : false;
    }
}
