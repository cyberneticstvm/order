<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\PaymentMode;
use App\Models\Voucher;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('permission:voucher-list|voucher-create|voucher-edit|voucher-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:voucher-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:voucher-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:voucher-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $vouchers = Voucher::whereDate('created_at', Carbon::today())->where('branch_id', Session::get('branch'))->latest()->get();
        return view('backend.voucher.index', compact('vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($category)
    {
        $pmodes = PaymentMode::all();
        return view('backend.voucher.create', compact('pmodes', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'payment_mode' => 'required',
        ]);
        try {
            $customer = Customer::findOrFail($request->customer_id);
            DB::transaction(function () use ($request, $customer) {
                $type = ($request->category == 'receipt') ? 'credit' : 'debit';
                $input = $request->all();
                $input['branch_id'] = Session::get('branch');
                $input['created_by'] = $request->user()->id;
                $input['updated_by'] = $request->user()->id;
                $voucher = Voucher::create($input);
                CustomerAccount::create([
                    'voucher_id' => $voucher->id,
                    'customer_id' => $customer->id,
                    'type' => $type,
                    'amount' => $request->amount,
                    'remarks' => $request->description,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('voucher')->with("success", "Voucher created successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $voucher = Voucher::findOrFail(decrypt($id));
        $pmodes = PaymentMode::all();
        return view('backend.voucher.edit', compact('pmodes', 'voucher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'payment_mode' => 'required',
        ]);
        try {
            $customer = Customer::findOrFail($request->customer_id);
            DB::transaction(function () use ($request, $customer, $id) {
                $input = $request->all();
                $input['updated_by'] = $request->user()->id;
                Voucher::findOrFail($id)->update($input);
                CustomerAccount::where('voucher_id', $id)->where('customer_id', $customer->id)->update([
                    'amount' => $request->amount,
                    'remarks' => $request->description,
                    'updated_by' => $request->user()->id,
                ]);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('voucher')->with("success", "Voucher updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Voucher::findOrFail(decrypt($id))->delete();
        CustomerAccount::where('voucher_id', decrypt($id))->delete();
        return redirect()->route('voucher')->with("success", "Voucher deleted successfully");
    }
}
