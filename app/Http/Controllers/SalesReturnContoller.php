<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SalesReturn;
use Illuminate\Http\Request;

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
            'qty' => 'required|array',
            'comment' => 'required',
        ]);
    }
}
