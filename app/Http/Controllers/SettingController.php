<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function settings()
    {
        return redirect()->back()->with("error", "Nothing found");
    }
}
