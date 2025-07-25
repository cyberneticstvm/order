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
use App\Models\LabOrderNote;
use App\Models\Month;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderHistory;
use App\Models\Patient;
use App\Models\PatientProcedure;
use App\Models\PatientProcedureDetail;
use App\Models\Payment;
use App\Models\Procedure;
use App\Models\Product;
use App\Models\ProductSubcategory;
use App\Models\PromotionSchedule;
use App\Models\Setting;
use App\Models\Transfer;
use App\Models\UserBranch;
use App\Models\VehiclePayment;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;


function api_url()
{
    return "https://emr.devihospitals.in";
}

function sendRequestedDocviaWa($mobile, $name, $oid, $doc_type)
{
    $token = Config::get('myconfig.whatsapp.token');
    if ($doc_type == 'prescription'):
        $config = [
            "messaging_product" => "whatsapp",
            "to" => "+91" . $mobile,
            "type" => "template",
            "template" => [
                "name" => "prescription_f",
                "language" => ["code" => "en"],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $name],
                            ["type" => "text", "text" => "+91 9388611622"],
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => 0,
                        "parameters" => [
                            ["type" => "text", "text" => encrypt($oid)],
                        ]
                    ]
                ]
            ]
        ];
    endif;

    if ($doc_type == 'receipt'):
        $config = [
            "messaging_product" => "whatsapp",
            "to" => "+91" . $mobile,
            "type" => "template",
            "template" => [
                "name" => "order_reference",
                "language" => ["code" => "en"],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "image",
                                "image" =>
                                [
                                    "link" => "https://store.devihospitals.in/public/backend/assets/images/logo/devi-logo.png",
                                ],
                            ],
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $name],
                            ["type" => "text", "text" => "+91 9388611622"],
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => 0,
                        "parameters" => [
                            ["type" => "text", "text" => encrypt($oid)],
                        ]
                    ]
                ]
            ]
        ];
    endif;

    if ($doc_type == 'invoice'):
        $config = [
            "messaging_product" => "whatsapp",
            "to" => "+91" . $mobile,
            "type" => "template",
            "template" => [
                "name" => "invoice_reference",
                "language" => ["code" => "en"],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "image",
                                "image" =>
                                [
                                    "link" => "https://store.devihospitals.in/public/backend/assets/images/logo/devi-logo.png",
                                ],
                            ],
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $name],
                            ["type" => "text", "text" => "+91 9388611622"],
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => 0,
                        "parameters" => [
                            ["type" => "text", "text" => encrypt($oid)],
                        ]
                    ]
                ]
            ]
        ];
    endif;

    $curl = curl_init();
    $data_string = json_encode($config);
    $ch = curl_init('https://graph.facebook.com/v22.0/543653938835557/messages');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        )
    );
    $result = curl_exec($ch);
    $res = json_decode($result, true);
    return $res;
}

function sendWaPromotion($schedule, $name, $mobile)
{
    $token = Config::get('myconfig.whatsapp.token');
    $config = [
        "messaging_product" => "whatsapp",
        "to" => "+91" . $mobile,
        "type" => "template",
        "template" => [
            "name" => $schedule->template_id,
            "language" => ["code" => $schedule->template_language],
            "components" => [
                [
                    "type" => "header",
                    "parameters" => [
                        [
                            "type" => "image",
                            "image" =>
                            [
                                "link" => ($schedule->entity == 'store') ? "https://store.devihospitals.in/public/backend/assets/images/logo/devi-hospital-logo.png" : "https://store.devihospitals.in/public/backend/assets/images/logo/devi-hospital-logo.png",
                            ],
                        ],
                    ]
                ],
                [
                    "type" => "body",
                    "parameters" => []
                ],
            ]
        ]
    ];
    $curl = curl_init();
    $data_string = json_encode($config);
    $ch = curl_init('https://graph.facebook.com/v22.0/543653938835557/messages');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        )
    );
    $result = curl_exec($ch);
    $res = json_decode($result, true);
    return $res;
}

