<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Consultation;
use App\Models\Order;
use App\Models\Product;
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

        $this->middleware(function ($request, $next) {
            $brs = Branch::selectRaw("0 as id, 'Main Branch' as name");
            $this->branches = Branch::selectRaw("id, name")->when(in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) use ($brs) {
                return $q->union($brs);
            })->when(Auth::user()->roles->first()->id != 1, function ($q) {
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
        $sales = Order::whereBetween('order_date', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->when($request->order_status != 'all', function ($q) use ($request) {
            return $q->where('order_status', $request->order_status);
        })->orderByDesc('created_at')->get();
        return view('backend.report.sales', compact('sales', 'inputs', 'branches'));
    }

    public function stockStatus()
    {
        $branches = $this->branches;
        $data = [];
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
}
