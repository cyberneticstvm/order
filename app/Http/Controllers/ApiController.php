<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    function getOrder($id, $secret)
    {
        if ($secret == apiSecret()) :
            $order = Order::find($id);
            if ($order) :
                $odetail = OrderDetail::leftJoin('products AS p', 'p.id', 'order_details.product_id')->leftJoin('product_subcategories AS ps', 'ps.id', 'p.type_id')->select('order_details.id', 'p.id AS pid', 'p.name', 'order_details.qty', 'order_details.sph', 'order_details.cyl', 'order_details.axis', 'order_details.add', 'order_details.eye', 'ps.name AS ptype')->where('order_details.order_id', $order->id)->get();
                $branch = Branch::where('id', $order->branch_id)->first();
                $ftype = $order->frameType($order->id);
                return response()->json([
                    'status' => true,
                    'data' => $order,
                    'odetail' => $odetail,
                    'ftype' => $ftype,
                    'branch' => $branch,
                    'lensCount' => $order->lensCount($order->id),
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
    function getVehicle($vcode, $rc_type, $secret)
    {
        if ($secret == apiSecret()) :
            $vehicle = Vehicle::where('royalty_card_id', $rc_type)->where('vcode', $vcode)->first();
            if ($vehicle && Carbon::parse($vehicle->payment->first()?->created_at)->addHour(settings()->royalty_card_cooling_period) <= Carbon::now()) :
                return response()->json([
                    'status' => true,
                    'data' => $vehicle,
                    'last_payment_date' => $vehicle->payment->first()->created_at,
                    'vstatus' => strip_tags($vehicle->vstatus()),
                ], 200);
            elseif ($vehicle && Carbon::parse($vehicle->payment->first()?->created_at)->addHour(settings()->royalty_card_cooling_period) >= Carbon::now()):
                return response()->json([
                    'status' => true,
                    'vstatus' => 'Cooling',
                    'data' => "Royalty card under " . settings()->royalty_card_cooling_period . " Hrs Cooling Period.",
                ], 503);
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

    function getOrders($secret)
    {
        if ($secret == apiSecret()) :
            $subdomain = env('SUBDOMAIN');
            $orders = Order::leftJoin('branches AS b', 'b.id', 'orders.branch_id')->leftJoin('lab_orders AS lo', 'lo.order_id', 'orders.id')->leftJoin('order_details AS od', 'orders.id', 'od.order_id')->selectRaw("orders.id, b.code, orders.name AS customer, orders.place, DATE_FORMAT(orders.order_date, '%d.%M.%Y') AS odate")->when($subdomain != 'storesas', function ($q) {
                return $q->where('lo.lab_id', 8);
            })->whereNull('lo.deleted_at')->whereNull('lab_order_number')->whereIn('od.eye', ['re', 'le'])->groupBy('orders.id', 'b.code', 'customer', 'place', 'odate')->orderBy('lo.created_at')->get();
            return response()->json([
                'status' => true,
                'data' => $orders,
                'subdomain' => $subdomain,
            ], 200);
        else:
            return response()->json([
                'status' => false,
                'data' => "Invalid Secret Key!"
            ], 500);
        endif;
    }

    function updateLabOrder(Request $request, string $storeorderid, string $labordernumber, string $secret)
    {
        if ($secret == apiSecret()) :
            Order::where('id', $storeorderid)->update([
                'lab_order_number' => $labordernumber,
            ]);
            return response()->json([
                'status' => true,
                'message' => "success",
            ], 200);
        endif;
    }

    function updateProduct(Request $request)
    {
        $product = Product::updateOrCreate(
            ['id' => $request->id],
            $request
        );
        if ($product):
            return response()->json([
                'status' => true,
                'message' => "success",
            ], 200);
        else:
            return response()->json([
                'status' => false,
                'message' => "error",
            ], 503);
        endif;
    }
}
