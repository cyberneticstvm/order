<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Closing;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{
    protected $branches;

    function __construct()
    {
        $this->middleware('permission:setting-account-adjustment', ['only' => ['accountSetting', 'accountSettingUpdate']]);
        $this->middleware('permission:setting-stock-adjustment', ['only' => ['stockAdjustmentSetting', 'stockAdjustmentSettingFetch', 'stockAdjustmentSettingUpdate']]);

        $this->middleware(function ($request, $next) {
            $brs = Branch::selectRaw("0 as id, 'All / Main Branch' as name");
            $this->branches = Branch::selectRaw("id, name")->when(in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO']), function ($q) use ($brs) {
                return $q->union($brs);
            })->when(!in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) {
                return $q->where('id', Session::get('branch'));
            })->orderBy('id')->pluck('name', 'id');
            return $next($request);
        });
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

    public function stockAdjustmentSetting()
    {
        $branches = $this->branches;
        $inputs = array('0', 'frame');
        $data = [];
        return view('backend.settings.stock-adjustment', compact('branches', 'inputs', 'data'));
    }

    public function stockAdjustmentSettingFetch(Request $request)
    {
        $data = getInventory($request->branch, 0, $request->category);
        $branches = $this->branches;
        $inputs = array($request->branch, $request->category);
        return view('backend.settings.stock-adjustment', compact('data', 'branches', 'inputs'));
    }

    public function stockAdjustmentSettingUpdate(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required',
            'product_category' => 'required',
        ]);
        try {
            DB::transaction(function () use ($request) {
                dd($request);
                die;
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
        return redirect()->back()->with("success", "Stock updated successfully");
    }
}
