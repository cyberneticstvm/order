<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientProcedureDetail extends Model
{
    use HasFactory;

    public function procedures()
    {
        return $this->belongsTo(Procedure::class, 'procedure_id', 'id');
    }

    public function patientProcedure()
    {
        return $this->belongsTo(PatientProcedure::class, 'patient_procedure_id', 'id');
    }
}
