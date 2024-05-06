<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatusNote;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductDamage;
use App\Models\Transfer;
use App\Models\TransferDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HelperController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:pending-transfer-list|pending-transfer-edit|product-damage-transfer-list|product-damage-transfer-update', ['only' => ['pendingTransfer', 'pendingTransferUpdate', 'pendingDamageTransfer', 'pendingDamageTransferUpdate']]);
        $this->middleware('permission:pending-transfer-list', ['only' => ['pendingTransfer']]);
        $this->middleware('permission:pending-transfer-edit', ['only' => ['pendingTransferEdit', 'pendingTransferUpdate']]);
        $this->middleware('permission:product-damage-transfer-list', ['only' => ['pendingDamageTransfer']]);
        $this->middleware('permission:product-damage-transfer-update', ['only' => ['pendingDamageTransferEdit', 'pendingDamageTransferUpdate']]);
        $this->middleware('permission:search-order', ['only' => ['searchOrder', 'searchOrderFetch']]);
        $this->middleware('permission:search-customer', ['only' => ['searchCustomer', 'searchCustomerFetch']]);
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
        $transfers = Transfer::where('to_branch_id', branch()->id)->where('transfer_status', 0)->latest()->get();
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
        $data = Order::where('id', $request->search_term)->orWhere('mobile', $request->search_term)->orWhere('alt_mobile', $request->search_term)->orWhere('name', 'LIKE', '%' . $request->search_term . '%')->get();
        return view('backend.search.order', compact('inputs', 'data'));
    }

    public function orderStatus(string $id)
    {
        $order = Order::findOrFail(decrypt($id));
        $notes = OrderStatusNote::where('order_id', $order->id)->latest()->get();
        return view('backend.order.status-update', compact('order', 'notes'));
    }

    public function orderStatusUpdate(Request $request, string $id)
    {
        $this->validate($request, [
            'order_status' => 'required',
        ]);
        $order = Order::findOrFail($id);
        if (!$order->invoice_number) :
            if (!isFullyPaid($order->id, $request->order_status)) :
                return redirect()->back()->with("error", "Amount due.");
            //elseif (isPendingFromLab($order->id)) :
            //return redirect()->back()->with("error", "One or more items pending from Lab");
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
}
