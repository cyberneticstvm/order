<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camp extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['from_date' => 'datetime', 'to_date' => 'datetime'];

    public function status(){
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withTrashed();
    }

    public function getOptometrist(){
        return $this->belongsTo(User::class, 'optometrist', 'id')->withTrashed();
    }

    public function getCordinator(){
        return $this->belongsTo(User::class, 'cordinator', 'id')->withTrashed();
    }

    public function ctype(){
        return $this->belongsTo(CampType::class, 'camp_type', 'id');
    }

    public function patients(){
        return $this->hasMany(CampPatient::class, 'camp_id', 'id');
    }
}
