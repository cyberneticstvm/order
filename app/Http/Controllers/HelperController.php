<?php

namespace App\Http\Controllers;

use App\Mail\SendDocuments;
use App\Models\Branch;
use App\Models\Consultation;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\LabOrderNote;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusNote;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\ProductDamage;
use App\Models\Spectacle;
use App\Models\Transfer;
use App\Models\TransferDetails;
use App\Models\UserBranch;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class HelperController extends Controller
{
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
    }

    public function adminDashboard()
    {
        return view('backend.dashboard-admin');
    }

    public function billDetails(string $id)
    {
        $order = Order::findOrFail($id);
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
        $expense = getExpenseTotal(Carbon::today(), Carbon::today(), branch()->id);
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
}
