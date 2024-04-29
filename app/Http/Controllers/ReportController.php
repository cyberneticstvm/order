<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Consultation;
use App\Models\LoginLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    protected $branches;

    public function __construct()
    {
        $this->middleware('permission:report-daybook', ['only' => ['daybook', 'fetchDayBook']]);
        $this->middleware('permission:report-sales', ['only' => ['sales', 'fetchSales']]);
        $this->middleware('permission:report-stock-status', ['only' => ['stockStatus', 'fetchStockStatus']]);
        $this->middleware('permission:report-login-log', ['only' => ['loginLog', 'fetchLoginLog']]);

        $this->middleware(function ($request, $next) {
            $brs = Branch::selectRaw("0 as id, 'All / Main Branch' as name");
            $this->branches = Branch::selectRaw("id, name")->when(in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) use ($brs) {
                return $q->union($brs);
            })->when(!in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) {
                return $q->where('id', Session::get('branch'));
            })->orderBy('id')->pluck('name', 'id');
            return $next($request);
        });
    }

    public function daybook()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), branch()->id];
        $branches = $this->branches;
        $data = getDayBook($inputs[0], $inputs[1], $inputs[2]);
        $opening_balance = getOpeningBalance(Carbon::today()->startOfDay()->subDay(), branch()->id);
        return view('backend.report.daybook', compact('data', 'inputs', 'branches', 'opening_balance'));
    }

    public function fetchDayBook(Request $request)
    {
        $inputs = [$request->from_date, $request->to_date, $request->branch];
        $branches = $this->branches;
        $data = getDayBook($inputs[0], $inputs[1], $inputs[2]);
        $opening_balance = getOpeningBalance(Carbon::parse($request->from_date)->startOfDay()->subDay(), $request->branch);
        return view('backend.report.daybook', compact('data', 'inputs', 'branches', 'opening_balance'));
    }

    public function sales()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), 'booked', branch()->id];
        $branches = $this->branches;
        $sales = [];
        return view('backend.report.sales', compact('sales', 'inputs', 'branches'));
    }

    public function fetchSales(Request $request)
    {
        $inputs = [$request->from_date, $request->to_date, $request->order_status, $request->branch];
        $branches = $this->branches;
        $sales = Order::whereBetween(($request->order_status != 'delivered') ? 'order_date' : 'invoice_generated_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->when($request->order_status != 'all', function ($q) use ($request) {
            return $q->where('order_status', $request->order_status);
        })->orderByDesc('created_at')->get();
        return view('backend.report.sales', compact('sales', 'inputs', 'branches'));
    }

    public function stockStatus()
    {
        $branches = $this->branches;
        $data = collect();
        $inputs = array('0', 'frame');
        return view('backend.report.stock', compact('branches', 'data', 'inputs'));
    }

    public function fetchStockStatus(Request $request)
    {
        $data = getInventory($request->branch, 0, $request->category);
        $branches = $this->branches;
        $inputs = array($request->branch, $request->category);
        return view('backend.report.stock', compact('data', 'branches', 'inputs'));
    }

    public function loginLog()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), '0'];
        $users = User::pluck('name', 'id');
        $data = LoginLog::whereDate('created_at', Carbon::today())->get();
        return view('backend.report.login-log', compact('data', 'inputs', 'users'));
    }

    public function fetchLoginLog(Request $request)
    {
        $inputs = [$request->from_date, $request->to_date, $request->user];
        $users = User::pluck('name', 'id');
        $data = LoginLog::whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->from_date)->endOfDay()])->when($request->user > 0, function ($q) use ($request) {
            return $q->where('user_id', $request->user);
        })->orderByDesc('id')->get();
        return view('backend.report.login-log', compact('data', 'inputs', 'users'));
    }
}
