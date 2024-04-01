<?php

namespace App\Http\Controllers;

use App\Models\BankTransfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BankTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:bank-transfer-list|bank-transfer-create|bank-transfer-edit|bank-transfer-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:bank-transfer-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:bank-transfer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:bank-transfer-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $btransfers = BankTransfer::withTrashed()->where('branch_id', Session::get('branch'))->whereDate('created_at', Carbon::today())->get();
        return view('backend.bank-transfer.index', compact('btransfers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.bank-transfer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
        ]);
        $input = $request->all();
        $input['branch_id'] = Session::get('branch');
        $input['created_by'] = $request->user()->id;
        $input['updated_by'] = $request->user()->id;
        BankTransfer::create($input);
        return redirect()->route('bank.transfers')->with("success", "Transfer recorded successfully");
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
        $btransfer = BankTransfer::findOrFail(decrypt($id));
        return view('backend.bank-transfer.edit', compact('btransfer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'amount' => 'required',
        ]);
        $input = $request->all();
        $input['updated_by'] = $request->user()->id;
        BankTransfer::findOrFail($id)->update($input);
        return redirect()->route('bank.transfers')->with("success", "Transfer updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        BankTransfer::findOrFail(decrypt($id))->delete();
        return redirect()->route('bank.transfers')->with("success", "Transfer deleted successfully");
    }
}
