<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampPatientVision extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function patient(){
        return $this->belongsTo(CampPatient::class, 'id', 'camp_patient_id');
    }
}
