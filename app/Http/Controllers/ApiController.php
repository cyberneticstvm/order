<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    function getOrder($id)
    {
        $order = Order::find($id);
        if ($order) :
            return response()->json([
                'status' => true,
                'data' => $order
            ], 200);
        else :
            return response()->json([
                'status' => false,
                'data' => "No Order Found!"
            ], 404);
        endif;
    }
}
