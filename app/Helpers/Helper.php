<?php

use App\Models\Appointment;
use Illuminate\Support\Facades\Session;
use App\Models\Branch;
use App\Models\Closing;
use App\Models\Consultation;
use App\Models\ConsultationType;
use App\Models\Customer;
use App\Models\Doctor;
use App\Models\IncomeExpense;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Patient;
use App\Models\PatientProcedure;
use App\Models\PatientProcedureDetail;
use App\Models\Payment;
use App\Models\Procedure;
use App\Models\Setting;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


function settings()
{
    return Setting::findOrFail(1);
}

function hsn()
{
    return array('lens' => '9001', 'frame' => '9003', 're' => '9001', 'le' => '9001', 'solution' => '9003', 'accessory' => '9003');
}

function title()
{
    return settings()->company_name;
}

function qrCodeText()
{
    return settings()->qr_code_text;
}

function branches()
{
    return Branch::all();
}

function procedures()
{
    return Procedure::orderBy('name')->get();
}

function branch()
{
    return Branch::findOrFail(Session::get('branch'));
}

function isExpenseLimitReached($amount, $ded = 0)
{
    $tot = IncomeExpense::where('category', 'expense')->whereDate('date', Carbon::today())->where('branch_id', branch()->id)->sum('amount');
    $tot = ($tot + $amount) - $ded;
    if ($tot > branch()->daily_expense_limit)
        return 1;
    return 0;
}

function patientId()
{
    return DB::table('patients')->selectRaw("CONCAT_WS('-', 'P', LPAD(IFNULL(max(id)+1, 1), 7, '0')) AS pid")->first();
}
function camptId()
{
    return DB::table('camps')->selectRaw("CONCAT_WS('-', 'CMP', LPAD(IFNULL(max(id)+1, 1), 7, '0')) AS cid")->first();
}

function productcode($category)
{
    $key = '0123456789';
    return substr(strtoupper($category), 0, 1) . substr(str_shuffle($key), 0, 6);
}

function invoicenumber($oid)
{
    $bcode = branch()->code;
    //$cat = substr(strtoupper($category), 0, 2);
    //return DB::table('orders')->selectRaw("CONCAT_WS('-', 'INV', '$cat', IFNULL(MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(invoice_number, '-', -2), '-', 1) AS INTEGER))+1, 1), '$bcode') AS ino")->where('branch_id', branch()->id)->first();
    return DB::table('orders')->selectRaw("CONCAT_WS('/', 'INV', '$bcode', LPAD(IFNULL($oid, 1), 7, '0')) AS ino")->first();
}

function purchaseId($category)
{
    $cat = substr(strtoupper($category), 0, 2);
    return DB::table('purchases')->selectRaw("CONCAT_WS('-', 'PUR', '$cat', LPAD(IFNULL(max(id)+1, 1), 7, '0')) AS pid")->first();
}

function transferId($category)
{
    $cat = substr(strtoupper($category), 0, 2);
    return DB::table('transfers')->selectRaw("CONCAT_WS('-', 'TRN', '$cat', LPAD(IFNULL(max(id)+1, 1), 7, '0')) AS tid")->first();
}

function getDocFee($doctor, $patient, $ctype)
{
    $days = settings()->consultation_fee_waived_days;
    $fee = 0;
    $date_diff = DB::table('consultations')->where('patient_id', $patient)->select(DB::raw("IFNULL(DATEDIFF(now(), created_at), 0) as days, CASE WHEN deleted_at IS NULL THEN 1 ELSE 0 END AS status"))->latest()->first();
    $diff = ($date_diff && $date_diff->days > 0) ? $date_diff->days : 0;
    $cstatus = ($date_diff && $date_diff->status > 0) ? $date_diff->status : 0;
    if ($diff == 0 || $diff > $days || ($diff < $days && $cstatus == 1)) :
        $doc = Doctor::findOrFail($doctor);
        $fee = $doc->fee;
    endif;
    $ctype = ConsultationType::findOrFail($ctype);
    $fee = ($ctype->fee == 1) ? $fee : 0;
    return $fee;
}

function mrn()
{
    $bcode = branch()->code;
    return DB::table('consultations')->selectRaw("CONCAT_WS('/', 'MRN', IFNULL(MAX(id)+1, 1), '$bcode') AS mrid")->first();
}

