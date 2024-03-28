<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\ProductDamage;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HelperController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:pending-transfer-list|pending-transfer-edit|product-damage-transfer-list|product-damage-transfer-update', ['only' => ['pendingTransfer', 'pendingTransferUpdate', 'pendingDamageTransfer', 'pendingDamageTransferUpdate']]);
        $this->middleware('permission:pending-transfer-list', ['only' => ['pendingTransfer']]);
        $this->middleware('permission:pending-transfer-edit', ['only' => ['pendingTransferEdit', 'pendingTransferUpdate']]);
        $this->middleware('permission:product-damage-transfer-list', ['only' => ['pendingDamageTransfer']]);
        $this->middleware('permission:product-damage-transfer-update', ['only' => ['pendingDamageTransferEdit', 'pendingDamageTransferUpdate']]);
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

    public function search()
    {
        $inputs = [];
        $data = [];
        return view('backend.search.index', compact('inputs', 'data'));
    }

    public function searchFetch(Request $request)
    {
        $this->validate($request, [
            'search_by' => 'required',
            'search_term' => 'required',
        ]);
        $inputs = array($request->search_by, $request->search_term);
        switch ($request->search_by):
            case 'mrn':
                $con = Consultation::with('patient')->findOrFail($request->search_term);
                $data = Patient::with('consultation')->where('id', $con->patient->id)->withTrashed()->get();
                break;
            case 'mobile':
                $data = Patient::with('consultation')->where('mobile', $request->search_term)->withTrashed()->get();
                break;
            case 'pid':
                $data = Patient::with('consultation')->where('id', $request->search_term)->withTrashed()->get();
                break;
            case 'pname':
                $data = Patient::with('consultation')->where('name', $request->search_term)->withTrashed()->get();
                break;
            default:
                $data = [];
        endswitch;
        return view('backend.search.index', compact('inputs', 'data'));
    }
}
