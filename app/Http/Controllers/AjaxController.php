<?php

namespace App\Http\Controllers;

use App\Models\BankTransfer;
use App\Models\Consultation;
use App\Models\IncomeExpense;
use App\Models\LabOrderNote;
use App\Models\Month;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PatientProcedure;
use App\Models\Payment;
use App\Models\Power;
use App\Models\Product;
use App\Models\ProductSubcategory;
use App\Models\PurchaseDetail;
use App\Models\Spectacle;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AjaxController extends Controller
{

    function __construct()
    {
        // 
    }

    public function getAppointmentTime(Request $request)
    {
        $arr = getAppointmentTimeList($request->date, $request->doctor_id, $request->branch_id);
        return response()->json($arr);
    }

    public function getProductsByCategory($category)
    {
        $products = Product::where('category', $category)->selectRaw("id, CONCAT_WS('-', name, code) AS name")->orderBy('name')->get();
        return response()->json($products);
    }

    public function getProductsByType($type)
    {
        $products = Product::where('type_id', $type)->orderBy('name')->get();
        return response()->json($products);
    }

    public function getPrescription($source, $val)
    {
        if ($source == 'hospital') :
            $spectacle = DB::connection('mysql1')->table('spectacles')->selectRaw("re_dist_sph as re_sph, re_dist_cyl as re_cyl, re_dist_axis as re_axis, re_dist_add as re_add, re_dist_va as re_va, rpd as re_pd, lpd as le_pd, le_dist_sph as le_sph, le_dist_cyl as le_cyl, le_dist_axis as le_axis, le_dist_add as le_add, le_dist_va as le_va, re_int_add, le_int_add, '' as a_size, '' as b_size, '' as dbl, '' as fh, '' as ed, '' as vd, '' as w_angle, 0 as doctor, 0 as optometrist")->where('id', $val)->first();
        else :
            $spectacle = Spectacle::findOrFail($val);
        /*$order = Order::find($val);
            $odetails = OrderDetail::where('order_id', $val)->get()->toArray();
            $spectacle = [
                're_sph' => $odetails[0]['sph'],
                're_cyl' => $odetails[0]['cyl'],
                're_axis' => $odetails[0]['axis'],
                're_add' => $odetails[0]['add'],
                're_va' => $odetails[0]['va'],
                're_pd' => $odetails[0]['ipd'],
                'le_sph' => $odetails[1]['sph'],
                'le_cyl' => $odetails[1]['cyl'],
                'le_axis' => $odetails[1]['axis'],
                'le_add' => $odetails[1]['add'],
                'le_va' => $odetails[1]['va'],
                'le_pd' => $odetails[1]['ipd'],
                'a_size' => $order->a_size,
                'b_size' => $order->b_size,
                'dbl' => $order->dbl,
                'fh' => $order->fh,
                'ed' => $order->ed,
                'vd' => $order->vd,
                'w_angle' => $order->w_angle,
            ];*/
        endif;
        return response()->json($spectacle);
    }

    public function getProductPrice($pid, $category, $batch)
    {
        if ($category == 'pharmacy') :
            $product = PurchaseDetail::selectRaw("unit_price_sales AS selling_price")->where('product_id', $pid)->where('batch_number', $batch)->firstOrFail();
        else :
            $product = Product::findOrFail($pid);
        endif;
        return response()->json($product);
    }

    public function getProductBatch($branch, $product, $category)
    {
        $stock = getInventory($branch, $product, $category);
        return response()->json($stock);
    }

    public function getProductTypes($category, $attribute)
    {
        $types = ProductSubcategory::where('category', $category)->where('attribute', $attribute)->orderBy('name')->get();
        return response()->json($types);
    }

    public function getAvailableCredit($cid)
    {
        $cr = getAvailableCredit($cid);
        return response()->json($cr);
    }

    public function powers()
    {
        $sph = Power::where('name', 'sph')->selectRaw("value as name")->get();
        $cyl = Power::where('name', 'cyl')->selectRaw("value as name")->get();
        $axis = Power::where('name', 'axis')->selectRaw("value as name")->get();
        $add = Power::where('name', 'add')->selectRaw("value as name")->get();
        $intad = Power::where('name', 'intad')->selectRaw("value as name")->get();
        return response()->json([
            'sph' => $sph,
            'cyl' => $cyl,
            'axis' => $axis,
            'add' => $add,
            'intad' => $intad
        ]);
    }

    public function getPaymentDetailsByConsultation($cid)
    {
        $arr = json_decode(owedTotal($cid));
        $tot = $arr->registration_fee + $arr->consultation_fee + $arr->procedure_fee + $arr->pharmacy + $arr->store;
        $paid = Payment::where('consultation_id', $cid)->sum('amount');
        $bal = number_format($tot - $paid, 2);
        $op = "<table class='table table-bordered table-striped'><thead><th>Service Opted</th><th>Amount</th></thead><tbody>";
        $op .= "<tr><td>Registration Fee</td><td class='text-end fw-bold'>$arr->registration_fee</td></tr>";
        $op .= "<tr><td>Consultation Fee</td><td class='text-end fw-bold'>$arr->consultation_fee</td></tr>";
        $op .= "<tr><td>Procedure Fee</td><td class='text-end fw-bold'>$arr->procedure_fee</td></tr>";
        $op .= "<tr><td>Pharmacy</td><td class='text-end fw-bold'>$arr->pharmacy</td></tr>";
        $op .= "<tr><td>Store</td><td class='text-end fw-bold'>$arr->store</td></tr>";
        $op .= "</tbody><tfoot><tr><td class='text-end fw-bold'>Total</td><td class='text-end fw-bold'>$tot</td></tr><tr><td class='text-end fw-bold'>Paid</td><td class='text-end fw-bold'>$paid</td></tr><tr><td class='text-end fw-bold'>Balance</td><td class='text-end fw-bold'>$bal</td></tr></table>";
        echo $op;
    }

    public function getFrameDetailed(Request $request)
    {
        $op = "";
        $fdate = Carbon::parse($request->from_date)->startOfDay();
        $tdate = Carbon::parse($request->to_date)->endOfDay();
        $order = Order::whereBetween('created_at', [$fdate, $tdate])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->when($request->status, function ($q) use ($request) {
            return $q->where('order_status', $request->status);
        })->get();
        $op = '<div class="drawer-header">
                <h6 class="drawer-title" id="drawer-3-title">Frame Detailed</h6></div><div class="drawer-body table-responsive">';
        $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Order</th><th>Product Name</th><th>Product Code</th></tr></thead><tbody>';
        foreach ($order as $key1 => $ord) :
            foreach ($ord->details->where('eye', 'frame') as $key => $item) :
                $op .= "<tr>";
                $op .= '<td>' . $key1 + 1 . '</td>';
                $op .= '<td>' . $ord->ono() . '</td>';
                $op .= '<td>' . $item->product?->name . '</td>';
                $op .= '<td>' . $item->product?->code . '</td>';
                $op .= "</tr>";
            endforeach;
        endforeach;
        $op .= '</tbody></table>';
        $op .= '</div><div class="drawer-footer">Frame</div>';
        echo $op;
    }

    public function getDaybookDetailed(Request $request)
    {
        $op = "";
        $fdate = Carbon::parse($request->from_date)->startOfDay();
        $tdate = Carbon::parse($request->to_date)->endOfDay();
        switch ($request->type):
            case 'ord':
                //$data = getOrderDetailed($request->from_date, $request->to_date, $request->branch);
                $data = Payment::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->when(in_array($request->mode, array(5)), function ($q) use ($request) {
                    return $q->whereIn('payment_mode', [5, 6, 7]);
                })->when(in_array($request->mode, array(1, 2, 3, 4)), function ($q) use ($request) {
                    return $q->where('payment_mode', $request->mode);
                })->get();
                $op = '<div class="drawer-header">
                <h6 class="drawer-title" id="drawer-3-title">Order Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Customer Name</th><th>
                Date<th>Order ID</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->order?->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->order?->branch?->code . '/' . $item->order_id . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="4" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Daybook</div>';
                break;
            case 'advance':
                //$data = getOrderDetailed($request->from_date, $request->to_date, $request->branch);
                $data = Payment::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->when(in_array($request->mode, array('advance')), function ($q) use ($request) {
                    return $q->whereIn('payment_type', ['advance']);
                })->when(in_array($request->mode, array('advance1')), function ($q) use ($request) {
                    return $q->whereIn('payment_type', ['advance1']);
                })->when(in_array($request->mode, array('other')), function ($q) use ($request) {
                    return $q->whereIn('payment_type', ['partial', 'balance']);
                })->get();
                $op = '<div class="drawer-header">
                    <h6 class="drawer-title" id="drawer-3-title">Advance & Receipts Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Customer Name</th><th>
                    Date<th>Order ID</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->order?->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->order?->branch?->code . '/' . $item->order_id . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="4" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Advance & Receipts</div>';
                break;
            case 'income':
                $data = IncomeExpense::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->where('category', 'income')->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->when(in_array($request->mode, array(5)), function ($q) use ($request) {
                    return $q->whereIn('payment_mode', [5, 6, 7]);
                })->when(in_array($request->mode, array(1, 2, 3, 4)), function ($q) use ($request) {
                    return $q->where('payment_mode', $request->mode);
                })->get();
                $op = '<div class="drawer-header">
                <h6 class="drawer-title" id="drawer-3-title">Income Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Branch Name</th><th>
                Head</th><th>Date</th><th>Description</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->branch->name . '</td>';
                    $op .= '<td>' . $item->head->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->description . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="5" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Income</div>';
                break;
            case 'expense':
                $data = IncomeExpense::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->where('category', 'expense')->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->get();
                $op = '<div class="drawer-header">
                    <h6 class="drawer-title" id="drawer-3-title">Expense Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Branch Name</th><th>
                    Head</th><th>Date</th><th>Description</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->branch->name . '</td>';
                    $op .= '<td>' . $item->head->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->description . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="5" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Expense</div>';
                break;
            case 'bank':
                $data = BankTransfer::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->get();
                $op = '<div class="drawer-header">
                        <h6 class="drawer-title" id="drawer-3-title">Bank Transfer Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Branch Name</th><th>Date</th><th>Notes</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->branch->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->notes . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="4" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Bank Transfer</div>';
                break;
            case 'order':
                $order = Order::findOrFail($request->mode);
                $op = '<div class="drawer-header">
                        <h6 class="drawer-title" id="drawer-3-title">Order Detail</h6></div><div class="drawer-body table-responsive">';
                $op .= "<table><tbody>";
                $op .= "<tr><th>Order ID</th><td>" . $order->ono() . "</td></tr>";
                $op .= "<tr><th>Customer ID</th><td>" . $order->customer_id . "</td></tr>";
                $op .= "<tr><th>Customer Name</th><td>" . $order->name . "</td></tr>";
                $op .= "<tr><th>Branch</th><td>" . $order->branch->name . "</td></tr>";
                $op .= "<tr><th>Order Date</th><td>" . $order->created_at->format('d.M.Y h:i a') . "</td></tr>";
                $op .= "<tr><th>Exp.Del.Date</th><td>" . $order->expected_delivery_date->format('d.M.Y') . "</td></tr>";
                $op .= "<tr><th>Order Note</th><td>" . $order->order_note . "</td></tr>";
                $op .= "<tr><th>Invoice Note</th><td>" . $order->invoice_note . "</td></tr>";
                $op .= "<tr><th>Lab Note</th><td>" . $order->lab_note . "</td></tr>";
                $op .= "<tr><th>Special Lab Note</th><td>" . $order->special_lab_note . "</td></tr>";
                $op .= "<tr><th><br></th><td></td></tr>";
                $op .= "</tbody></table>";
                $op .= "<table class='table table-bordered'><thead><th>Eye</th><th>Product</th><th>Qty</th><th>Sph</th><th>Cyl</th><th>Axis</th><th>Add</th><th>pd</th><th>Price</th></thead><tbody>";
                foreach ($order->details as $key => $item) :
                    $op .= "<tr><td>" . strtoupper($item->eye) . "</td><td>" . $item->product->name . "</td><td>" . $item->qty . "</td><td>" . $item->sph . "</td><td>" . $item->cyl . "</td><td>" . $item->axis . "</td><td>" . $item->add . "</td><td>" . $item->ipd . "</td><td>" . $item->unit_price . "</td></tr>";
                endforeach;
                $op .= "</tbody></table>";
                $op .= "<h5 class='mt-3 mb-3'>Order History</h5>";
                $op .= "<table class='table table-bordered'><thead><th>Action</th><th>Performed at</th><th>Performed by</th></thead><tbody>";
                foreach ($order->history as $key => $item) :
                    $op .= "<tr><td>" . $item->action . "</td><td>" . $item->created_at->format('d.M.Y h:i a') . "</td><td>" . $item->user->name . "</td></tr>";
                endforeach;
                $op .= "</tbody></table>";
                $op .= '</div><div class="drawer-footer">Order Detail</div>';
                break;
            default:
                $op = "No records found";
        endswitch;
        echo $op;
    }

    public function getOrderData()
    {
        $orders = Month::leftJoin('orders as o', function ($q) {
            $q->on('o.created_at', '>=', DB::raw('LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH'));
            $q->on('o.created_at', '<', DB::raw('LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH + INTERVAL 1 MONTH'))->where('o.branch_id', Session::get('branch'));
        })->select(DB::raw("LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH AS date, COUNT(o.id) AS order_count, CONCAT_WS('/', DATE_FORMAT(LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH, '%b'), DATE_FORMAT(LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH, '%y')) AS month"))->groupBy('date', 'months.id')->orderByDesc('date')->get();
        return json_encode($orders);
    }

    public function checkPendingTransfer(Request $request)
    {
        $transfer = Transfer::when(!in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO']), function ($q) {
            return $q->where('to_branch_id', Session::get('branch'));
        });
        return response()->json([
            'message' => $transfer->exists() && $request->user()->hasPermissionTo('pending-transfer-list') ? "You have some pending transfers to accept. Please accept it asap." : "",
        ]);
    }

    public function getLabNote(string $oid)
    {
        $notes = LabOrderNote::where('order_id', $oid)->get();
        $op = "<table width='100%'><thead><tr><th>Notes</th><th>Created by</th><th>Created at</th></tr></thead><tbody>";
        foreach ($notes as $key => $item) :
            $op .= "<tr>";
            $op .= "<td>" . $item->notes . "</td>";
            $op .= "<td>" . $item->user->name . "</td>";
            $op .= "<td>" . $item->created_at->format('d.M.Y h:i a') . "</td>";
            $op .= "</tr>";
        endforeach;
        $op .= "</tbody></table>";
        echo $op;
    }

    public function getBookedProductDetails(Request $request)
    {
        $orders = OrderDetail::leftJoin('orders as o', 'o.id', 'order_details.order_id')->where('o.branch_id', $request->branch)->selectRaw("order_details.*")->whereNotIn('o.order_status', ['delivered', 'cancelled'])->where('order_details.eye', 'frame')->get();
        $op = "<table width='100%'><thead><tr><th>Order No</th><th>Created at</th></tr></thead><tbody>";
        foreach ($orders as $key => $item) :
            $op .= "<tr>";
            $op .= "<td>" . $item->order->ono() . "</td>";
            $op .= "<td>" . $item->created_at->format('d.M.Y h:i a') . "</td>";
            $op .= "</tr>";
        endforeach;
        $op .= "</tbody></table>";
        echo $op;
    }
}
