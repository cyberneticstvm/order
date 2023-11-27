<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Consultation;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    protected $branches;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $br = Branch::selectRaw("id, name")->when(Auth::user()->roles->first()->id != 1, function ($q) {
                return $q->where('id', Session::get('branch'));
            })->orderBy('name');
            if (Auth::user()->roles->first()->id == 1) :
                $this->branches = Branch::selectRaw("'0' AS id, 'All Branches' AS name")->union($br)->pluck('name', 'id');
            else :
                $this->branches = $br->pluck('name', 'id');
            endif;
            return $next($request);
        });
    }

    public function daybook()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), branch()->id];
        $branches = $this->branches;
        $data = getDayBook($inputs[0], $inputs[1], $inputs[2]);
        return view('backend.report.daybook', compact('data', 'inputs', 'branches'));
    }

    public function fetchDayBook(Request $request)
    {
        $inputs = [$request->from_date, $request->to_date, $request->branch];
        $branches = $this->branches;
        $data = getDayBook($inputs[0], $inputs[1], $inputs[2]);
        return view('backend.report.daybook', compact('data', 'inputs', 'branches'));
    }

    public function consultation()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), branch()->id];
        $branches = $this->branches;
        $data = [];
        return view('backend.report.consultation', compact('data', 'inputs', 'branches'));
    }

    public function fetchConsultation(Request $request)
    {
        $inputs = [$request->from_date, $request->to_date, $request->branch];
        $branches = $this->branches;
        $data = Consultation::whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->get();
        return view('backend.report.consultation', compact('data', 'inputs', 'branches'));
    }

    public function lab()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), 'booked', branch()->id];
        $branches = $this->branches;
        $labs = Order::whereBetween('order_date', [date('Y-m-d'), date('Y-m-d')])->where('category', 'store')->orderByDesc('created_at')->get();
        return view('backend.report.lab', compact('labs', 'inputs', 'branches'));
    }

    public function fetchLab(Request $request)
    {
        $inputs = [$request->from_date, $request->to_date, $request->order_status, $request->branch];
        $branches = $this->branches;
        $labs = Order::whereBetween('order_date', [$request->from_date, $request->to_date])->where('category', 'store')->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->when($request->order_status != 'all', function ($q) use ($request) {
            return $q->where('order_status', $request->order_status);
        })->orderByDesc('created_at')->get();
        return view('backend.report.lab', compact('labs', 'inputs', 'branches'));
    }

    public function sales()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), 'booked', 'all', branch()->id];
        $branches = $this->branches;
        $sales = [];
        return view('backend.report.sales', compact('sales', 'inputs', 'branches'));
    }

    public function fetchSales(Request $request)
    {
        $inputs = [$request->from_date, $request->to_date, $request->order_status, $request->category, $request->branch];
        $branches = $this->branches;
        $sales = Order::whereBetween('order_date', [$request->from_date, $request->to_date])->when($request->category != 'all', function ($q) use ($request) {
            return $q->where('category', $request->category);
        })->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->when($request->order_status != 'all', function ($q) use ($request) {
            return $q->where('order_status', $request->order_status);
        })->orderByDesc('created_at')->get();
        return view('backend.report.sales', compact('sales', 'inputs', 'branches'));
    }
}
