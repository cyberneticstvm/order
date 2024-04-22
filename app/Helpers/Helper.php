<?php

use App\Models\Appointment;
use App\Models\BankTransfer;
use Illuminate\Support\Facades\Session;
use App\Models\Branch;
use App\Models\Closing;
use App\Models\Consultation;
use App\Models\ConsultationType;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\Doctor;
use App\Models\Head;
use App\Models\IncomeExpense;
use App\Models\LabOrder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Patient;
use App\Models\PatientProcedure;
use App\Models\PatientProcedureDetail;
use App\Models\Payment;
use App\Models\Procedure;
use App\Models\Product;
use App\Models\ProductSubcategory;
use App\Models\Setting;
use App\Models\Transfer;
use App\Models\Voucher;
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
    $tot = IncomeExpense::where('category', 'expense')->whereIn('head_id', Head::where('daily_expense_limit', 1)->pluck('id'))->whereDate('date', Carbon::today())->where('branch_id', branch()->id)->sum('amount');
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

function isFullyPaid($oid, $status)
{
    $order = Order::findOrFail($oid);
    $paid = Payment::where('order_id', $oid)->sum('amount');
    if ($paid < $order->invoice_total && $status == 'delivered')
        return false;
    return true;
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
    // Sent to branch - recived item from fitting lab to branch directly
    // Received from lab - received item from stock/rx/outsource lab to Purchase Manager for further config and then Sent to branch
    return array('booked' => 'Booked', 'sent-to-lab' => 'Sent to Lab', 'sent-to-branch' => 'Sent to Branch', 'received_from_lab' => 'Received From Lab', 'ready-for-delivery' => 'Ready For Delivery', 'delivered' => 'Billed / Delivered');
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
    $income_total_cash = getIncomeTotalByMode($fdate, $tdate, $branch, $mode = [1]);
    $income_total_bank = getIncomeTotalByMode($fdate, $tdate, $branch, $mode = [4]);
    $income_total_card = getIncomeTotalByMode($fdate, $tdate, $branch, $mode = [2]);
    $income_total_upi = getIncomeTotalByMode($fdate, $tdate, $branch, $mode = [3]);
    $income_total_other = getIncomeTotalByMode($fdate, $tdate, $branch, $mode = [5, 6, 7]);
    $paid_total_cash = getPaidTotalByMode($from_date, $to_date, $branch, $mode = [1]);
    $paid_total_bank = getPaidTotalByMode($from_date, $to_date, $branch, $mode = [4]);
    $paid_total_card = getPaidTotalByMode($from_date, $to_date, $branch, $mode = [2]);
    $paid_total_upi = getPaidTotalByMode($from_date, $to_date, $branch, $mode = [3]);
    $paid_total_other = getPaidTotalByMode($from_date, $to_date, $branch, $mode = [5, 6, 7]);
    $bank_transfer_total = getBankTransferTotal($from_date, $to_date, $branch);
    $voucher_income_total_cash = getVoucherTotal($from_date, $to_date, $branch, $type = 'receipt', $mode = [1]);
    $voucher_income_total_bank = getVoucherTotal($from_date, $to_date, $branch, $type = 'receipt', $mode = [4]);
    $voucher_income_total_card = getVoucherTotal($from_date, $to_date, $branch, $type = 'receipt', $mode = [2]);
    $voucher_income_total_upi = getVoucherTotal($from_date, $to_date, $branch, $type = 'receipt', $mode = [3]);
    $voucher_income_total_other = getVoucherTotal($from_date, $to_date, $branch, $type = 'receipt', $mode = [5, 6, 7]);
    $voucher_paid_total_cash = getVoucherTotal($from_date, $to_date, $branch, $type = 'payment', $mode = [1]);
    $voucher_paid_total_bank = getVoucherTotal($from_date, $to_date, $branch, $type = 'payment', $mode = [4]);
    $voucher_paid_total_card = getVoucherTotal($from_date, $to_date, $branch, $type = 'payment', $mode = [2]);
    $voucher_paid_total_upi = getVoucherTotal($from_date, $to_date, $branch, $type = 'payment', $mode = [3]);
    $voucher_paid_total_other = getVoucherTotal($from_date, $to_date, $branch, $type = 'payment', $mode = [5, 6, 7]);
    return json_encode([
        'order_total' => $order_total,
        'paid_total' => $paid_total,
        'expense_total' => $expense_total,
        'income_total' => $income_total,
        'income_total_cash' => $income_total_cash,
        'income_total_bank' => $income_total_bank,
        'income_total_card' => $income_total_card,
        'income_total_upi' => $income_total_upi,
        'income_total_other' => $income_total_other,
        'paid_total_cash' => $paid_total_cash,
        'paid_total_bank' => $paid_total_bank,
        'paid_total_card' => $paid_total_card,
        'paid_total_upi' => $paid_total_upi,
        'paid_total_other' => $paid_total_other,
        'bank_transfer_total' => $bank_transfer_total,
        'voucher_income_total_cash' => $voucher_income_total_cash,
        'voucher_income_total_bank' => $voucher_income_total_bank,
        'voucher_income_total_card' => $voucher_income_total_card,
        'voucher_income_total_upi' => $voucher_income_total_upi,
        'voucher_income_total_other' => $voucher_income_total_other,
        'voucher_paid_total_cash' => $voucher_paid_total_cash,
        'voucher_paid_total_bank' => $voucher_paid_total_bank,
        'voucher_paid_total_card' => $voucher_paid_total_card,
        'voucher_paid_total_upi' => $voucher_paid_total_upi,
        'voucher_paid_total_other' => $voucher_paid_total_other,
        'voucher_receipt_total' => $voucher_income_total_cash + $voucher_income_total_bank + $voucher_income_total_card + $voucher_income_total_upi + $voucher_income_total_other,
        'voucher_payment_total' => $voucher_paid_total_cash + $voucher_paid_total_bank + $voucher_paid_total_card + $voucher_paid_total_upi + $voucher_paid_total_other,
    ]);
}

