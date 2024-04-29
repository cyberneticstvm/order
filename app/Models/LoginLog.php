<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['logged_in' => 'datetime', 'logged_out' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function status()
    {
        return $this->logged_out ? "<span class='badge badge-danger'>Inactive</span>" : "<span class='badge badge-success'>Active</span>";
    }
}
