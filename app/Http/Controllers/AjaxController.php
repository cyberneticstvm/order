<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BankTransfer;
use App\Models\Consultation;
use App\Models\IncomeExpense;
use App\Models\LabOrderNote;
use App\Models\Month;
use App\Models\OfferCategory;
use App\Models\OfferProduct;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PatientProcedure;
use App\Models\Payment;
use App\Models\Power;
use App\Models\Product;
use App\Models\ProductSubcategory;
use App\Models\PurchaseDetail;
use App\Models\RoyaltyCardSetting;
use App\Models\Spectacle;
use App\Models\Transfer;
use App\Models\TransferDetails;
use App\Models\Vehicle;
use App\Models\VehiclePayment;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    public function content($branch, $oid)
    {
        $existing = OfferProduct::where('branch_id', $branch)->where('offer_category_id', $oid)->orderByDesc('id')->get();
        $tbl = "<table class='table table-bordered tblPdct'><thead><tr><th>SL No.</th><th>Product</th><th>Remove</th></tr></thead><tbody>";
        foreach ($existing as $key => $item):
            $tbl .= "<tr>";
            $tbl .= "<td>" . $key + 1 . "</td>";
            $tbl .= "<td>" . $item->product->name . "</td>";
            $tbl .= "<td class='text-center'><a href='/ajax/offer/product/remove/' class='dltOfferPdct' data-pid='" . $item->id . "'><i class='fa fa-trash text-danger fa-lg'></i></a></td>";
            $tbl .= "</tr>";
        endforeach;
        $tbl .= "</tbody></table>";
        return $tbl;
    }

    public function removeOfferProduct(Request $request)
    {
        $pdct = OfferProduct::findOrFail($request->pid);
        $pdct->forceDelete();
        return response()->json([
            'msg' => 'Product removed successfully!',
            'type' => 'success',
        ]);
    }

    public function getProductsForOffer(Request $request)
    {
        $offer = OfferCategory::findOrFail($request->oid);
        $existing = OfferProduct::where('branch_id', $offer->branch_id)->where('offer_category_id', $offer->id)->get();
        $products = Product::whereIn('category', ['frame'])->whereNotIn('id', $existing->pluck('product_id'))->selectRaw("id, CONCAT_WS('-', name, code) AS name")->orderBy('name')->get();
        $tbl = $this->content($offer->branch_id, $offer->id);
        return response()->json([
            'offer' => $offer,
            'products' => $products,
            'content' => $tbl,
        ]);
    }

    public function validateRoyaltyCard(Request $request)
    {
        $rctype = $request->ctype;
        $rcnumber = $request->cnumber;
        $order_total = $request->order_total;
        $products = $request->products;
        $pdctTotal = $request->pdctTotal;
        $type = ProductSubcategory::find($rctype)->name;
        $card = null;
        $discount = 0;
        if ($type == 'Vehicle'):
            $card = Vehicle::where('vcode', $rcnumber)->first();
            foreach ($products as $key => $item):
                if ($item != ''):
                    $product = Product::find($item);
                    $discSetting = RoyaltyCardSetting::where('category', $product->category)->where('card_id', $rctype)->first();
                    $taxAmount = $product->taxamount($pdctTotal[$key]);
                    $pdctValue = $pdctTotal[$key] - $taxAmount;
                    $discount += ($pdctValue > 0 && $discSetting?->discount_percentage) ? ($pdctValue * $discSetting?->discount_percentage) / 100 : 0;
                endif;
            endforeach;
        endif;
        if ($card && Carbon::parse($card->payment->first()?->created_at)->addHour(settings()->royalty_card_cooling_period) >= Carbon::now()):
            return response()->json([
                'message' => "Royalty card under " . settings()->royalty_card_cooling_period . " Hrs Cooling Period.",
                'type' => 'warning',
                'discount' => 0,
            ]);
        elseif ($card && $discount == 0):
            return response()->json([
                'message' => 'No discount applicable for provided card!',
                'type' => 'warning',
                'discount' => $discount,
            ]);
        elseif ($card && $discount):
            return response()->json([
                'message' => 'Royalty Card Validated Successfully! ',
                'type' => 'success',
                'discount' => getFloorVal($discount, 10),
            ]);
        else:
            return response()->json([
                'message' => 'Invalid Card!',
                'type' => 'error',
                'discount' => 0,
            ]);
        endif;
    }

    public function saveProductForOffer(Request $request)
    {
        $offer = OfferCategory::findOrFail($request->oid);
        if ($request->pid):
            OfferProduct::insert([
                'offer_category_id' => $offer->id,
                'branch_id' => $offer->branch_id,
                'product_id' => $request->pid,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $tbl = $this->content($offer->branch_id, $offer->id);
            return response()->json([
                'msg' => 'Product added successfully!',
                'type' => 'success',
                'content' => $tbl,
            ]);
        else:
            return response()->json([
                'msg' => 'Please select a product',
                'type' => 'error',
            ]);
        endif;
    }

    public function getOfferedProducts($pid)
    {
        $products = NULL;
        $item = OfferProduct::where('product_id', $pid)->where('branch_id', Session::get('branch'))->first();
        if ($item):
            $offer = OfferCategory::where('branch_id', Session::get('branch'))->whereDate('valid_from', '<=', Carbon::today())->whereDate('valid_to', '>=', Carbon::today())->where('id', $item->offer_category_id)->where('buy_number', '>', 0)->where('get_number', '>', 0)->first();
            if ($offer):
                $pdcts = OfferProduct::where('offer_category_id', $offer->id)->pluck('product_id');
                $products = Product::whereIn('category', ['frame'])->whereIn('id', $pdcts)->selectRaw("id, CONCAT_WS('-', name, code) AS name")->orderBy('name')->get();
            endif;
        endif;
        return array('products' => $products, 'getnumber' => $offer?->get_number ?? 0);
    }

    public function getOfferProducts($pid)
    {
        $products = $this->getOfferedProducts($pid)['products'] ?? NULL;
        $discount = 0;
        $get_number = $this->getOfferedProducts($pid)['getnumber'];
        $item = OfferProduct::where('product_id', $pid)->where('branch_id', Session::get('branch'))->first();
        if ($item):
            $product = Product::find($pid);
            $offer = OfferCategory::where('branch_id', Session::get('branch'))->whereDate('valid_from', '<=', Carbon::today())->whereDate('valid_to', '>=', Carbon::today())->where('id', $item->offer_category_id)->where('discount_percentage', '>', 0)->first();
            if ($offer && $offer->discount_percentage > 0 && $product->selling_price > 0):
                $discount = ($product->selling_price * $offer->discount_percentage) / 100;
            endif;
        endif;
        return response()->json([
            'get_number' => $get_number,
            'products' => $products,
            'discount' => $discount,
            'item' => $item,
        ]);
    }

    public function getProductsByCategory($category, $type, $product = 0)
    {
        $products = Product::where('category', $category)->selectRaw("id, CONCAT_WS('-', name, code) AS name")->orderBy('name')->get();
        if (($category == 'frame' || $category == 'solution' || $category == 'accessory') && ($type == 'order' || $type == 'transfer') && $product == 0):
            $products = getInventory(Session::get('branch'), 0, $category)->where('balanceQty', '>', 0);
        endif;
        if ($product > 0):
            $products = $this->getOfferedProducts($product)['products'];
        endif;
        return response()->json([
            'products' => $products,
        ]);
    }

    public function getProductsByType($type)
    {
        $products = Product::where('type_id', $type)->orderBy('name')->get();
        return response()->json($products);
    }

    public function getPrescription($source, $val)
    {
        if ($source == 'hospital') :
            /*$spectacle = DB::connection('mysql1')->table('spectacles')->leftJoin('users as u', 'u.id', 'spectacles.created_by')->selectRaw("re_dist_sph as re_sph, re_dist_cyl as re_cyl, re_dist_axis as re_axis, re_dist_add as re_add, re_dist_va as re_va, rpd as re_pd, lpd as le_pd, le_dist_sph as le_sph, le_dist_cyl as le_cyl, le_dist_axis as le_axis, le_dist_add as le_add, le_dist_va as le_va, re_int_add, le_int_add, '' as a_size, '' as b_size, '' as dbl, '' as fh, '' as ed, '' as vd, '' as w_angle, 0 as doctor, u.name as optometrist")->where('spectacles.id', $val)->first();*/
            $secret = apiSecret();
            $url = api_url() . "/api/prescription/" . $val . "/" . $secret;
            $json = file_get_contents($url);
            $data = json_decode($json);
            $spectacle = $data->spectacle;
        else :
            $spectacle = Spectacle::findOrFail($val);
        endif;
        return response()->json($spectacle);
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

    public function getAvailableCredit($cid)
    {
        $cr = getAvailableCredit($cid);
        return response()->json($cr);
    }

    public function powers()
    {
        $sph = Power::where('name', 'sph')->selectRaw("value as name")->get();
        $cyl = Power::where('name', 'cyl')->selectRaw("value as name")->get();
        $axis = Power::where('name', 'axis')->selectRaw("value as name")->get();
        $add = Power::where('name', 'add')->selectRaw("value as name")->get();
        $intad = Power::where('name', 'intad')->selectRaw("value as name")->get();
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

    public function getFrameDetailed(Request $request)
    {
        $op = "";
        $c = 1;
        $fdate = Carbon::parse($request->from_date)->startOfDay();
        $tdate = Carbon::parse($request->to_date)->endOfDay();
        $order = Order::whereBetween(($request->status == 'delivered') ? 'invoice_generated_at' : 'created_at', [$fdate, $tdate])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->when($request->status, function ($q) use ($request) {
            return $q->where('order_status', $request->status);
        })->get();
        $op = '<div class="drawer-header">
                <h6 class="drawer-title" id="drawer-3-title">Frame Detailed</h6></div><div class="drawer-body table-responsive">';
        $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Order</th><th>Product Name</th><th>Product Code</th></tr></thead><tbody>';
        foreach ($order as $key1 => $ord) :
            foreach ($ord->details->where('eye', 'frame') as $key => $item) :
                $op .= "<tr>";
                $op .= '<td>' . $c++ . '</td>';
                $op .= '<td>' . $ord->ono() . '</td>';
                $op .= '<td>' . $item->product?->name . '</td>';
                $op .= '<td>' . $item->product?->code . '</td>';
                $op .= "</tr>";
            endforeach;
        endforeach;
        $op .= '</tbody></table>';
        $op .= '</div><div class="drawer-footer">Frame</div>';
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
                })->when(in_array($request->mode, array(5)), function ($q) use ($request) {
                    return $q->whereIn('payment_mode', [5, 6, 7]);
                })->when(in_array($request->mode, array(1, 2, 3, 4)), function ($q) use ($request) {
                    return $q->where('payment_mode', $request->mode);
                })->get();
                $op = '<div class="drawer-header">
                <h6 class="drawer-title" id="drawer-3-title">Order Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Customer Name</th><th>
                Date<th>Order ID</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->order?->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->order?->branch?->code . '/' . $item->order_id . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="4" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Daybook</div>';
                break;
            case 'advance':
                //$data = getOrderDetailed($request->from_date, $request->to_date, $request->branch);
                $data = Payment::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->when(in_array($request->mode, array('advance')), function ($q) use ($request) {
                    return $q->whereIn('payment_type', ['advance']);
                })->when(in_array($request->mode, array('advance1')), function ($q) use ($request) {
                    return $q->whereIn('payment_type', ['advance1']);
                })->when(in_array($request->mode, array('other')), function ($q) use ($request) {
                    return $q->whereIn('payment_type', ['partial', 'balance']);
                })->get();
                $op = '<div class="drawer-header">
                    <h6 class="drawer-title" id="drawer-3-title">Advance & Receipts Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Customer Name</th><th>
                    Date<th>Order ID</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->order?->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->order?->branch?->code . '/' . $item->order_id . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="4" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Advance & Receipts</div>';
                break;
            case 'income':
                $data = IncomeExpense::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->where('category', 'income')->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->when(in_array($request->mode, array(5)), function ($q) use ($request) {
                    return $q->whereIn('payment_mode', [5, 6, 7]);
                })->when(in_array($request->mode, array(1, 2, 3, 4)), function ($q) use ($request) {
                    return $q->where('payment_mode', $request->mode);
                })->get();
                $op = '<div class="drawer-header">
                <h6 class="drawer-title" id="drawer-3-title">Income Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Branch Name</th><th>
                Head</th><th>Date</th><th>Description</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->branch->name . '</td>';
                    $op .= '<td>' . $item->head->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->description . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="5" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Income</div>';
                break;
            case 'expense':
                $data = IncomeExpense::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->where('category', 'expense')->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->get();
                $op = '<div class="drawer-header">
                    <h6 class="drawer-title" id="drawer-3-title">Expense Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Branch Name</th><th>
                    Head</th><th>Date</th><th>Description</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->branch->name . '</td>';
                    $op .= '<td>' . $item->head->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->description . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="5" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Expense</div>';
                break;
            case 'bank':
                $data = BankTransfer::whereBetween('created_at', [$fdate, $tdate])->where('amount', '>', 0)->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->get();
                $op = '<div class="drawer-header">
                        <h6 class="drawer-title" id="drawer-3-title">Bank Transfer Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Branch Name</th><th>Date</th><th>Notes</th><th>Type</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->branch->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y h:i a') . '</td>';
                    $op .= '<td>' . $item->notes . '</td>';
                    $op .= '<td>' . $item->type . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="5" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Bank Transfer</div>';
                break;
            case 'order':
                $order = Order::withTrashed()->where('id', $request->mode)->firstOrFail();
                $op = '<div class="drawer-header">
                        <h6 class="drawer-title" id="drawer-3-title">Order Detail</h6></div><div class="drawer-body table-responsive">';
                $op .= "<table><tbody>";
                $op .= "<tr><th>Order ID</th><td>" . $order->ono() . "</td></tr>";
                $op .= "<tr><th>Customer ID</th><td>" . $order->customer_id . "</td></tr>";
                $op .= "<tr><th>Customer Name</th><td>" . $order->name . "</td></tr>";
                $op .= "<tr><th>Branch</th><td>" . $order->branch->name . "</td></tr>";
                $op .= "<tr><th>Order Date</th><td>" . $order->created_at->format('d.M.Y h:i a') . "</td></tr>";
                $op .= "<tr><th>Exp.Del.Date</th><td>" . $order->expected_delivery_date->format('d.M.Y') . "</td></tr>";
                $op .= "<tr><th>Order Note</th><td>" . $order->order_note . "</td></tr>";
                $op .= "<tr><th>Invoice Note</th><td>" . $order->invoice_note . "</td></tr>";
                $op .= "<tr><th>Lab Note</th><td>" . $order->lab_note . "</td></tr>";
                $op .= "<tr><th>Special Lab Note</th><td>" . $order->special_lab_note . "</td></tr>";
                $op .= "<tr><th>W Angle</th><td>" . $order->w_angle . "</td></tr>";
                $op .= "<tr><th><br></th><td></td></tr>";
                $op .= "</tbody></table>";
                $op .= "<h5 class='mb-3'>Values</h5>";
                $op .= "<table class='table table-bordered'><thead><th>Int. Add</th><th>A</th><th>B</th><th>DBL</th><th>FH</th><th>ED</th><th>VD</th><th>LPD</th><th>RPD</th></thead><tbody>";
                $op .= "<tr><td>" . $order->int_add . "</td><td>" . $order->a_size . "</td><td>" . $order->b_size . "</td><td>" . $order->dbl . "</td><td>" . $order->fh . "</td><td>" . $order->ed . "</td><td>" . $order->vd . "</td><td>" . $order->lpd . "</td><td>" . $order->rpd . "</td></tr>";
                $op .= "</tbody></table>";
                $op .= "<h5 class='mt-3 mb-3'>Prescription</h5>";
                $op .= "<table class='table table-bordered'><thead><th>Eye</th><th>Product</th><th>Qty</th><th>Sph</th><th>Cyl</th><th>Axis</th><th>Add</th><th>pd</th><th>Price</th></thead><tbody>";
                foreach ($order->details as $key => $item) :
                    $op .= "<tr><td>" . strtoupper($item->eye) . "</td><td>" . $item->product->name . "</td><td>" . $item->qty . "</td><td>" . $item->sph . "</td><td>" . $item->cyl . "</td><td>" . $item->axis . "</td><td>" . $item->add . "</td><td>" . $item->ipd . "</td><td>" . $item->unit_price . "</td></tr>";
                endforeach;
                $op .= "</tbody></table>";
                $op .= "<h5 class='mt-3 mb-3'>Order History</h5>";
                $op .= "<table class='table table-bordered'><thead><th>Action</th><th>Performed at</th><th>Performed by</th></thead><tbody>";
                foreach ($order->history as $key => $item) :
                    $op .= "<tr><td>" . $item->action . "</td><td>" . $item->created_at->format('d.M.Y h:i a') . "</td><td>" . $item->user->name . "</td></tr>";
                endforeach;
                $op .= "</tbody></table>";
                $op .= "<h5 class='mt-3 mb-3'>Lab Order Notes</h5>";
                $op .= "<table class='table table-bordered'><thead><th>Notes</th><th>Written by</th></thead><tbody>";
                foreach ($order->labNotes as $key => $item) :
                    $op .= "<tr><td>" . $item->notes . "</td><td>" . $item->user->name . "</td></tr>";
                endforeach;
                $op .= "</tbody></table>";
                $op .= '</div><div class="drawer-footer">Order Detail</div>';
                break;
            case 'voucher':
                $data = Voucher::whereBetween('created_at', [$fdate, $tdate])->where('category', 'payment')->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->get();
                $op = '<div class="drawer-header">
                        <h6 class="drawer-title" id="drawer-3-title">Voucher Payment Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Customer</th><th>Branch Name</th><th>Date</th><th>Description</th><th>P.mode</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->customer->name . '</td>';
                    $op .= '<td>' . $item->branch->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y h:i a') . '</td>';
                    $op .= '<td>' . $item->description . '</td>';
                    $op .= '<td>' . $item->paymentmode->name . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="6" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Voucher Payments</div>';
                break;
            case 'ads':
                $data = VehiclePayment::whereBetween('created_at', [$fdate, $tdate])->when($request->branch > 0, function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch);
                })->get();
                $op = '<div class="drawer-header">
                        <h6 class="drawer-title" id="drawer-3-title">Vehicle Payment Detailed</h6></div><div class="drawer-body table-responsive">';
                $op .= '<table class="table table-bordered table-striped"><thead><tr><th>SL No</th><th>Vehicle Number</th><th>Branch Name</th><th>Date</th><th>Notes</th><th>P.mode</th><th>Amount</th></tr></thead><tbody>';
                foreach ($data as $key => $item) :
                    $op .= "<tr>";
                    $op .= '<td>' . $key + 1 . '</td>';
                    $op .= '<td>' . $item->vehicle->reg_number . '</td>';
                    $op .= '<td>' . $item->branch->name . '</td>';
                    $op .= '<td>' . $item->created_at->format('d, M Y') . '</td>';
                    $op .= '<td>' . $item->notes . '</td>';
                    $op .= '<td>' . $item->paymentmode->name . '</td>';
                    $op .= '<td class="text-end">' . number_format($item->amount, 2) . '</td>';
                    $op .= "</tr>";
                endforeach;
                $op .= '</tbody><tfoot><tr><td colspan="6" class="text-end fw-bold">Total</td><td class="text-end fw-bold">' . number_format($data->sum('amount'), 2) . '</td></tr></tfoot></table>';
                $op .= '</div><div class="drawer-footer">Ad Payments</div>';
                break;
            default:
                $op = "No records found";
        endswitch;
        echo $op;
    }

    public function getOrderData()
    {
        $orders = Month::leftJoin('orders as o', function ($q) {
            $q->on('o.created_at', '>=', DB::raw('LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH'));
            $q->on('o.created_at', '<', DB::raw('LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH + INTERVAL 1 MONTH'))->where('o.branch_id', Session::get('branch'));
        })->select(DB::raw("LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH AS date, COUNT(CASE WHEN o.deleted_at IS NULL THEN o.id END) AS order_count, CONCAT_WS('/', DATE_FORMAT(LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH, '%b'), DATE_FORMAT(LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH, '%y')) AS month"))->groupBy('date', 'months.id')->orderByDesc('date')->get();
        return json_encode($orders);
    }

    public function getOrderComparisonData($bid)
    {
        $bid = ($bid > 0) ? $bid : Session::get('branch');
        $orders = Month::leftJoin('orders as o', function ($q) use ($bid) {
            $q->on('o.order_date', '>=', DB::raw('LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH'));
            $q->on('o.order_date', '<', DB::raw('LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH + INTERVAL 1 MONTH'))->where('o.branch_id', $bid);
        })->select(DB::raw("LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH AS date, COUNT(CASE WHEN o.deleted_at IS NULL THEN o.id END) AS order_count, COUNT(CASE WHEN order_status = 'delivered' THEN o.id END) AS dcount, COUNT(CASE WHEN o.deleted_at IS NULL THEN o.id END) - COUNT(CASE WHEN order_status = 'delivered' THEN o.id END) AS bcount, CONCAT_WS('/', DATE_FORMAT(LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH, '%b'), DATE_FORMAT(LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL months.id MONTH, '%y')) AS month"))->groupBy('date', 'months.id')->orderByDesc('date')->get();
        return json_encode([
            'ord' => $orders,
        ]);
    }

    public function getSalesComparisonData($bid, $month, $year)
    {
        $bid = ($bid > 0) ? $bid : Session::get('branch');
        $month = ($month > 0) ? $month : date('m');
        $year = ($year > 0) ? $year : date('Y');
        $order = DB::select("SELECT SUM(tbl2.invoice_total) AS invtot, SUM(tbl2.advance) AS advance, SUM(tbl2.invoice_total)-SUM(tbl2.advance) AS balance FROM (SELECT DISTINCT(tbl1.id) AS oid, tbl1.invoice_total, SUM(p.amount) AS advance FROM (SELECT o.id, o.invoice_total FROM orders o WHERE o.branch_id = ? AND MONTH(o.created_at) = ? AND YEAR(o.created_at) = ?) AS tbl1 LEFT JOIN payments p ON p.order_id = tbl1.id GROUP BY oid, invoice_total) AS tbl2", [$bid, $month, $year]);
        return json_encode($order);
    }

    public function getBranchPerformance()
    {
        $branches = Branch::where('type', 'branch')->get();
        $data = array();
        foreach ($branches as $Key => $item):
            $tot = (unpaidTotal($item->id, 0, 0, 0)->invtot > 0) ? unpaidTotal($item->id, 0, 0, 0)->invtot : 1;
            $advance = (unpaidTotal($item->id, 0, 0, 0)->advance > 0) ? unpaidTotal($item->id, 0, 0, 0)->advance : 1;
            $balance = (unpaidTotal($item->id, 0, 0, 0)->balance > 0) ? unpaidTotal($item->id, 0, 0, 0)->balance : 1;
            $bal_per = ($advance / $tot) * 100;
            array_push($data, array('branch' => $item->name, 'balance' => $bal_per, 'total' => $tot, 'advance' => $advance));
        endforeach;
        return json_encode($data);
    }

    public function getBranches($id)
    {
        return Branch::when($id > 0, function ($q) use ($id) {
            return $q->where('id', $id);
        })->get();
    }

    public function checkPendingTransfer(Request $request)
    {
        $transfer = Transfer::when(!in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager']), function ($q) {
            return $q->where('to_branch_id', Session::get('branch'));
        })->where('transfer_status', 0);
        return response()->json([
            'message' => $transfer->exists() && $request->user()->hasPermissionTo('pending-transfer-list') ? "You have some pending transfers to accept. Please accept it asap." : "",
        ]);
    }

    public function getLabNote(string $oid)
    {
        $notes = LabOrderNote::where('order_id', $oid)->get();
        $op = "<table width='100%'><thead><tr><th>Notes</th><th>Created by</th><th>Created at</th></tr></thead><tbody>";
        foreach ($notes as $key => $item) :
            $op .= "<tr>";
            $op .= "<td>" . $item->notes . "</td>";
            $op .= "<td>" . $item->user->name . "</td>";
            $op .= "<td>" . $item->created_at->format('d.M.Y h:i a') . "</td>";
            $op .= "</tr>";
        endforeach;
        $op .= "</tbody></table>";
        echo $op;
    }

    public function getBookedProductDetails(Request $request)
    {
        $c = 1;
        $type = ($request->category == 'lens') ? ['re', 'le'] : [$request->category];
        $orders = OrderDetail::leftJoin('orders as o', 'o.id', 'order_details.order_id')->where('o.branch_id', $request->branch)->selectRaw("order_details.id, order_details.order_id, order_details.qty, order_details.product_id")->whereNull('o.stock_updated_at')->whereNotIn('o.order_status', ['delivered', 'cancelled'])->whereIn('order_details.eye', $type)->whereNull('o.deleted_at')->get();
        $op = "<table class='table table-bordered'><thead><tr><th>SL No</th><th>Order No</th><th>PID</th><th>Product</th><th>Qty</th><th>Status</th></tr></thead><tbody>";
        foreach ($orders as $key => $item) :
            $op .= "<tr>";
            $op .= "<td>" . $c++ . "</td>";
            $op .= "<td>" . $item->order?->ono() . "</td>";
            $op .= "<td>" . $item->product?->code . "</td>";
            $op .= "<td>" . $item->product?->name . "</td>";
            $op .= "<td class='text-end'>" . $item->qty . "</td>";
            $op .= "<td>" . $item->order?->order_status . "</td>";
            $op .= "</tr>";
        endforeach;
        $op .= "</tr><td colspan='4' class='text-end'>Total</td><td class='text-end fw-bold'>" . $orders->sum('qty') . "</td><td></td></tr>";
        $op .= "</tbody></table>";
        echo $op;
    }

    public function transferInProductDetails(Request $request)
    {
        $c = 1;
        $transfers = TransferDetails::leftJoin('transfers as t', 't.id', 'transfer_details.transfer_id')->selectRaw("transfer_details.product_id, transfer_details.transfer_id, SUM(transfer_details.qty) AS qty")->where('t.to_branch_id', $request->branch)->where('t.transfer_status', 1)->where('t.category', $request->category)->whereNull('t.stock_updated_in_at')->groupBy('transfer_details.product_id', 'transfer_details.transfer_id')->get();
        $op = "<table class='table table-bordered'><thead><tr><th>SL No</th><th>Transfer No</th><th>Product</th><th>PCode</th><th>PId</th><th>Date</th><th>From Branch</th><th>Qty</th></tr></thead><tbody>";
        foreach ($transfers->where('qty', '>', 0) as $key => $item) :
            $op .= "<tr>";
            $op .= "<td>" . $c++ . "</td>";
            $op .= "<td>" . $item->transfer?->transfer_number . "</td>";
            $op .= "<td>" . $item->product?->name . "</td>";
            $op .= "<td>" . $item->product?->code . "</td>";
            $op .= "<td>" . $item->product?->id . "</td>";
            $op .= "<td>" . $item->transfer?->created_at->format('d.M.Y') . "</td>";
            $op .= "<td>" . $item->transfer?->frombranch?->name . "</td>";
            $op .= "<td class='text-end'>" . $item->qty . "</td>";
            $op .= "</tr>";
        endforeach;
        $op .= "</tr><td colspan='7' class='text-end'>Total</td><td class='text-end fw-bold'>" . $transfers->sum('qty') . "</td><td></td></tr>";
        $op .= "</tbody></table>";
        echo $op;
    }

    public function transferOutProductDetails(Request $request)
    {
        $c = 1;
        $transfers = TransferDetails::leftJoin('transfers as t', 't.id', 'transfer_details.transfer_id')->selectRaw("transfer_details.product_id, transfer_details.transfer_id, SUM(transfer_details.qty) AS qty")->where('t.from_branch_id', $request->branch)->where('t.transfer_status', 1)->where('t.category', $request->category)->whereNull('t.stock_updated_out_at')->groupBy('transfer_details.product_id', 'transfer_details.transfer_id')->get();
        $op = "<table class='table table-bordered'><thead><tr><th>SL No</th><th>Transfer No</th><th>Product</th><th>PCode</th><th>PId</th><th>Date</th><th>To Branch</th><th>Qty</th></tr></thead><tbody>";
        foreach ($transfers->where('qty', '>', 0) as $key => $item) :
            $op .= "<tr>";
            $op .= "<td>" . $c++ . "</td>";
            $op .= "<td>" . $item->transfer?->transfer_number . "</td>";
            $op .= "<td>" . $item->product?->name . "</td>";
            $op .= "<td>" . $item->product?->code . "</td>";
            $op .= "<td>" . $item->product?->id . "</td>";
            $op .= "<td>" . $item->transfer?->created_at->format('d.M.Y') . "</td>";
            $op .= "<td>" . $item->transfer?->tobranch?->name . "</td>";
            $op .= "<td class='text-end'>" . $item->qty . "</td>";
            $op .= "</tr>";
        endforeach;
        $op .= "</tr><td colspan='7' class='text-end'>Total</td><td class='text-end fw-bold'>" . $transfers->sum('qty') . "</td><td></td></tr>";
        $op .= "</tbody></table>";
        echo $op;
    }

    public function generatePaymentQr(Request $request)
    {
        $vehicle = Vehicle::findOrFail($request->vid);
        $pn = $vehicle->owner_name;
        $tn = $request->mobile . ' - ' . $vehicle->owner_name;
        $upi = ($vehicle->upi_id) ? $vehicle->upi_id : $request->mobile . '@upi';
        $days = $vehicle->daysLeft();
        $am = ($days < 0) ? $vehicle->fee + ($vehicle->fee / 30) * abs($days) : $vehicle->fee - ($vehicle->fee / 30) * abs($days);
        $qr = base64_encode(QrCode::format('svg')->size(150)->color('#000000')->errorCorrection('H')->generate('upi://pay?pa=' . $upi . '&pn=' . $pn . '&tn=' . $tn . '&am=' . ceil($am) . '&cu=INR'));
        return response()->json([
            'qrCode' => $qr,
        ]);
    }
}
