<?php

namespace App\Http\Controllers;

use App\Mail\SendDocuments;
use App\Models\Branch;
use App\Models\Closing;
use App\Models\Consultation;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\LabOrderNote;
use App\Models\LoginLog;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusNote;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\ProductDamage;
use App\Models\PromotionContact;
use App\Models\PromotionSchedule;
use App\Models\Spectacle;
use App\Models\Transfer;
use App\Models\TransferDetails;
use App\Models\UserBranch;
use App\Models\Vehicle;
use App\Models\WaDocsRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;

class HelperController extends Controller
{
    protected $products;
    function __construct()
    {
        $this->middleware('permission:pending-transfer-list|pending-transfer-edit|product-damage-transfer-list|product-damage-transfer-update', ['only' => ['pendingTransfer', 'pendingTransferUpdate', 'pendingDamageTransfer', 'pendingDamageTransferUpdate']]);
        /*$this->middleware('permission:pending-transfer-list', ['only' => ['pendingTransfer']]);*/
        $this->middleware('permission:pending-transfer-edit', ['only' => ['pendingTransferEdit', 'pendingTransferUpdate']]);
        $this->middleware('permission:product-damage-transfer-list', ['only' => ['pendingDamageTransfer']]);
        $this->middleware('permission:product-damage-transfer-update', ['only' => ['pendingDamageTransferEdit', 'pendingDamageTransferUpdate']]);
        $this->middleware('permission:search-order', ['only' => ['searchOrder', 'searchOrderFetch']]);
        $this->middleware('permission:search-customer', ['only' => ['searchCustomer', 'searchCustomerFetch']]);
        $this->middleware('permission:search-prescription', ['only' => ['searchPrescription', 'searchPrescriptionFetch']]);
        $this->middleware('permission:update-delivered-order', ['only' => ['editDispatechedOrder', 'editDispatechedOrderUpdate']]);
        $this->middleware('permission:admin-dashboard', ['only' => ['adminDashboard']]);
        $this->middleware('permission:check-product-availability', ['only' => ['checkProductAvailability', 'checkProductAvailabilityFetch']]);

        $this->products = Product::selectRaw("id, CONCAT_WS('-', name, code) AS name")->pluck('name', 'id');
    }

    function getUserLocationMap(Request $request)
    {
        $login = LoginLog::findOrFail(decrypt($request->lid));
        return view('backend.user-location-map', compact('login'));
    }

    function getclosing()
    {
        $branches = Branch::where('type', 'branch')->get();
        /*foreach ($branches as $key => $branch) :
            $payments = getPaidTotalByMode(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $mode = [1]);
            $expense = getExpenseTotal(Carbon::today(), Carbon::today(), $branch->id, 1);
            $vehicle_payment_total = getVehiclePaymentTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $mode = [1]);
            $income = getIncomeTotalByMode(Carbon::today(), Carbon::today(), $branch->id, $mode = [1]);
            $bank = getBankTransferTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, null);
            $opening_balance = getOpeningBalance(Carbon::today()->subDay(), $branch->id);
            $voucher_total_receipt = getVoucherTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $type = 'receipt', $mode = [1]);
            $voucher_total_payment = getVoucherTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $type = 'payment', $mode = [1]);
            Closing::insert([
                'date' => Carbon::today(),
                'closing_balance' => ($opening_balance + $payments + $income + $voucher_total_receipt) - ($expense + $bank + $voucher_total_payment + $vehicle_payment_total),
                'branch' => $branch->id,
                'closed_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        endforeach;*/
        echo "success";
    }

    public function adminDashboard()
    {
        return view('backend.dashboard-admin');
    }

    public function billDetails(string $id)
    {
        $oid = $id / 100;
        $order = Order::findOrFail($oid);
        if ($order->invoice_number) :
            return view('backend.extras.invoice-view', compact('order'));
        endif;
    }

    public function viewArr($id)
    {
        $odetail = OrderDetail::where('order_id', $id)->selectRaw("sph, cyl, axis, `add`, product_id")->get()->toArray();
        print_r(array_column($odetail, 'sph'));
    }

