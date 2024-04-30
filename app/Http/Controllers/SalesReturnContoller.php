<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Purchase;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SalesReturnContoller extends Controller
{
    function __construct()
    {
        $this->middleware('permission:sales-return-list|sales-return-create|sales-return-edit|sales-return-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:sales-return-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales-return-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales-return-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $data = SalesReturn::latest()->get();
        return view('backend.order.return.index', compact('data'));
    }
    public function fetch(Request $request)
    {
        $this->validate($request, [
            'query_string' => 'required|numeric',
        ]);
        $data = Order::where('id', $request->query_string)->orWhere('mobile', $request->query_string)->get();
        return view('backend.order.return.list', compact('data'));
    }
    public function list($id)
    {
        $order = Order::findOrFail(decrypt($id));
        if ($order->invoice_number) :
            $data = OrderDetail::where('order_id', $order->id)->whereNull('return')->orWhere('return', 0)->get();
            return view('backend.order.return.items', compact('data', 'order'));
        else :
            return redirect()->back()->with("error", "Cannot proceed with return since the invoice has yet to be generated.");
        endif;
    }
    public function store(Request $request, string $id)
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);
        try {
            DB::transaction(function () use ($request, $id) {
                $order = Order::findOrFail($id);
                $returns = [];
                $tot = 0;
                $input = $request->except(array('qty', 'oqty', 'odid', 'pid', 'amount'));
                $input['order_id'] = $order->id;
                $input['order_branch'] = $order->branch_id;
                $input['returned_branch'] = Session::get('branch');
                $input['created_by'] = $request->user()->id;
                $input['updated_by'] = $request->user()->id;
                $return = SalesReturn::create($input);
                foreach ($request->qty as $key => $item) :
                    if ($item > 0) :
                        $returns[] = [
                            'return_id' => $return->id,
                            'product_id' => $request->pid[$key],
                            'order_qty' => $request->oqty[$key],
                            'returned_qty' => $item,
                            'returned_amount' => $request->amount[$key],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                        OrderDetail::findOrFail($request->odid[$key])->update([
                            'return' => 1,
                            'returned_qty' => $item,
                        ]);
                        $tot += $request->amount[$key];
                    endif;
                endforeach;
                SalesReturnDetail::insert($returns);
                CustomerAccount::create([
                    'customer_id' => $order->customer_id,
                    'voucher_id' => $order->id,
                    'type' => 'credit',
                    'category' => 'order',
                    'amount' => $tot,
                    'remarks' => "Sales return against order ID" . $order->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput($request->all());
        }
        return redirect()->route('sales.return')->with("success", "Return updated successfully");
    }

    public function show(string $id)
    {
        $sreturn = SalesReturn::findOrFail(decrypt($id));
        return view('backend.order.return.details', compact('sreturn'));
    }

    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $return = SalesReturn::findOrFail(decrypt($id));
            $return->delete();
            CustomerAccount::where('voucher_id', $return->order_id)->where('type', 'credit')->where('category', 'order')->delete();
            $details = SalesReturnDetail::where('return_id', $return->id)->get();
            foreach ($details as $key => $item) :
                OrderDetail::where('order_id', $return->order_id)->where('product_id', $item->product_id)->update(['return' => NULL, 'returned_qty' => NULL]);
            endforeach;
            SalesReturnDetail::where('return_id', $return->id)->delete();
        });
        return redirect()->back()->with("success", "Return deleted successfully");
    }
}
