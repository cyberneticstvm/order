<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Closing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:setting-account-adjustment', ['only' => ['accountSetting', 'accountSettingUpdate']]);
    }

    public function accountSetting()
    {
        $branches = Branch::all();
        $closing = [];
        $inputs = array(date('Y-m-d'), Session::get('branch'));
        return view('backend.settings.account', compact('branches', 'closing', 'inputs'));
    }

    public function accountSettingFetch(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'branch' => 'required',
        ]);
        $branches = Branch::all();
        $inputs = array($request->date, $request->branch);
        $closing = Closing::where('date', $request->date)->where('branch', $request->branch)->first();
        return view('backend.settings.account', compact('branches', 'closing', 'inputs'));
    }

    public function accountSettingUpdate(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'branch' => 'required',
            'operand' => 'required',
            'amount' => 'required',
        ]);
        if ($request->operand == 'add') :
            Closing::where("branch", $request->branch)->where('date', '>=', $request->date)->increment('closing_balance', $request->amount);
        else :
            Closing::where("branch", $request->branch)->where('date', '>=', $request->date)->decrement('closing_balance', $request->amount);
        endif;
        return redirect()->back()->with('success', 'Record updated successfully');
    }
}