    public function switchBranch($branch)
    {
        if (UserBranch::where('user_id', Auth::id())->where('branch_id', decrypt($branch))->exists()) :
            Session::put('branch', decrypt($branch));
            return redirect()->back()->with("success", "Branch switched successfully");
        else :
            return redirect()->back()->with("error", "Requested branch access denied!");
        endif;
    }

    public function updateInvoiceNumber()
    {
        $orders = Order::where('order_status', 'delivered')->orderBy('invoice_generated_at', 'ASC')->get();
        foreach ($orders as $key => $item) :
            $ino = Order::where('branch_id', $item->branch_id)->selectRaw("IFNULL(MAX(order_sequence)+1, 1) AS sid")->value('sid');
            Order::find($item->id)->update(['order_sequence' => $ino]);
        endforeach;
        echo "success";
    }

    public function transferProductBulk(string $category, $branch)
    {
        DB::transaction(function () use ($category, $branch) {
            $products = Product::where('category', $category)->get();
            $transfer = Transfer::create([
                'transfer_number' => transferId($category)->tid,
                'category' => $category,
                'transfer_date' => Carbon::today(),
                'from_branch_id' => 1000, // If branch id 1000, then treat as stock adjustment entry
                'to_branch_id' => $branch,
                'transfer_note' => "Stock Adjustment Entry",
                'transfer_status' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            $data = [];
            foreach ($products as $key => $item) :
                $data[] = [
                    'transfer_id' => $transfer->id,
                    'product_id' => $item->id,
                    'qty' => 0,
                    'batch_number' => NULL,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            endforeach;
            TransferDetails::insert($data);
        });
        echo "success";
    }

    public function closingBalance()
    {
        $payments = getPaidTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), branch()->id);
        $expense = getExpenseTotal(Carbon::today(), Carbon::today(), branch()->id, 1);
        $income = getIncomeTotal(Carbon::today(), Carbon::today(), branch()->id);
        $opening_balance = getOpeningBalance(Carbon::today()->subDay(), branch()->id);
        return [
            'payments' => $payments,
            'income' => $income,
            'expense' => $expense,
            'opening_balance' => $opening_balance,
            'closing_balance' => ($opening_balance + $payments + $income) - $expense,
        ];
    }

    public function pendingTransfer()
    {
        $transfers = Transfer::when(!in_array(Auth::user()->roles->first()->name, array('Administrator', 'CEO', 'Store Manager')), function ($q) {
            return $q->where('to_branch_id', Session::get('branch'));
        })->where('transfer_status', 0)->latest()->get();
        return view('backend.order.transfer.pending', compact('transfers'));
    }

    public function pendingTransferEdit($id)
    {
        $transfer = Transfer::findOrFail(decrypt($id));
        return view('backend.order.transfer.edit', compact('transfer'));
    }

    public function pendingTransferUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'transfer_status' => 'required',
            'remarks' => 'required',
        ]);
        Transfer::findOrFail($id)->update([
            'transfer_status' => $request->transfer_status,
            'remarks' => $request->remarks,
            'accepted_by' => $request->user()->id,
            'accepted_at' => Carbon::now(),
        ]);
        return redirect()->route('pending.transfer')->with("success", "Status updated successfully");
    }

    public function pendingDamageTransfer()
    {
        $products = ProductDamage::whereNull('approved_status')->orWhere('approved_status', 0)->latest()->get();
        return view('backend.order.damage.pending', compact('products'));
    }

    public function pendingDamageTransferEdit(string $id)
    {
        $transfer = ProductDamage::findOrFail(decrypt($id));
        return view('backend.order.damage.update', compact('transfer'));
    }

    public function pendingDamageTransferUpdate(Request $request, string $id)
    {
        $this->validate($request, [
            'approved_status' => 'required',
            'remarks' => 'required',
        ]);
        ProductDamage::findOrFail($id)->update([
            'approved_status' => $request->approved_status,
            'remarks' => $request->remarks,
            'approved_by' => $request->user()->id,
            'approved_at' => Carbon::now(),
        ]);
        return redirect()->route('pending.damage.transfer')->with("success", "Status updated successfully");
    }

    public function searchOrder()
    {
        $data = [];
        $inputs = [];
        return view('backend.search.order', compact('inputs', 'data'));
    }

    public function searchOrderFetch(Request $request)
    {
        $this->validate($request, [
            'search_term' => 'required',
        ]);
        $inputs = array($request->search_term);
        $data = Order::where('id', $request->search_term)->orWhere('mobile', $request->search_term)->orWhere('alt_mobile', $request->search_term)->orWhere('name', 'LIKE', '%' . $request->search_term . '%')->withTrashed()->get();
        return view('backend.search.order', compact('inputs', 'data'));
    }

    public function orderStatus(string $id)
    {
        $order = Order::findOrFail(decrypt($id));
        $notes = OrderStatusNote::where('order_id', $order->id)->latest()->get();
        return view('backend.order.status-update', compact('order', 'notes'));
    }

    public function searchPrescription()
    {
        $data = [];
        $inputs = [];
        return view('backend.search.prescription', compact('inputs', 'data'));
    }

    public function searchPrescriptionFetch(Request $request)
    {
        $this->validate($request, [
            'search_term' => 'required',
        ]);
        $inputs = array($request->search_term);
        $data = Spectacle::leftJoin('customers as c', 'c.id', 'spectacles.customer_id')->selectRaw('spectacles.*')->where('c.id', $request->search_term)->orWhere('c.mobile', $request->search_term)->orWhere('c.mrn', $request->search_term)->orWhere('c.name', 'LIKE', '%' . $request->search_term . '%')->withTrashed()->get();
        return view('backend.search.prescription', compact('inputs', 'data'));
    }

    public function orderStatusUpdate(Request $request, string $id)
    {
        $this->validate($request, [
            'order_status' => 'required',
        ]);
        $order = Order::findOrFail($id);
        if (!$order->order_sequence) :
            if ($request->order_status == 'cancelled') :
                cancelOrder($order->id);
            elseif (!isFullyPaid($order->id, $request->order_status)) :
                return redirect()->back()->with("error", "Amount due.");
            elseif (!Session::has('geninv') && isPendingFromLab($order->id) && in_array($request->order_status, ['ready-for-delivery', 'delivered'])) :
                //return redirect()->back()->with("error", "One or more items are pending from Lab");
                Session::put('geninv', $request->input());
                return redirect()->back()->with("warning", "One or more items are pending from Lab. Do you want to procced?");
            else :
                $ino = ($request->order_status == 'delivered') ? branchInvoiceNumber() : NULL;
                $order->update(['order_status' => $request->order_status, 'order_sequence' => $ino]);
                if ($request->status_note) :
                    OrderStatusNote::create([
                        'order_id' => $order->id,
                        'order_status' => $request->order_status,
                        'status_note' => $request->status_note,
                        'created_by' => $request->user()->id,
                    ]);
                endif;
                if (in_array($request->order_status, ['ready-for-delivery', 'delivered'])) :
                    updateLabOrderStatus($order->id);
                endif;
                recordOrderEvent($order->id, 'Order status has been updated to ' . $request->order_status . ' with notes ' . $request->status_note);
            endif;
            Session::forget('geninv');
            if ($request->order_status == 'ready-for-delivery'):
                sendWAMessage($order, 'status');
            endif;
            return redirect()->route('search.order')->with("success", "Status updated successfully");
        else :
            return redirect()->back()->with("error", "Cannot update status for the order which has invoice number already been generated");
        endif;
    }

    public function searchCustomer()
    {
        $data = [];
        $inputs = [];
        return view('backend.search.customer', compact('inputs', 'data'));
    }

    public function searchCustomerFetch(Request $request)
    {
        $this->validate($request, [
            'search_term' => 'required',
        ]);
        $inputs = array($request->search_term);
        $data = Customer::where('id', $request->search_term)->orWhere('mobile', $request->search_term)->orWhere('alt_mobile', $request->search_term)->orWhere('name', 'LIKE', '%' . $request->search_term . '%')->get();
        return view('backend.search.customer', compact('inputs', 'data'));
    }

    public function updateLabNote(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required',
            'notes' => 'required',
        ]);
        LabOrderNote::create([
            'order_id' => $request->order_id,
            'notes' => $request->notes,
            'created_by' => $request->user()->id,
        ]);
        return redirect()->route('lab.assign.orders')->with("success", "Order notes updated successfully.");
    }

    public function editDispatechedOrder()
    {
        return view('backend.order.edit-after-dispatch');
    }

    public function editDispatechedOrderFetch(Request $request)
    {
        $this->validate($request, [
            'search_term' => 'required'
        ]);
        $order = Order::whereIn('order_status', ['delivered'])->whereDate('invoice_generated_at', Carbon::today())->where('id', $request->search_term)->firstOrFail();
        return view('backend.order.edit-dispatch-proceed', compact('order'));
    }

    public function editDispatechedOrderGet(string $id)
    {
        $order = Order::findOrFail(decrypt($id));
        $payments = Payment::where('order_id', $order->id)->get();
        $pmodes = PaymentMode::pluck('name', 'id');
        return view('backend.order.edit-dispatch-update', compact('order', 'payments', 'pmodes'));
    }

    public function editDispatechedOrderUpdate(Request $request, string $id)
    {
        $this->validate($request, [
            'payment_id' => 'required',
        ]);
        try {
            $input = $request->only(array('discount', 'credit_used'));
            $input['updated_by'] = $request->user()->id;
            DB::transaction(function () use ($request, $input, $id) {
                Order::findOrFail($id)->update($input);
                foreach ($request->payment_id as $key => $item) :
                    Payment::findOrFail($item)->update([
                        'amount' => $request->amount[$key],
                        'payment_type' => $request->payment_type[$key],
                        'payment_mode' => $request->payment_mode[$key],
                        'notes' => $request->notes[$key],
                        'updated_by' => $request->user()->id,
                    ]);
                endforeach;
                recordOrderEvent($id, "Order has been updated after delivered");
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('edit.dispatched.order')->with("success", "Record updated successfully");
    }

    public function waDocs(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required|numeric|digits:10',
        ]);
        try {
            if ($request->invoice):
                $order = Order::findOrFail($request->order_id);
                $wa = sendRequestedDocviaWa($request->mobile, $order->name, $order->id, 'invoice');
                WaDocsRequest::create([
                    'doc_type' => 'invoice',
                    'doc_id' => $order->id,
                    'sent_to' => $request->mobile,
                    'sent_by' => $request->user()->id,
                ]);
            endif;
            if ($request->receipt):
                $order = Order::findOrFail($request->order_id);
                $wa = sendRequestedDocviaWa($request->mobile, $order->name, $order->id, 'receipt');
                WaDocsRequest::create([
                    'doc_type' => 'receipt',
                    'doc_id' => $order->id,
                    'sent_to' => $request->mobile,
                    'sent_by' => $request->user()->id,
                ]);
            endif;
            if ($request->prescription):
                $spectacle = Spectacle::findOrFail($request->order_id);
                $customer = Customer::findOrFail($spectacle->customer_id);
                $wa = sendRequestedDocviaWa($request->mobile, $customer->name, $spectacle->id, 'prescription');
                WaDocsRequest::create([
                    'doc_type' => 'prescription',
                    'doc_id' => $spectacle->id,
                    'sent_to' => $request->mobile,
                    'sent_by' => $request->user()->id,
                ]);
            endif;
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->back()->with("success", "Whatsapp sent successfully");
    }

    public function emailDocs(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);
        try {
            $order = Order::findOrFail($request->order_id);
            $spectacle = Spectacle::where('registration_id', $order->registration_id)->first();
            $nums = 0;
            $qrcode = null;
            $data = ['body' => $request->body, 'cname' => $order->name, 'uname' => Auth::user()->name, 'time' => Carbon::now(), 'branch' => Branch::find(Session::get('branch'))->name, 'is_invoice' => $request->invoice, 'is_receipt' => $request->receipt, 'is_prescription' => $request->prescription];
            $advance = $order->payments->where('payment_type', 'advance1')->sum('amount');
            $data['invoice'] = Pdf::loadView('backend.pdf.store-order-invoice', compact('order', 'nums', 'qrcode'));
            $data['receipt'] = Pdf::loadView('backend.pdf.store-order-receipt', compact('order', 'qrcode', 'advance'));
            $data['prescription'] = Pdf::loadView('backend.pdf.spectacle', compact('spectacle', 'qrcode'));

            Mail::to($request->email)->bcc('cssumesh@yahoo.com')->send(new SendDocuments($data));
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->back()->with("success", "Email sent successfully");
    }

    function fetchVehicle()
    {
        $vehicles = collect();
        return view("backend.extras.fetch-vehicle", compact('vehicles'));
    }

    function fetchVehicleDetails(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required|numeric|digits:10',
        ]);
        $vehicles = Vehicle::where('contact_number', $request->mobile)->latest()->get();
        return view("backend.extras.fetch-vehicle", compact('vehicles'));
    }

    function waSendPromotion(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'schedule_id' => 'required',
            'mobile' => 'required|numeric|digits:10',
        ]);
        $schedule = PromotionSchedule::findOrFail($request->schedule_id);
        sendWaPromotion($schedule, $request->name, $request->mobile);
        return redirect()->back()->with("success", "Message sent successfully");
    }

    function checkProductAvailability()
    {
        $products = $this->products;
        $branches = Branch::where('type', 'branch')->pluck('name', 'id');
        $inputs = array('', Session::get('branch'));
        $data = collect();
        return view('backend.product.check-availability', compact('branches', 'products', 'inputs', 'data'));
    }

    function checkProductAvailabilityFetch(Request $request)
    {
        $product = Product::findOrFail($request->product);
        $inputs = array($request->product, $request->branch);
        $products = $this->products;
        $branches = Branch::where('type', 'branch')->pluck('name', 'id');
        $data = getInventory($request->branch, $request->product, $product->category);
        return view('backend.product.check-availability', compact('branches', 'products', 'inputs', 'data'));
    }

    function asd()
    {
        /*$products = getInventory(Session::get('branch'), 0, 'frame')->where('balanceQty', '>', 0);
        dd($products);*/
        $promo = PromotionSchedule::whereDate('scheduled_date', Carbon::today())->where('status', 'publish')->latest()->first();
        if ($promo):
            $clist = PromotionContact::selectRaw("id, name, contact_number as mobile, 'clist' as type")->whereNull('wa_sms_status')->where('entity', $promo->entity)->where('type', 'include')->when($promo->branch_id > 0, function ($q) use ($promo) {
                return $q->where('branch_id', $promo->branch_id);
            })->orderBy('id');
            $cdata = null;
            if ($promo->entity == 'store'):
                $cdata = Order::selectRaw("id, name, mobile, 'ord' as type ")->whereNull('wa_sms_status')->when($promo->branch_id > 0, function ($q) use ($promo) {
                    return $q->where('branch_id', $promo->branch_id);
                })->whereNotIn('mobile', PromotionContact::where('type', 'exclude')->pluck('contact_number'))->limit($promo->sms_limit_per_hour)->union($clist)->orderBy('id')->get()->unique('mobile');
            endif;
            /*if ($cdata):
                $ids1 = [];
                $ids2 = [];
                foreach ($cdata as $key => $item):
                    if ($item->type == 'clist'):
                        array_push($ids1, $item->id);
                    else:
                        array_push($ids2, $item->id);
                    endif;
                //$res = sendWaPromotion($promo, $item->name, $item->mobile);
                endforeach;
                PromotionContact::whereIn('id', $ids1)->update(['wa_sms_status' => 'yes']);
                Order::whereIn('id', $ids2)->update(['wa_sms_status' => 'yes']);
            endif;*/
            $res = sendWaPromotion($promo, 'Vijo', '9188848860');
            if ($res['messages'][0]['message_status'] == 'accepted'):
                echo "true";
            else:
                echo "false";
            endif;
        endif;
    }
}
