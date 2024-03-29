<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Order;
use App\Models\PatientProcedure;
use App\Models\Payment;
use App\Models\Power;
use App\Models\Product;
use App\Models\ProductSubcategory;
use App\Models\PurchaseDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AjaxController extends Controller
{

    function __construct()
    {
        // 
    }

    public function getAppointmentTime(Request $request)
    {
        $arr = getAppointmentTimeList($request->date, $request->doctor_id, $request->branch_id);
        return response()->json($arr);
    }

    public function getProductsByCategory($category)
    {
        $products = Product::where('category', $category)->selectRaw("id, CONCAT_WS('-', name, code) AS name")->orderBy('name')->get();
        return response()->json($products);
    }

    public function getProductsByType($type)
    {
        $products = Product::where('type_id', $type)->orderBy('name')->get();
        return response()->json($products);
    }

    public function getProductPrice($pid, $category, $batch)
    {
        if ($category == 'pharmacy') :
            $product = PurchaseDetail::selectRaw("unit_price_sales AS selling_price")->where('product_id', $pid)->where('batch_number', $batch)->firstOrFail();
        else :
            $product = Product::findOrFail($pid);
        endif;
        return response()->json($product);
    }

    public function getProductBatch($branch, $product, $category)
    {
        $stock = getInventory($branch, $product, $category);
        return response()->json($stock);
    }

    public function getProductTypes($category, $attribute)
    {
        $types = ProductSubcategory::where('category', $category)->where('attribute', $attribute)->orderBy('name')->get();
        return response()->json($types);
    }

    public function powers()
    {
        $sph = Power::where('name', 'sph')->selectRaw("value as id, value as name")->get();
        $cyl = Power::where('name', 'cyl')->selectRaw("value as id, value as name")->get();
        $axis = Power::where('name', 'axis')->selectRaw("value as id, value as name")->get();
        $add = Power::where('name', 'add')->selectRaw("value as id, value as name")->get();
        $intad = Power::where('name', 'intad')->selectRaw("value as id, value as name")->get();
        return response()->json([
            'sph' => $sph,
            'cyl' => $cyl,
            'axis' => $axis,
            'add' => $add,
            'intad' => $intad
        ]);
    }

    public function getPaymentDetailsByConsultation($cid)
    {
        $arr = json_decode(owedTotal($cid));
        $tot = $arr->registration_fee + $arr->consultation_fee + $arr->procedure_fee + $arr->pharmacy + $arr->store;
        $paid = Payment::where('consultation_id', $cid)->sum('amount');
        $bal = number_format($tot - $paid, 2);
        $op = "<table class='table table-bordered table-striped'><thead><th>Service Opted</th><th>Amount</th></thead><tbody>";
        $op .= "<tr><td>Registration Fee</td><td class='text-end fw-bold'>$arr->registration_fee</td></tr>";
        $op .= "<tr><td>Consultation Fee</td><td class='text-end fw-bold'>$arr->consultation_fee</td></tr>";
        $op .= "<tr><td>Procedure Fee</td><td class='text-end fw-bold'>$arr->procedure_fee</td></tr>";
        $op .= "<tr><td>Pharmacy</td><td class='text-end fw-bold'>$arr->pharmacy</td></tr>";
        $op .= "<tr><td>Store</td><td class='text-end fw-bold'>$arr->store</td></tr>";
        $op .= "</tbody><tfoot><tr><td class='text-end fw-bold'>Total</td><td class='text-end fw-bold'>$tot</td></tr><tr><td class='text-end fw-bold'>Paid</td><td class='text-end fw-bold'>$paid</td></tr><tr><td class='text-end fw-bold'>Balance</td><td class='text-end fw-bold'>$bal</td></tr></table>";
        echo $op;
    }

    public function getDaybookDetailed(Request $request)
    {
        $op = "";
        $fdate = Carbon::parse($request->from_date)->startOfDay();
        $tdate = Carbon::parse($request->to_date)->endOfDay();
        switch ($request->type):
            case 'ord':
                //$data = getOrderDetailed($request->from_date, $request->to_date, $request->branch);
                $data = Payment::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->get();
                $op = '<div class="drawer-header">
                <h6 class="drawer-title" id="drawer-3-title">Order Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Customer Name</th><th>
                Date<th>Order ID</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->order->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->order->branch->code . '/' . $item->order_id . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="4" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Daybook</div>';
                break;
            default:
                $op = "No records found";
        endswitch;
        echo $op;
    }
}
