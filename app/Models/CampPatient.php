<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampPatient extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['review_date' => 'datetime'];

    public function status(){
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function vision(){
        return $this->hasOne(CampPatientVision::class, 'camp_patient_id', 'id');
    }

    public function camp(){
        return $this->belongsTo(Camp::class, 'camp_id', 'id');
    }

    public function isSuregry(){
        return $this->surgery_advised == 1 ? 'YES' : 'NO';
    }

    public function isInvestigation(){
        return $this->further_investigation_advised == 1 ? 'YES' : 'NO';
    }

    public function isGlasses(){
        return $this->galsses_advised == 1 ? 'YES' : 'NO';
    }

    public function isYearlyTest(){
        return $this->yearly_eye_test_advised == 1 ? 'YES' : 'NO';
    }
}
