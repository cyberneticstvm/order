<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id')->withTrashed();
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id')->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withTrashed();
    }

    public function medicalrecord()
    {
        return $this->hasOne(MedicalRecord::class, 'id', 'consultation_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'consultation_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'consultation_id', 'id');
    }
}