function sendWAMessageWithLink($order, $type)
{
    $token = Config::get('myconfig.whatsapp.token');
    if ($type == 'receipt'):
        $config = [
            "messaging_product" => "whatsapp",
            "to" => "+91" . $order->mobile,
            "type" => "template",
            "template" => [
                "name" => "order_confirmation_v2",
                "language" => ["code" => "en"],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "image",
                                "image" =>
                                [
                                    "link" => "https://store.devihospitals.in/public/backend/assets/images/logo/devi-logo.png",
                                ],
                            ],
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $order->name],
                            ["type" => "text", "text" => $order->ono()],
                            ["type" => "text", "text" => number_format($order->invoice_total, 2)],
                            ["type" => "text", "text" => number_format($order->advance, 2)],
                            ["type" => "text", "text" => number_format($order->balance, 2)],
                            ["type" => "text", "text" => $order->expected_delivery_date->format('d.M.Y')],
                            ["type" => "text", "text" => "+91 9388611622"],
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => 0,
                        "parameters" => [
                            ["type" => "text", "text" => encrypt($order->id)],
                        ]
                    ]
                ]
            ]
        ];
    endif;
    $curl = curl_init();
    $data_string = json_encode($config);
    $ch = curl_init('https://graph.facebook.com/v22.0/543653938835557/messages');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        )
    );
    $result = curl_exec($ch);
    $res = json_decode($result, true);
    return $res;
}

function sendWAMessage($data, $type)
{
    $token = Config::get('myconfig.whatsapp.token');
    if ($type == 'order'):
        $order = $data;
        $config = [
            "messaging_product" => "whatsapp",
            "to" => "+91" . $order->mobile,
            "type" => "template",
            "template" => [
                "name" => "order_confirmation_v1",
                "language" => ["code" => "en"],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $order->name],
                            ["type" => "text", "text" => $order->ono()],
                            ["type" => "text", "text" => number_format($order->invoice_total, 2)],
                            ["type" => "text", "text" => number_format($order->advance, 2)],
                            ["type" => "text", "text" => number_format($order->balance, 2)],
                            ["type" => "text", "text" => $order->expected_delivery_date->format('d.M.Y')],
                            ["type" => "text", "text" => "+91 9388611622"],
                        ]
                    ]
                ]
            ]
        ];
    endif;
    if ($type == 'payment'):
        $order = Order::where('id', $data->order_id)->first();
        $config = [
            "messaging_product" => "whatsapp",
            "to" => "+91" . $order->mobile,
            "type" => "template",
            "template" => [
                "name" => "payment_confirmation",
                "language" => ["code" => "en"],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $order->name],
                            ["type" => "text", "text" => number_format($data->amount, 2)],
                            ["type" => "text", "text" => $order->ono()],
                            ["type" => "text", "text" => "+91 9388611622"],
                        ]
                    ]
                ]
            ]
        ];
    endif;
    if ($type == 'status'):
        $order = $data;
        $paid = Payment::where('order_id', $order->id)->get() ?? 0;
        $bal = number_format($order->invoice_total - $paid->sum('amount'), 2);
        $config = [
            "messaging_product" => "whatsapp",
            "to" => "+91" . $order->mobile,
            "type" => "template",
            "template" => [
                "name" => "order_ready",
                "language" => ["code" => "en"],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $order->name],
                            ["type" => "text", "text" => $order->ono()],
                            ["type" => "text", "text" => number_format($order->invoice_total, 2)],
                            ["type" => "text", "text" => number_format($paid->sum('amount'), 2)],
                            ["type" => "text", "text" => $bal ?? '0.00'],
                            ["type" => "text", "text" => "+91 9388611622"],
                        ]
                    ]
                ]
            ]
        ];
    endif;
    if ($type == 'cancel'):
        $order = $data;
        $config = [
            "messaging_product" => "whatsapp",
            "to" => "+91" . $order->mobile,
            "type" => "template",
            "template" => [
                "name" => "order_cancel",
                "language" => ["code" => "en"],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $order->name],
                            ["type" => "text", "text" => $order->ono()],
                            ["type" => "text", "text" => "+91 9388611622"],
                        ]
                    ]
                ]
            ]
        ];
    endif;
    $curl = curl_init();
    $data_string = json_encode($config);
    $ch = curl_init('https://graph.facebook.com/v22.0/543653938835557/messages');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        )
    );
    $result = curl_exec($ch);
    $res = json_decode($result, true);
    //return ($res['code'] == 200) ? 200 : $res['code'];
    return $res;
}

function getFloorVal($value, $base)
{
    $val =  floor($value);
    $reminder = $val % $base;
    return $val - $reminder;
}

function apiSecret()
{
    return 'fdjsvsgdf4dhgf687f4bg54g4hf787';
}

function allMonths()
{
    return Month::all();
}

function getYears()
{
    return Order::selectRaw("YEAR(created_at) AS id, YEAR(created_at) AS name")->get()->unique("id")->pluck('name', 'id');
}

