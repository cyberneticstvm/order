<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        $data = SalesReturn::all();
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
        $data = OrderDetail::where('order_id', $order->id)->whereNull('return')->orWhere('return', 0)->get();
        return view('backend.order.return.items', compact('data', 'order'));
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
                Customer::create([
                    'name' => $order->name,
                    'mobile' => $order->mobile,
                    'return_id' => $return->id,
                    'credit' => $tot,
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
}