function getAppointmentTimeList($date, $doctor, $branch)
{
    $arr = [];
    $endtime = Carbon::parse(settings()->appointment_ends_at)->toTimeString();
    $starttime = Carbon::parse(settings()->appointment_starts_at)->toTimeString();
    $interval = settings()->per_appointment_minutes;
    if ($date && $doctor && $branch) :
        $starttime = ($starttime < Carbon::now()->toTimeString() && Carbon::parse($date)->toDate() == Carbon::today()) ? Carbon::now()->endOfHour()->addSecond()->toTimeString() : $starttime;

        $start = strtotime($starttime);

        $appointment = Appointment::select('time as atime')->whereDate('date', $date)->where('doctor_id', $doctor)->where('branch_id', $branch)->pluck('atime')->toArray();
        while ($start <= strtotime($endtime)) :
            $disabled = in_array(Carbon::parse(date('h:i A', $start))->toTimeString(), $appointment) ? 'disabled' : NULL;
            $arr[] = [
                'name' => date('h:i A', $start),
                'id' => Carbon::parse(date('h:i A', $start))->toTimeString(),
                'disabled' => $disabled,
            ];
            $start = strtotime('+' . $interval . ' minutes', $start);
        endwhile;
    endif;
    return $arr;
}

function uploadDocument($item, $path)
{
    $doc = Storage::disk('s3')->put($path, $item);
    $url = Storage::disk('s3')->url($doc);
    return $url;
}

function deleteDocument($path, $url)
{
    if (Storage::disk('s3')->exists($path . substr($url, strrpos($url, '/') + 1))) :
        Storage::disk('s3')->delete($path . substr($url, strrpos($url, '/') + 1));
    endif;
}

function orderStatuses()
{
    return array('booked' => 'Booked', 'under-process' => 'Under Process', 'pending' => 'Pending', 'ready-for-delivery' => 'Ready For Delivery', 'delivered' => 'Delivered');
}

function casetypes()
{
    return array('box' => 'Box', 'rexine' => 'Rexine', 'other' => 'Other');
}

function headcategory()
{
    return array('expense' => 'Expense', 'income' => 'Income', 'other' => 'Other');
}

function paymentypes()
{
    return array('advance1' => 'Advance', 'partial' => 'Partial', 'balance' => 'Balance');
}

function checkOrderedProductsAvailability($request)
{
    foreach ($request->product_id as $key => $item) :
        $stockin = Transfer::with('details')->where('to_branch_id', branch()->id)->get();
        $stockincount = $stockin->details()->where('product_id', $item)->sum('qty');
    endforeach;
}

function owedTotal($consultation_id)
{
    $consultation = Consultation::findOrFail($consultation_id);
    $registration_fee = Patient::findOrFail($consultation->patient_id)->registration_fee;
    $consultation_fee = $consultation->doctor_fee;
    $procedure_fee = PatientProcedure::join('patient_procedure_details as pd', 'pd.patient_procedure_id', 'patient_procedures.id')->where('consultation_id', $consultation_id)->sum('pd.fee');
    $pharmacy = Order::where('category', 'pharmacy')->where('consultation_id', $consultation_id)->sum('invoice_total');
    $store = Order::where('category', 'store')->where('consultation_id', $consultation_id)->sum('invoice_total');
    return json_encode([
        'registration_fee' => $registration_fee, 'consultation_fee' => $consultation_fee, 'procedure_fee' => $procedure_fee, 'pharmacy' => $pharmacy, 'store' => $store
    ]);
}

function getDayBook($fdate, $tdate, $branch)
{
    $from_date = Carbon::parse($fdate)->startOfDay();
    $to_date = Carbon::parse($tdate)->endOfDay();
    $order_total = getOrderTotal($fdate, $tdate, $branch);
    $paid_total = getPaidTotal($from_date, $to_date, $branch);
    $expense_total = getExpenseTotal($fdate, $tdate, $branch);
    $income_total = getIncomeTotal($fdate, $tdate, $branch);
    $paid_total_cash = getPaidTotalByMode($from_date, $to_date, $branch, $mode = [1]);
    $paid_total_other = getPaidTotalByMode($from_date, $to_date, $branch, $mode = [2, 3, 4, 5]);
    return json_encode([
        'order_total' => $order_total,
        'paid_total' => $paid_total,
        'expense_total' => $expense_total,
        'income_total' => $income_total,
        'paid_total_cash' => $paid_total_cash,
        'paid_total_other' => $paid_total_other,
    ]);
}
function getPaidTotalByMode($from_date, $to_date, $branch, $mode)
{
    return Payment::whereBetween('created_at', [$from_date, $to_date])->whereIn('payment_mode', $mode)->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('amount');
}

