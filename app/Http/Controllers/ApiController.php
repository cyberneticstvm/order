<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    function getOrder($id, $secret)
    {
        if ($secret == apiSecret()) :
            $order = Order::find($id);
            if ($order) :
                $odetail = OrderDetail::where('order_id', $order->id)->get();
                $branch = Branch::where('id', $order->branch_id)->first();
                return response()->json([
                    'status' => true,
                    'data' => $order,
                    'odetail' => $odetail,
                    'branch' => $branch,
                ], 200);
            else :
                return response()->json([
                    'status' => false,
                    'data' => "No Order Found!"
                ], 404);
            endif;
        else :
            return response()->json([
                'status' => false,
                'data' => "Invalid Secret Key!"
            ], 500);
        endif;
    }
}
