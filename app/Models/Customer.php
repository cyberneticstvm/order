<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function spectacles()
    {
        return $this->hasMany(Spectacle::class, 'id', 'customer_id');
    }

    public function hasOrder($cid)
    {
        $ocount = Order::where('customer_id', $cid)->whereDate('created_at', Carbon::today())->count('id');
        return ($ocount == 1) ? true : false;
    }
}