function masterBranch()
{
    return Branch::where('ho_master', 1)->first();
}

function unpaidTotal($branch, $month, $year, $current_month)
{
    //current month exclude if 0
    $order = Order::leftJoin('payments as p', 'orders.id', 'p.order_id')->selectRaw("IFNULL(SUM(orders.invoice_total), 0) AS invtot, IFNULL(SUM(p.amount), 0) AS advance, IFNULL(SUM(orders.invoice_total) - SUM(p.amount), 0) AS balance")->where('orders.branch_id', $branch)->whereNotIn('orders.order_status', ['delivered'])->when($month > 0, function ($q) use ($month) {
        return $q->whereMonth('orders.created_at', $month);
    })->when($year > 0, function ($q) use ($year) {
        return $q->whereYear('orders.created_at', $year);
    })->when($current_month == 0, function ($q) {
        return $q->where('orders.created_at', '<', Carbon::now()->startOfMonth());
    })->first();
    return $order;
}

function settings()
{
    return Setting::findOrFail(1);
}

function hsn()
{
    return array('lens' => '9001', 'frame' => '9003', 're' => '9001', 'le' => '9001', 'solution' => '9003', 'accessory' => '9003', 'service' => '9405');
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

function userBranches()
{
    return Branch::whereIn('id', UserBranch::where('user_id', Auth::id())->pluck('branch_id'))->where('type', 'branch')->get();
}

function procedures()
{
    return Procedure::orderBy('name')->get();
}

function branch()
{
    return Branch::findOrFail(Session::get('branch'));
}

function uniqueCode($model, $col, $str, $min, $max)
{
    do {
        $code = $str . random_int($min, $max);
    } while ($model::where($col, $code)->first());

    return $code;
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
    if (($paid + ($order->credit_used) ?? 0) < $order->invoice_total && $status == 'delivered')
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
    return array('booked' => 'Booked', 'sent-to-lab' => 'Sent to Lab', 'sent-to-branch' => 'Sent to Branch', 'sent-to-main-branch' => 'Sent to Main Branch', 'received_from_lab' => 'Received From Lab', 'ready-for-delivery' => 'Ready For Delivery', 'delivered' => 'Billed / Delivered', 'cancelled' => 'Cancelled', 'job-completed' => 'Job Completed', 'job-under-process' => 'Job Under Process');
}

function branchInvoiceNumber()
{
    return Order::where('branch_id', branch()->id)->selectRaw("IFNULL(MAX(order_sequence)+1, 1) AS sid")->value('sid');
}

function casetypes()
{
    return array('box' => 'Box', 'rexine' => 'Rexine', 'other' => 'Other');
}

function bankTransferTypes()
{
    return array('cdm' => 'CDM / Bank', 'cash' => 'Cash');
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
        'registration_fee' => $registration_fee,
        'consultation_fee' => $consultation_fee,
        'procedure_fee' => $procedure_fee,
        'pharmacy' => $pharmacy,
        'store' => $store
    ]);
}

function getDayBook($fdate, $tdate, $branch)
{
    $from_date = Carbon::parse($fdate)->startOfDay();
    $to_date = Carbon::parse($tdate)->endOfDay();
    $order_total = getOrderTotal($fdate, $tdate, $branch);
    $paid_total = getPaidTotal($from_date, $to_date, $branch);
    $expense_total = getExpenseTotal($fdate, $tdate, $branch, 0);
    $expense_total_cash = getExpenseTotal($fdate, $tdate, $branch, 1);
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
    $bank_transfer_total = getBankTransferTotal($from_date, $to_date, $branch, $type = null);
    $bank_transfer_cash = getBankTransferTotal($from_date, $to_date, $branch, $type = 'cash');
    $bank_transfer_cdm = getBankTransferTotal($from_date, $to_date, $branch, $type = 'cdm');
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
    $advance_total = getOrderPayments($from_date, $to_date, $branch, $mode = ['advance']);
    $advance_after_order_total = getOrderPayments($from_date, $to_date, $branch, $mode = ['advance1']);
    $receipts_order_total = getOrderPayments($from_date, $to_date, $branch, $mode = ['partial', 'balance']);
    $vehicle_payment_total_cash = getVehiclePaymentTotal($from_date, $to_date, $branch, $mode = [1]);
    $vehicle_payment_total_upi = getVehiclePaymentTotal($from_date, $to_date, $branch, $mode = [3]);
    return json_encode([
        'order_total' => $order_total,
        'paid_total' => $paid_total,
        'expense_total' => $expense_total,
        'expense_total_cash' => $expense_total_cash,
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
        'bank_transfer_cash' => $bank_transfer_cash,
        'bank_transfer_cdm' => $bank_transfer_cdm,
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
        'advance_total' => $advance_total,
        'advance_after_order_total' => $advance_after_order_total,
        'receipts_order_total' => $receipts_order_total,
        'vehicle_payment_total_cash' => $vehicle_payment_total_cash,
        'vehicle_payment_total_upi' => $vehicle_payment_total_upi,
    ]);
}

