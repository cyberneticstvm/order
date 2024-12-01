<?php

namespace App\Http\Controllers;

use App\Models\BankTransfer;
use App\Models\Branch;
use App\Models\Consultation;
use App\Models\Head;
use App\Models\IncomeExpense;
use App\Models\LoginLog;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\ProductDamage;
use App\Models\ProductSubcategory;
use App\Models\PurchaseDetail;
use App\Models\SalesReturn;
use App\Models\Transfer;
use App\Models\TransferDetails;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    protected $branches, $products;

    public function __construct()
    {
        $this->middleware('permission:report-daybook', ['only' => ['daybook', 'fetchDayBook']]);
        $this->middleware('permission:report-payment', ['only' => ['payment', 'fetchPayment']]);
        $this->middleware('permission:report-sales', ['only' => ['sales', 'fetchSales']]);
        $this->middleware('permission:report-stock-status', ['only' => ['stockStatus', 'fetchStockStatus']]);
        $this->middleware('permission:report-login-log', ['only' => ['loginLog', 'fetchLoginLog']]);
        $this->middleware('permission:report-income-expense', ['only' => ['incomeExpense', 'fetchIncomeExpense']]);
        $this->middleware('permission:report-bank-transfer', ['only' => ['bankTransfer', 'fetchBankTransfer']]);
        $this->middleware('permission:report-product-damage', ['only' => ['productDamage', 'fetchProductDamage']]);
        $this->middleware('permission:report-sales-return', ['only' => ['salesReturn', 'fetchSalesReturn']]);
        $this->middleware('permission:report-product-transfer', ['only' => ['productTransfer', 'fetchProductTransfer']]);
        $this->middleware('permission:report-purchase', ['only' => ['purchase', 'fetchPurchase']]);
        $this->middleware('permission:export-order', ['only' => ['exportOrder']]);
        $this->middleware('permission:report-order-by-price', ['only' => ['orderByPrice', 'orderByPriceFetch']]);
        $this->middleware('permission:report-stock-movement', ['only' => ['stockMovement', 'stockMovementFetch']]);

        $this->middleware(function ($request, $next) {
            $brs = Branch::selectRaw("0 as id, 'All / Main Branch' as name");
            $this->branches = Branch::selectRaw("id, name")->when(in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) use ($brs) {
                return $q->union($brs);
            })->when(!in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) {
                return $q->where('id', Session::get('branch'));
            })->orderBy('id')->pluck('name', 'id');

            $this->products = Product::selectRaw("id, CONCAT_WS('-', name, code) AS name")->pluck('name', 'id');

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

    public function payment()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), 0, Session::get('branch')];
        $branches = $this->branches;
        $pmodes = PaymentMode::pluck('name', 'id');
        $data = collect();
        return view('backend.report.payment', compact('data', 'inputs', 'branches', 'pmodes'));
    }

    public function fetchPayment(Request $request)
    {
        $inputs = [$request->from_date, $request->to_date, $request->pmode, $request->branch];
        $branches = $this->branches;
        $pmodes = PaymentMode::pluck('name', 'id');
        $data = Payment::whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->pmode > 0, function ($q) use ($request) {
            return $q->where('payment_mode', $request->pmode);
        })->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->latest()->get();
        return view('backend.report.payment', compact('data', 'inputs', 'branches', 'pmodes'));
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
        })->orderBy('order_sequence', 'ASC')->get();
        return view(($request->redir == 'sales') ? 'backend.report.sales' : 'backend.report.order', compact('sales', 'inputs', 'branches'));
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
        $this->validate($request, [
            'category' => 'required',
            'branch' => 'required',
        ]);
        $data = getInventory($request->branch, 0, $request->category);
        $branches = $this->branches;
        $inputs = array($request->branch, $request->category);
        return view('backend.report.stock', compact('data', 'branches', 'inputs'));
    }

    public function loginLog()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), '0'];
        $users = User::pluck('name', 'id');
        $data = LoginLog::whereDate('created_at', Carbon::today())->orderByDesc('id')->get();
        return view('backend.report.login-log', compact('data', 'inputs', 'users'));
    }

    public function fetchLoginLog(Request $request)
    {
        $inputs = [$request->from_date, $request->to_date, $request->user];
        $users = User::pluck('name', 'id');
        $data = LoginLog::whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->user > 0, function ($q) use ($request) {
            return $q->where('user_id', $request->user);
        })->orderByDesc('id')->get();
        return view('backend.report.login-log', compact('data', 'inputs', 'users'));
    }

    public function incomeExpense()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), 0, 0, 0, Session::get('branch')];
        $branches = $this->branches;
        $pmodes = PaymentMode::pluck('name', 'id');
        $heads = Head::pluck('name', 'id');
        $data = collect();
        return view('backend.report.income-expense', compact('data', 'inputs', 'branches', 'pmodes', 'heads'));
    }

    public function fetchIncomeExpense(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
        $inputs = [$request->from_date, $request->to_date, $request->type, $request->head, $request->pmode, $request->branch];
        $branches = $this->branches;
        $pmodes = PaymentMode::pluck('name', 'id');
        $heads = Head::pluck('name', 'id');
        $data = IncomeExpense::whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->type, function ($q) use ($request) {
            return $q->where('category', $request->type);
        })->when($request->head, function ($q) use ($request) {
            return $q->where('head_id', $request->head);
        })->when($request->pmode, function ($q) use ($request) {
            return $q->where('payment_mode', $request->pmode);
        })->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->orderByDesc('id')->get();
        return view('backend.report.income-expense', compact('data', 'inputs', 'branches', 'pmodes', 'heads'));
    }

    public function bankTransfer()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), Session::get('branch')];
        $branches = $this->branches;
        $data = collect();
        return view('backend.report.bank-transfer', compact('data', 'inputs', 'branches'));
    }

    public function fetchBankTransfer(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
        $inputs = [$request->from_date, $request->to_date, $request->branch];
        $branches = $this->branches;
        $data = BankTransfer::whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->orderByDesc('id')->get();
        return view('backend.report.bank-transfer', compact('data', 'inputs', 'branches'));
    }

    public function productDamage()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), NULL, Session::get('branch')];
        $branches = $this->branches;
        $data = collect();
        return view('backend.report.product-damage', compact('data', 'inputs', 'branches'));
    }

    public function fetchProductDamage(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
        $inputs = [$request->from_date, $request->to_date, $request->status, $request->branch];
        $branches = $this->branches;
        $data = ProductDamage::whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('from_branch', $request->branch);
        })->when($request->status == 0, function ($q) use ($request) {
            return $q->whereNull('approved_status');
        })->when($request->status == 1, function ($q) use ($request) {
            return $q->where('approved_status', 1);
        })->orderByDesc('id')->get();
        return view('backend.report.product-damage', compact('data', 'inputs', 'branches'));
    }

    public function salesReturn()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), Session::get('branch')];
        $branches = $this->branches;
        $data = collect();
        return view('backend.report.sales-return', compact('data', 'inputs', 'branches'));
    }

    public function fetchSalesReturn(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
        $inputs = [$request->from_date, $request->to_date, $request->branch];
        $branches = $this->branches;
        $data = SalesReturn::whereBetween('created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('returned_branch', $request->branch);
        })->orderByDesc('id')->get();
        return view('backend.report.sales-return', compact('data', 'inputs', 'branches'));
    }

    public function productTransfer()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), 0, 'all', Session::get('branch'), 0, 'all'];
        $products = $this->products;
        $branches = $this->branches;
        $users = User::pluck('name', 'id');
        $data = collect();
        return view('backend.report.product-transfer', compact('data', 'inputs', 'branches', 'products', 'users'));
    }

    public function fetchProductTransfer(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
        $inputs = [$request->from_date, $request->to_date, $request->product, $request->status, $request->branch, $request->approved_by, $request->product_type];
        $branches = $this->branches;
        $products = $this->products;
        $users = User::pluck('name', 'id');
        /*$data = TransferDetails::leftJoin('transfers as t', 't.id', 'transfer_details.transfer_id')->whereBetween('t.created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('t.to_branch_id', $request->branch);
        })->when($request->product > 0, function ($q) use ($request) {
            return $q->where('transfer_details.product_id', $request->product);
        })->when($request->status != '', function ($q) use ($request) {
            return $q->where('t.transfer_status', $request->status);
        })->orderByDesc('transfer_details.id')->get();*/
        $data = Transfer::leftJoin('transfer_details as td', 'td.transfer_id', 'transfers.id')->selectRaw("transfers.id, transfers.transfer_date, transfers.transfer_number, transfers.from_branch_id, transfers.to_branch_id, transfers.transfer_note, transfers.transfer_status, transfers.accepted_by, transfers.accepted_at")->whereBetween('transfers.created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('transfers.to_branch_id', $request->branch);
        })->when($request->product > 0, function ($q) use ($request) {
            return $q->where('td.product_id', $request->product);
        })->when($request->status != 'all', function ($q) use ($request) {
            return $q->where('transfers.transfer_status', $request->status);
        })->when($request->product_type != 'all', function ($q) use ($request) {
            return $q->where('transfers.category', $request->product_type);
        })->when($request->approved_by > 0, function ($q) use ($request) {
            return $q->where('transfers.accepted_by', $request->approved_by);
        })->groupBy('transfers.id', 'transfers.transfer_date', 'transfers.transfer_number', 'transfers.from_branch_id', 'transfers.to_branch_id', 'transfers.transfer_note', 'transfers.transfer_status', 'transfers.accepted_by', 'transfers.accepted_at')->orderByDesc('transfers.created_at')->get();
        return view('backend.report.product-transfer', compact('data', 'inputs', 'branches', 'products', 'users'));
    }

    public function purchase()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), 0];
        $data = collect();
        $products = $this->products;

        return view('backend.report.purchase', compact('data', 'inputs', 'products'));
    }

    public function fetchPurchase(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
        $inputs = [$request->from_date, $request->to_date, $request->product];
        $products = $this->products;
        $data = PurchaseDetail::leftJoin('purchases as p', 'p.id', 'purchase_details.purchase_id')->whereBetween('p.created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->product > 0, function ($q) use ($request) {
            return $q->where('purchase_details.product_id', $request->product);
        })->orderByDesc('purchase_details.id')->get();
        return view('backend.report.purchase', compact('data', 'inputs', 'products'));
    }

    public function exportOrder()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), 'delivered', branch()->id];
        $branches = $this->branches;
        $sales = [];
        return view('backend.report.order', compact('sales', 'inputs', 'branches'));
    }

    function orderByPrice()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), Session::get('branch'), $cat = 'frame', $min = 1, $max = 1000];
        $branches = $this->branches;
        $records = collect();
        $categories = ProductSubcategory::all()->unique('category')->pluck('category', 'category');
        return view('backend.report.order-by-price', compact('inputs', 'categories', 'branches', 'records'));
    }

    function orderByPriceFetch(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
            'minimum' => 'required|numeric',
            'maximum' => 'required|numeric',
        ]);
        try {
            $inputs = [$request->from_date, $request->to_date, $request->branch, $request->category, $request->minimum, $request->maximum];
            $branches = $this->branches;
            $categories = ProductSubcategory::all()->unique('category')->pluck('category', 'category');
            $records = OrderDetail::leftJoin('orders AS o', 'o.id', 'order_details.order_id')->selectRaw("SUM(order_details.qty) AS ocount, SUM(order_details.total) AS amount, order_details.eye, order_details.product_id")->when($request->branch > 0, function ($q) use ($request) {
                return $q->where('o.branch_id', $request->branch);
            })->when($request->category == 'lens', function ($q) use ($request) {
                return $q->whereIn('order_details.eye', ['re', 'le']);
            })->when($request->category != 'lens', function ($q) use ($request) {
                return $q->whereIn('order_details.eye', [$request->category]);
            })->whereBetween('o.created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->whereBetween('order_details.unit_price', [$request->minimum, $request->maximum])->whereNull('order_details.return')->groupBy('order_details.eye', 'order_details.product_id')->get();
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return view('backend.report.order-by-price', compact('records', 'inputs', 'categories', 'branches'));
    }

    function stockMovement()
    {
        $inputs = [date('Y-m-d'), date('Y-m-d'), 0, Session::get('branch')];
        $branches = $this->branches;
        $products = Product::whereIn('category', ['frame', 'solution'])->get();
        $data = collect();
        return view('backend.report.stock-movement', compact('data', 'inputs', 'branches', 'products'));
    }

    function stockMovementFetch(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
            'branch' => 'required',
        ]);
        $inputs = [$request->from_date, $request->to_date, $request->product, $request->branch];
        $branches = $this->branches;
        $products = Product::whereIn('category', ['frame', 'solution'])->get();
        /*$data = Product::selectRaw("products.id, products.code, products.name, products.category, products.type_id, products.selling_price, IFNULL(COUNT(od.qty), 0) AS soldQty")->leftJoin('order_details AS od', 'products.id', 'od.product_id')->leftJoin('orders AS o', 'o.id', 'od.order_id')->whereIn('products.category', ['frame', 'solution'])->whereBetween('o.created_at', [Carbon::parse($request->from_date)->startOfDay(), Carbon::parse($request->to_date)->endOfDay()])->when($request->product > 0, function ($q) use ($request) {
            return $q->where('od.product_id', $request->product);
        })->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('o.branch_id', $request->branch);
        })->groupBy("products.id", "products.code", "products.name", "products.category", "products.type_id", "products.selling_price")->orderBy("soldQty")->get();*/
        $data = TransferDetails::leftJoin('transfers as t', 'transfer_details.transfer_id', 't.id')->selectRow("transfer_details.product_id")->unique('transfer_details.product_id');
        return view('backend.report.stock-movement', compact('data', 'inputs', 'branches', 'products'));
    }
}
