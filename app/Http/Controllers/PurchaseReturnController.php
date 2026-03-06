<?php

namespace App\Http\Controllers;

use App\Models\ProductDamage;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\SupplierAccount;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase-return-list|purchase-return-create|purchase-return-edit|purchase-return-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:purchase-return-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchase-return-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchase-return-delete', ['only' => ['destroy']]);
    }

    function index()
    {
        $returns = PurchaseReturn::all();
        return view('backend.purchase.return.index', compact('returns'));
    }

    function store(Request $request)
    {
        $this->validate($request, [
            'ret_qty' => 'required',
        ]);
        try {
            DB::transaction(function () use ($request) {
                $return = PurchaseReturn::create([
                    'notes' => $request->notes,
                    'courier_charges' => $request->courier_charges ?? 0,
                    'other_charges' => $request->other_charges ?? 0,
                    'rtype' => $request->rtype,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
                $data = [];
                $tot = 0;
                foreach ($request->product_ids as $key => $item):
                    if ($request->ret_qty[$key] > 0):
                        $data[] = [
                            "return_id" => $return->id,
                            "rtype_id" => $request->rtype_ids[$key],
                            "supplier_id" => $request->supplier_ids[$key],
                            "product_id" => $item,
                            "qty" => $request->ret_qty[$key],
                            "price" => $request->prices[$key],
                            "total" => $request->ret_qty[$key] * $request->prices[$key],
                            "created_at" => Carbon::now(),
                            "updated_at" => Carbon::now(),
                        ];
                        if ($request->rtype == "damage"):
                            ProductDamage::where("id", $request->rtype_ids[$key])->update([
                                "returned" => true,
                            ]);
                        else:
                            PurchaseDetail::where("id", $request->rtype_ids[$key])->update([
                                "qty_returned" => $request->ret_qty[$key],
                            ]);
                        endif;
                        $tot += $request->ret_qty[$key] * $request->prices[$key];
                    endif;
                endforeach;
                PurchaseReturnDetail::insert($data);
                SupplierAccount::create([
                    "pr_id" => $return->id,
                    "amount" => $tot + $request->courier_charges + $request->other_charges,
                    "type" => "dr"
                ]);
                return redirect()->back()->with("success", "Purchase return recorded successfully!");
            });
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}