function getRegFeeTotal($from_date, $to_date, $branch)
{
    return Patient::whereBetween('created_at', [$from_date, $to_date])->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('registration_fee');
}
function getRegFeeDetailed($from_date, $to_date, $branch)
{
    return Patient::whereBetween('created_at', [$from_date, $to_date])->where('registration_fee', '>', 0)->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->get();
}
function getConsultationFeeTotal($from_date, $to_date, $branch)
{
    return Consultation::whereBetween('created_at', [$from_date, $to_date])->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('doctor_fee');
}
function getConsultationFeeDetailed($from_date, $to_date, $branch)
{
    return Consultation::whereBetween('created_at', [$from_date, $to_date])->where('doctor_fee', '>', 0)->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->get();
}
function getProcedureFeeTotal($from_date, $to_date, $branch)
{
    return PatientProcedureDetail::join('patient_procedures AS p', 'p.id', 'patient_procedure_details.patient_procedure_id')->whereNull('p.deleted_at')->whereBetween('created_at', [$from_date, $to_date])->when($branch > 0, function ($q) use ($branch) {
        return $q->where('p.branch_id', $branch);
    })->sum('fee');
}
function getProcedureFeeDetailed($from_date, $to_date, $branch)
{
    return PatientProcedure::whereBetween('created_at', [$from_date, $to_date])->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->get();
}
function getOrderTotal($from_date, $to_date, $branch)
{
    return Order::whereBetween('order_date', [$from_date, $to_date])->where('category', 'store')->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('invoice_total');
}
function getOrderDetailed($from_date, $to_date, $branch)
{
    return Order::whereBetween('order_date', [$from_date, $to_date])->where('invoice_total', '>', 0)->where('category', 'store')->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->get();
}
function getPharmacyTotal($from_date, $to_date, $branch)
{
    return Order::whereBetween('order_date', [$from_date, $to_date])->where('invoice_total', '>', 0)->where('category', 'pharmacy')->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('invoice_total');
}
function getPharmacyDetailed($from_date, $to_date, $branch)
{
    return Order::whereBetween('order_date', [$from_date, $to_date])->where('invoice_total', '>', 0)->where('category', 'pharmacy')->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->get();
}
function getPaidTotal($from_date, $to_date, $branch)
{
    return Payment::whereBetween('created_at', [$from_date, $to_date])->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('amount');
}
function getExpenseTotal($from_date, $to_date, $branch)
{
    return IncomeExpense::whereBetween('date', [$from_date, $to_date])->where('category', 'expense')->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('amount');
}

function getIncomeTotal($from_date, $to_date, $branch)
{
    return IncomeExpense::whereBetween('date', [$from_date, $to_date])->where('category', 'income')->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('amount');
}

function getOpeningBalance($date, $branch)
{
    return (Closing::where('branch', $branch)->whereDate('date', $date)->latest()->first()->closing_balance) ?? 0;
}