function getVehiclePaymentTotal($from_date, $to_date, $branch, $mode)
{
    return VehiclePayment::whereBetween('created_at', [$from_date, $to_date])->whereIn('payment_mode', $mode)->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('amount');
}

function getOrderPayments($from_date, $to_date, $branch, $mode)
{
    return Payment::whereBetween('created_at', [$from_date, $to_date])->whereIn('payment_type', $mode)->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('amount');
}

function getVoucherTotal($from_date, $to_date, $branch, $type, $mode)
{
    return Voucher::whereBetween('created_at', [$from_date, $to_date])->where('category', $type)->whereIn('payment_mode', $mode)->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->sum('amount');
}

function getBankTransferTotal($from_date, $to_date, $branch, $type)
{
    return BankTransfer::whereBetween('created_at', [$from_date, $to_date])->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->when($type, function ($q) use ($type) {
        return $q->where('type', $type);
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
function getExpenseTotal($from_date, $to_date, $branch, $mode)
{
    return IncomeExpense::whereBetween('date', [$from_date, $to_date])->where('category', 'expense')->when($branch > 0, function ($q) use ($branch) {
        return $q->where('branch_id', $branch);
    })->when($mode > 0, function ($q) use ($mode) {
        return $q->where('payment_mode', $mode);
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
            $stock = DB::select("SELECT 'Main Stock' AS branch, tbl1.product_id, tbl1.product_name, tbl1.purchasedQty, tbl1.code AS pcode, SUM(CASE WHEN t.from_branch_id = 0 AND t.deleted_at IS NULL AND t.transfer_status = 1 THEN td.qty ELSE 0 END) AS transferredQty, SUM(CASE WHEN dam.deleted_at IS NULL THEN dam.qty ELSE 0 END) AS damagedQty, 0 as returnedQty, 0 as soldQty, tbl1.purchasedQty - (IFNULL(SUM(CASE WHEN t.from_branch_id = 0 AND t.deleted_at IS NULL THEN td.qty ELSE 0 END), 0) + IFNULL(SUM(CASE WHEN dam.deleted_at IS NULL THEN dam.qty ELSE 0 END), 0)) AS balanceQty FROM (SELECT pdct.id AS product_id, pdct.name AS product_name, pdct.code, SUM(pd.qty) AS purchasedQty FROM purchase_details pd LEFT JOIN products pdct ON pd.product_id = pdct.id WHERE IF(? > 0, pd.product_id = ?, 1) AND pdct.category = ? GROUP BY pdct.id, pdct.name, pdct.code) AS tbl1 LEFT JOIN transfer_details td ON tbl1.product_id = td.product_id LEFT JOIN transfers t ON t.id = td.transfer_id LEFT JOIN product_damages dam ON tbl1.product_id = dam.product_id GROUP BY product_id, product_name, purchasedQty, pcode", [$product, $product, $category]);
        else :
            $branch = Branch::findOrFail($branch);
            $bname = $branch->name;
            /*$stock = DB::select("SELECT tbl3.*, SUM(CASE WHEN dam.deleted_at IS NULL AND dam.approved_status = 1 AND dam.from_branch = ? THEN dam.qty ELSE 0 END) AS damagedQty, ((tbl3.purchasedQty+tbl3.returnedQty+SUM(CASE WHEN dam.deleted_at IS NULL AND dam.approved_status IS NULL AND dam.from_branch = ? THEN dam.qty ELSE 0 END))-(tbl3.transferredQty+tbl3.billedQty+SUM(CASE WHEN dam.deleted_at IS NULL AND dam.from_branch = ? THEN dam.qty ELSE 0 END))) AS balanceQty FROM(SELECT tbl2.*, SUM(CASE WHEN sr.returned_branch = ? AND sr.deleted_at IS NULL THEN srd.returned_qty ELSE 0 END) AS returnedQty FROM (SELECT tbl1.*, SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL AND o.order_status != 'delivered' AND od.return IS NULL AND o.stock_updated_at IS NULL AND od.eye = 'frame' THEN od.qty ELSE 0 END) AS soldQty, SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL AND o.order_status = 'delivered' AND od.return IS NULL AND o.stock_updated_at IS NULL THEN od.qty ELSE 0 END) AS billedQty FROM (SELECT pdct.id AS product_id, pdct.code AS pcode, pdct.name AS product_name, SUM(CASE WHEN t.to_branch_id = ? THEN td.qty ELSE 0 END) AS purchasedQty, SUM(CASE WHEN t.from_branch_id = ? THEN td.qty ELSE 0 END) AS transferredQty FROM transfer_details td LEFT JOIN transfers t ON t.id = td.transfer_id LEFT JOIN products pdct ON td.product_id = pdct.id WHERE IF(? > 0, td.product_id = ?, 1) AND pdct.category = ? AND t.deleted_at IS NULL AND t.transfer_status = 1 GROUP BY pdct.id, pdct.name, pdct.code) AS tbl1 LEFT JOIN order_details od ON od.product_id = tbl1.product_id LEFT JOIN orders o ON o.id = od.order_id GROUP BY product_id, product_name, purchasedQty, transferredQty, pcode) AS tbl2 LEFT JOIN sales_return_details srd ON tbl2.product_id = srd.product_id LEFT JOIN sales_returns sr ON sr.id = srd.return_id GROUP BY product_id, product_name, purchasedQty, transferredQty, pcode, billedQty, soldQty) AS tbl3 LEFT JOIN product_damages dam ON tbl3.product_id = dam.product_id GROUP BY product_id, product_name, purchasedQty, transferredQty, pcode, billedQty, soldQty, returnedQty", [$branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $product, $product, $category]);*/
            $stock = DB::select("SELECT tbl4.*, tbl4.balanceQty - tbl4.soldQty AS shelfQty FROM(SELECT tbl3.*, tbl3.purchasedQty+tbl3.returnedQty AS item_tot, SUM(CASE WHEN dam.deleted_at IS NULL AND dam.approved_status = 1 AND dam.from_branch = ? THEN dam.qty ELSE 0 END) AS damagedQty, ((tbl3.purchasedQty+tbl3.returnedQty+SUM(CASE WHEN dam.deleted_at IS NULL AND dam.approved_status IS NULL AND dam.from_branch = ? THEN dam.qty ELSE 0 END))-(tbl3.transferredQty+tbl3.billedQty+SUM(CASE WHEN dam.deleted_at IS NULL AND dam.from_branch = ? THEN dam.qty ELSE 0 END))) AS balanceQty FROM(SELECT tbl2.*, SUM(CASE WHEN sr.returned_branch = ? AND sr.deleted_at IS NULL THEN srd.returned_qty ELSE 0 END) AS returnedQty FROM (SELECT tbl1.*, SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL AND o.order_status NOT IN('delivered', 'cancelled') AND o.stock_updated_at IS NULL AND od.eye = 'frame' THEN od.qty ELSE 0 END) AS soldQty, SUM(CASE WHEN o.branch_id = ? AND o.deleted_at IS NULL AND o.order_status = 'delivered' AND o.stock_updated_at IS NULL THEN od.qty ELSE 0 END) AS billedQty FROM (SELECT pdct.id AS id, pdct.code AS pcode, CONCAT_WS('-', pdct.name, pdct.code) AS name, SUM(CASE WHEN t.to_branch_id = ? AND t.stock_updated_in_at IS NULL THEN td.qty ELSE 0 END) AS purchasedQty, SUM(CASE WHEN t.from_branch_id = ? AND t.stock_updated_out_at IS NULL THEN td.qty ELSE 0 END) AS transferredQty FROM transfer_details td LEFT JOIN transfers t ON t.id = td.transfer_id LEFT JOIN products pdct ON td.product_id = pdct.id WHERE IF(? > 0, td.product_id = ?, 1) AND pdct.category = ? AND t.deleted_at IS NULL AND t.transfer_status = 1 GROUP BY pdct.id, pdct.name, pdct.code) AS tbl1 LEFT JOIN order_details od ON od.product_id = tbl1.id LEFT JOIN orders o ON o.id = od.order_id GROUP BY id, name, purchasedQty, transferredQty, pcode) AS tbl2 LEFT JOIN sales_return_details srd ON tbl2.id = srd.product_id LEFT JOIN sales_returns sr ON sr.id = srd.return_id GROUP BY id, name, purchasedQty, transferredQty, pcode, billedQty, soldQty) AS tbl3 LEFT JOIN product_damages dam ON tbl3.id = dam.product_id GROUP BY id, name, purchasedQty, transferredQty, pcode, billedQty, soldQty, returnedQty HAVING balanceQty > 0 OR purchasedQty > 0 OR transferredQty > 0 OR billedQty > 0 OR soldQty > 0 OR returnedQty > 0) AS tbl4 ", [$branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $branch->id, $product, $product, $category]);
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
    else
        return false;
}

function updateLabOrderStatus($oid)
{
    LabOrder::where('order_id', $oid)->update(['status' => 'sent-to-branch']);
    recordOrderEvent($oid, "Order status has been changed to 'Sent to Branch' via Order Status Update");
}

function recordOrderEvent($oid, $action)
{
    OrderHistory::create([
        'order_id' => $oid,
        'action' => $action,
        'performed_by' => Auth::id(),
    ]);
}

function generateInvoiceForOrder(string $id)
{
    $order = Order::findOrFail(decrypt($id));
    if (!isFullyPaid($order->id, $status = 'delivered')) :
        //return redirect()->back()->with("error", "Amount due.");
        return false;
    else :
        $order->update([
            'invoice_number' => invoicenumber(decrypt($id))->ino,
            'order_sequence' => branchInvoiceNumber(),
            'invoice_generated_by' => Auth::id(),
            'invoice_generated_at' => Carbon::now(),
            'order_status' => 'delivered',
        ]);
        updateLabOrderStatus($order->id);
        recordOrderEvent($order->id, 'Invoice has been generated');
    endif;
    return true;
    // return redirect()->route('invoice.register')->with("success", "Invoice generated successfully!");
}

function getLastId($category)
{
    return DB::table('products')->selectRaw("IFNULL(MAX(CAST(SUBSTR(code, 2) AS INTEGER)), 1) AS pid")->where('category', $category)->first()->pid;
}

function cancelOrder($oid)
{
    $order = Order::findOrFail($oid);
    if ($order->order_status == 'delivered') :
        return redirect()->back()->with('error', 'Order has already been delivered!');
    elseif (!in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO'])) :
        return redirect()->back()->with('error', 'User not permitted to perform this action');
    else :
        DB::transaction(function () use ($order) {
            $credit = Payment::where('order_id', $order->id)->sum('amount');
            $order->delete();
            LabOrder::where('order_id', $order->id)->delete();
            Payment::where('order_id', $order->id)->delete();
            if ($credit > 0) :
                CustomerAccount::create([
                    'customer_id' => $order->customer_id,
                    'voucher_id' => $order->id,
                    'type' => 'credit',
                    'category' => 'order',
                    'amount' => $credit,
                    'remarks' => 'Cancelled amount credited against order number' . $order->ono(),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            endif;
        });
    endif;
    sendWAMessage($order, 'cancel');
    recordOrderEvent($oid, 'Order has been deleted / cancelled');
}

function isOrderNote($oid)
{
    return (LabOrderNote::where('order_id', $oid)->exists()) ? 'text-success fw-bold' : 'text-primary';
}

function isProductChanged($oid, $products)
{
    $details = OrderDetail::where('order_id', $oid)->whereNull('return')->whereNull('returned_qty')->get();
    $ret = false;
    foreach ($details as $key => $item) :
        if (!in_array($item->product_id, $products))
            $ret = true;
    endforeach;
    return $ret;
}

function orderUpdateType($request, $id)
{
    $msg = NULL;
    $odetail = OrderDetail::where('order_id', $id)->selectRaw("sph, cyl, axis, `add`, product_id")->get()->toArray();
    if (array_diff(array_column($odetail, 'sph'), $request->sph)) :
        $msg = "Sph ";
    endif;
    if (array_diff(array_column($odetail, 'cyl'), $request->cyl)) :
        $msg .= "Cyl ";
    endif;
    if (array_diff(array_column($odetail, 'axis'), $request->axis)) :
        $msg .= "Axis ";
    endif;
    if (array_diff(array_column($odetail, 'add'), $request->add)) :
        $msg .= "Add ";
    endif;
    if (array_diff(array_column($odetail, 'product_id'), $request->product_id)) :
        $msg .= "Product ";
    endif;
    if ($msg) :
        $msg .= "has been updated";
        Order::where('id', $id)->update(['prescription_updated_at' => Carbon::now()]);
    else :
        $msg = "Order has been Updated";
    endif;
    return $msg;
}
