<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    function getOrder($id, $secret)
    {
        if ($secret == apiSecret()) :
            $order = Order::find($id);
            if ($order) :
                $odetail = OrderDetail::leftJoin('products AS p', 'p.id', 'order_details.product_id')->select('order_details.id', 'p.id AS pid', 'p.name', 'order_details.qty', 'order_details.sph', 'order_details.cyl', 'order_details.axis', 'order_details.add', 'order_details.eye')->where('order_details.order_id', $order->id)->get();
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
    function getVehicle($vcode, $secret)
    {
        if ($secret == apiSecret()) :
            $vehicle = Vehicle::where('vcode', $vcode)->first();
            if ($vehicle) :
                return response()->json([
                    'status' => true,
                    'data' => $vehicle,
                    'last_payment_date' => $vehicle->payment->first()->created_at,
                    'vstatus' => htmlentities($vehicle->vstatus()),
                ], 200);
            else :
                return response()->json([
                    'status' => false,
                    'data' => "No Vehicle Found!"
                ], 404);
            endif;
        else:
            return response()->json([
                'status' => false,
                'data' => "Invalid Secret Key!"
            ], 500);
        endif;
    }
}