function getInventory($branch, $product, $category)
{
    $stock = [];
    if ($category == 'pharmacy') :
        if ($branch == 0) :
            $stock = DB::select("SELECT 'Main Stock' AS branch, tblPurchase.product_id, tblPurchase.name AS product_name, tblPurchase.batch_number, tblPurchase.purchasedQty, SUM(CASE WHEN t.from_branch_id = 0 AND t.transfer_status = 1 AND t.deleted_at IS NULL THEN td.qty ELSE 0 END) AS transferredQty, tblPurchase.purchasedQty-SUM(CASE WHEN t.from_branch_id = 0 AND t.transfer_status = 1 AND t.deleted_at IS NULL THEN td.qty ELSE 0 END) AS balanceQty FROM (SELECT pd.product_id, p.name, pd.batch_number, SUM(pd.qty) AS purchasedQty FROM purchase_details pd LEFT JOIN products p ON p.id = pd.product_id WHERE IF(? > 0, pd.product_id=?, 1) GROUP BY pd.batch_number, p.name, pd.product_id) AS tblPurchase LEFT JOIN transfer_details td ON td.product_id = tblPurchase.product_id AND td.batch_number = tblPurchase.batch_number LEFT JOIN transfers t ON t.id = td.transfer_id GROUP BY branch, product_id, product_name, batch_number, purchasedQty HAVING balanceQty > 0", [$product, $product]);
        else :
            $branch = Branch::findOrFail($branch);
            $bname = $branch->name;
            $stock = DB::select("SELECT '$bname' AS branch, tbl1.batch_number, tbl1.product_name, tbl1.purchasedQty, tbl1.transferredQty, SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL THEN od.qty ELSE 0 END) AS soldQty, tbl1.purchasedQty - (tbl1.transferredQty + SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL THEN od.qty ELSE 0 END)) AS balanceQty FROM(SELECT p.id AS product_id, p.name AS product_name, td.batch_number, SUM(CASE WHEN t.to_branch_id = ? AND t.deleted_at IS NULL THEN td.qty ELSE 0 END) AS purchasedQty, SUM(CASE WHEN t.from_branch_id = ? AND t.deleted_at IS NULL THEN td.qty ELSE 0 END) AS transferredQty FROM transfer_details td LEFT JOIN products p ON p.id = td.product_id LEFT JOIN transfers t ON t.id = td.transfer_id WHERE IF(? > 0, td.product_id = ?, 1) AND t.transfer_status = 1 AND td.batch_number IS NOT NULL GROUP BY p.id, p.name, td.batch_number) AS tbl1 LEFT JOIN order_details od ON od.product_id = tbl1.product_id AND od.batch_number = tbl1.batch_number LEFT JOIN orders o ON o.id=od.order_id GROUP BY batch_number, product_name, purchasedQty, transferredQty HAVING balanceQty > 0", [$branch->id, $branch->id, $branch->id, $branch->id, $product, $product]);
        endif;
    else :
        if ($branch == 0) :
            $stock = DB::select("SELECT 'Main Stock' AS branch, tbl1.product_name, tbl1.purchasedQty, SUM(CASE WHEN t.from_branch_id = 0 AND t.deleted_at IS NULL AND t.transfer_status = 1 THEN td.qty ELSE 0 END) AS transferredQty, SUM(dam.qty) AS damagedQty, tbl1.purchasedQty - SUM(CASE WHEN t.from_branch_id = 0 AND t.deleted_at IS NULL THEN td.qty ELSE 0 END) AS balanceQty FROM (SELECT pdct.id AS product_id, pdct.name AS product_name, SUM(pd.qty) AS purchasedQty FROM purchase_details pd LEFT JOIN products pdct ON pd.product_id = pdct.id WHERE IF(? > 0, pd.product_id = ?, 1) GROUP BY pdct.id, pdct.name) AS tbl1 LEFT JOIN transfer_details td ON tbl1.product_id = td.product_id LEFT JOIN transfers t ON t.id = td.transfer_id LEFT JOIN product_damages dam ON tbl1.product_id = dam.product_id GROUP BY product_name, purchasedQty", [$product, $product]);
        else :
            $branch = Branch::findOrFail($branch);
            $bname = $branch->name;
            $stock = DB::select("SELECT '$bname' AS branch, tbl1.product_name, tbl1.purchasedQty, tbl1.transferredQty, SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL THEN od.qty ELSE 0 END) AS soldQty, tbl1.purchasedQty - (tbl1.transferredQty+SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL THEN od.qty ELSE 0 END)) AS balanceQty FROM (SELECT pdct.id AS product_id, pdct.name AS product_name, SUM(CASE WHEN t.to_branch_id = ? THEN td.qty ELSE 0 END) AS purchasedQty, SUM(CASE WHEN t.from_branch_id = ? THEN td.qty ELSE 0 END) AS transferredQty FROM transfer_details td LEFT JOIN transfers t ON t.id = td.transfer_id LEFT JOIN products pdct ON td.product_id = pdct.id WHERE IF(? > 0, td.product_id = ?, 1) AND t.deleted_at IS NULL AND t.transfer_status = 1 GROUP BY pdct.id, pdct.name) AS tbl1 LEFT JOIN order_details od ON od.product_id = tbl1.product_id LEFT JOIN orders o ON o.id = od.order_id GROUP BY product_name, purchasedQty, transferredQty", [$branch->id, $branch->id, $branch->id, $branch->id, $product, $product]);
        endif;
    endif;
    return collect($stock);
}

function getPercentage($amount, $total)
{
    $percentage = 0;
    if ($amount > 0 && $total > 0) :
        $percentage = ($amount / $total) * 100;
    endif;
    return $percentage;
}

function getAvailableCredit($mobile)
{
    return (Customer::where('mobile', $mobile)->selectRaw("SUM(IFNULL(credit, 0) - IFNULL(debit, 0)) AS cr")->value('cr')) ?? 0;
}
