<?php

namespace App\Models;

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
}
