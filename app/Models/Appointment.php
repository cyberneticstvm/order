<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['date' => 'date', 'time' => 'datetime'];

    public function status(){
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function doctor(){
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id')->withTrashed();
    }

    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withTrashed();
    }
}
