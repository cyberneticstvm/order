<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['date' => 'datetime'];

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function head()
    {
        return $this->belongsTo(Head::class, 'head_id', 'id');
    }
}