function getVoucherTotal($from_date, $to_date, $branch, $type, $mode)
{
    return Voucher::whereBetween('created_at', [$from_date, $to_date])->where('category', $type)->whereIn('payment_mode', $mode)->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('amount');
}

function getBankTransferTotal($from_date, $to_date, $branch)
{
    return BankTransfer::whereBetween('created_at', [$from_date, $to_date])->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('amount');
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

function getIncomeTotalByMode($from_date, $to_date, $branch, $mode)
{
    return IncomeExpense::whereBetween('date', [$from_date, $to_date])->whereIn('payment_mode', $mode)->where('category', 'income')->when($branch > 0, function ($q) use ($branch) {
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
    //
    else :
        if ($branch == 0) :
            $stock = DB::select("SELECT 'Main Stock' AS branch, tbl1.product_id, tbl1.product_name, tbl1.purchasedQty, SUM(CASE WHEN t.from_branch_id = 0 AND t.deleted_at IS NULL AND t.transfer_status = 1 THEN td.qty ELSE 0 END) AS transferredQty, SUM(CASE WHEN dam.deleted_at IS NULL THEN dam.qty ELSE 0 END) AS damagedQty, 0 as returnedQty, 0 as soldQty, tbl1.purchasedQty - (IFNULL(SUM(CASE WHEN t.from_branch_id = 0 AND t.deleted_at IS NULL THEN td.qty ELSE 0 END), 0) + IFNULL(SUM(CASE WHEN dam.deleted_at IS NULL THEN dam.qty ELSE 0 END), 0)) AS balanceQty FROM (SELECT pdct.id AS product_id, pdct.name AS product_name, SUM(pd.qty) AS purchasedQty FROM purchase_details pd LEFT JOIN products pdct ON pd.product_id = pdct.id WHERE IF(? > 0, pd.product_id = ?, 1) AND pdct.category = ? GROUP BY pdct.id, pdct.name) AS tbl1 LEFT JOIN transfer_details td ON tbl1.product_id = td.product_id LEFT JOIN transfers t ON t.id = td.transfer_id LEFT JOIN product_damages dam ON tbl1.product_id = dam.product_id GROUP BY product_id, product_name, purchasedQty", [$product, $product, $category]);
        else :
            $branch = Branch::findOrFail($branch);
            $bname = $branch->name;
            $stock = DB::select("SELECT '$bname' AS branch, tbl1.product_id, tbl1.product_name, tbl1.purchasedQty, tbl1.transferredQty, SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL THEN od.qty ELSE 0 END) AS soldQty, SUM(CASE WHEN sr.returned_branch=? AND sr.deleted_at IS NULL THEN srd.returned_qty ELSE 0 END) AS returnedQty, (tbl1.purchasedQty + SUM(CASE WHEN sr.returned_branch=? AND sr.deleted_at IS NULL THEN srd.returned_qty ELSE 0 END)) - ((tbl1.transferredQty+SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL THEN od.qty ELSE 0 END) + SUM(CASE WHEN dam.deleted_at IS NULL AND dam.from_branch = ? THEN dam.qty ELSE 0 END))) AS balanceQty, SUM(CASE WHEN dam.deleted_at IS NULL AND dam.from_branch = ? THEN dam.qty ELSE 0 END) AS damagedQty FROM (SELECT pdct.id AS product_id, pdct.name AS product_name, SUM(CASE WHEN t.to_branch_id = ? THEN td.qty ELSE 0 END) AS purchasedQty, SUM(CASE WHEN t.from_branch_id = ? THEN td.qty ELSE 0 END) AS transferredQty FROM transfer_details td LEFT JOIN transfers t ON t.id = td.transfer_id LEFT JOIN products pdct ON td.product_id = pdct.id WHERE IF(? > 0, td.product_id = ?, 1) AND pdct.category = ? AND t.deleted_at IS NULL AND t.transfer_status = 1 GROUP BY pdct.id, pdct.name) AS tbl1 LEFT JOIN order_details od ON od.product_id = tbl1.product_id LEFT JOIN orders o ON o.id = od.order_id LEFT JOIN product_damages dam ON tbl1.product_id = dam.product_id LEFT JOIN sales_return_details srd ON tbl1.product_id = srd.product_id LEFT JOIN sales_returns sr ON sr.id = srd.return_id GROUP BY product_id, product_name, purchasedQty, transferredQty", [$branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $product, $product, $category]);
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

function getAvailableCredit($cid)
{
    return CustomerAccount::where('customer_id', $cid)->selectRaw("SUM(CASE WHEN type='credit' THEN amount ELSE 0 END) - SUM(CASE WHEN type='debit' THEN amount ELSE 0 END) AS cr")->value('cr') ?? 0;
}

function getFrameType($oid)
{
    $product = OrderDetail::where('order_id', $oid)->where('eye', 'frame')->first();
    return ProductSubcategory::where('id', Product::where('id', $product?->product_id ?? 0)->first()?->type_id)->first()?->name;
}

function isPendingFromLab($oid)
{
    $orders = LabOrder::where('order_id', $oid)->whereIn('status', ['sent-to-lab', 'received-from-lab'])->count('id');
    if ($orders > 0)
        return true;
    return false;
}
