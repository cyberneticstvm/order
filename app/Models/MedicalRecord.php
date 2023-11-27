<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['review_date' => 'datetime'];

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id');
    }

    public function symptoms()
    {
        return $this->hasMany(MedicalRecordSymptom::class, 'medical_record_id', 'id');
    }

    public function diagnoses()
    {
        return $this->hasMany(MedicalRecordDiagnosis::class, 'medical_record_id', 'id');
    }

    public function vision()
    {
        return $this->hasOne(MedicalRecordVision::class, 'medical_record_id', 'id');
    }

    public function medicines()
    {
        return $this->hasMany(MedicalRecordPharmacy::class, 'medical_record_id', 'id');
    }

    public function isSuregry()
    {
        return $this->surgery_advised == 1 ? 'YES' : 'NO';
    }
}
