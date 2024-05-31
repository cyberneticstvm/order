<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentExport;
use App\Exports\CampPatientExport;
use App\Exports\FailedProductsExport;
use App\Exports\OrderExport;
use App\Exports\ProductCompareExport;
use App\Exports\ProductFrameExport;
use App\Exports\ProductLensExport;
use App\Exports\ProductPharmacyExport;
use App\Models\Branch;
use App\Imports\FrameImport;
use App\Imports\LensImport;
use App\Imports\ProductCompareImport;
use App\Imports\ProductLensPurchaseImport;
use App\Imports\ProductPurchaseImport;
use App\Imports\ProductTransferImport;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductDamage;
use App\Models\Purchase;
use App\Models\SalesReturn;
use App\Models\StockCompareTemp;
use App\Models\Transfer;
use App\Models\TransferDetails;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    protected $branches, $tobranches;

    function __construct()
    {
        $this->middleware('permission:export-product-lens-excel', ['only' => ['exportProductLens']]);
        $this->middleware('permission:export-product-frame-excel', ['only' => ['exportProductFrame']]);
        $this->middleware('permission:import-product-purchase', ['only' => ['importProductPurchase', 'importProductPurchaseUpdate']]);
        $this->middleware('permission:import-new-frames', ['only' => ['importFrames', 'importFramesUpdate']]);
        $this->middleware('permission:import-new-lenses', ['only' => ['importLenses', 'importLensesUpdate']]);
        $this->middleware('permission:import-product-transfer', ['only' => ['importTransfer', 'importTransferUpdate']]);
        $this->middleware('permission:stock-comparison', ['only' => ['stockComparison', 'stockComparisonUpdate']]);

        $this->middleware(function ($request, $next) {

            $brs = Branch::selectRaw("0 as id, 'Main Branch' as name");
            /*$this->branches = Branch::selectRaw("id, name")->union($brs)->when(Auth::user()->roles->first()->name != 'Administrator', function ($q) {
                return $q->where('id', Session::get('branch'));
            })->orderBy('id')->pluck('name', 'id');

            $this->tobranches = Branch::selectRaw("id, name")->union($brs)->when(Auth::user()->roles->first()->name != 'Administrator', function ($q) {
                return $q->where('id', Session::get('branch'));
            })->orderBy('id')->pluck('name', 'id');*/
            $this->branches = Branch::selectRaw("id, name")->where('id', Session::get('branch'))->when(in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) use ($brs) {
                return $q->union($brs);
            })->orderBy('id')->pluck('name', 'id');

            $this->tobranches = Branch::selectRaw("id, name")->where('id', '<>', Session::get('branch'))->when(in_array(Auth::user()->roles->first()->name, ['Administrator', 'CEO', 'Store Manager', 'Accounts']), function ($q) use ($brs) {
                return $q->union($brs);
            })->orderBy('id')->pluck('name', 'id');

            return $next($request);
        });
    }

    public function exportProductLens(Request $request)
    {
        return Excel::download(new ProductLensExport($request), 'lens_products.xlsx');
    }

    public function exportProductFrame(Request $request)
    {
        return Excel::download(new ProductFrameExport($request), 'frame_products.xlsx');
    }

    public function importProductPurchase()
    {
        return view('backend.purchase.import.index');
    }

    public function importProductPurchaseUpdate(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
            'category' => 'required',
        ]);
        try {
            $purchase = Purchase::create([
                'category' => $request->category,
                'purchase_number' => purchaseId('frame')->pid,
                'order_date' => Carbon::today(),
                'delivery_date' => Carbon::today(),
                'supplier_id' => 1,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            $import = new ProductPurchaseImport($purchase);
            Excel::import($import, $request->file('file')->store('temp'));
            if ($import->data) :
                Session::put('fdata', $import->data);
                return redirect()->route('upload.failed')->with("warning", "Some products weren't uploaded. Please check the excel file for more info.");
            endif;
        } catch (Exception $e) {
            return back()->with("error", $e->getMessage());
        }
        return back()->with("success", "Purchase Updated Successfully");
    }

    public function importFrames()
    {
        return view('backend.product.import.frame');
    }

    public function importFramesUpdate(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);
        try {
            $import = new FrameImport();
            Excel::import($import, $request->file('file')->store('temp'));
            if ($import->data) :
                Session::put('fdata', $import->data);
                return redirect()->route('upload.failed')->with("warning", "Some products weren't uploaded. Please check the excel file for more info.");
            endif;
        } catch (Exception $e) {
            return back()->with("error", $e->getMessage());
        }
        return back()->with("success", "Frames Imported Successfully");
    }

    public function importLenses()
    {
        return view('backend.product.import.lens');
    }

    public function importLensesUpdate(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);
        try {
            $import = new LensImport();
            Excel::import($import, $request->file('file')->store('temp'));
            if ($import->data) :
                Session::put('fdata', $import->data);
                return redirect()->route('upload.failed')->with("warning", "Some products weren't uploaded. Please check the excel file for more info.");
            endif;
        } catch (Exception $e) {
            return back()->with("error", $e->getMessage());
        }
        return back()->with("success", "Frames Imported Successfully");
    }

    public function importTransfer()
    {
        $branches = $this->branches;
        $tobranches = $this->tobranches;
        return view('backend.transfer.import.index', compact('branches', 'tobranches'));
    }

    public function importTransferUpdate(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
            'category' => 'required',
            'from_branch_id' => 'required',
            'to_branch_id' => 'required',
        ]);
        try {
            $transfer = Transfer::create([
                'transfer_number' => transferId($request->category)->tid,
                'category' => $request->category,
                'transfer_date' => Carbon::today(),
                'from_branch_id' => $request->from_branch_id,
                'to_branch_id' => $request->to_branch_id,
                'transfer_note' => 'Transfer via excel import',
                'transfer_status' => 0,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            $import = new ProductTransferImport($transfer);
            Excel::import($import, $request->file('file')->store('temp'));
            if ($import->data) :
                Session::put('fdata', $import->data);
                return redirect()->route('upload.failed')->with("warning", "Some products weren't uploaded. Please check the excel file for more info.");
            endif;
        } catch (Exception $e) {
            return back()->with("error", $e->getMessage());
        }
        return back()->with("success", "Products Transferred Successfully");
    }

    public function stockPreview()
    {
        StockCompareTemp::query()->delete();
        $branches = Branch::where('type', 'branch')->orderBy('name', 'ASC')->get();
        $products = collect();
        $inputs = array(Session::get('branch'), 'frame');
        return view('backend.extras.stock-compare', compact('branches', 'products', 'inputs'));
    }

    public function stockPreviewUpdate(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
            'branch' => 'required',
            'category' => 'required',
        ]);
        try {
            StockCompareTemp::query()->delete();
            $branches = Branch::where('type', 'branch')->orderBy('name', 'ASC')->get();
            $inputs = array($request->branch, $request->category);
            $import = new ProductCompareImport($request);
            Excel::import($import, $request->file('file')->store('temp'));
            $products = StockCompareTemp::all();
            if ($products) :
                return view('backend.extras.stock-compare', compact('products', 'branches', 'inputs'));
            endif;
            /*if ($import->pdct) :
                Session::put('fdata', $import->pdct);
                return redirect()->route('upload.failed')->with("warning", "Some products weren't uploaded. Please check the excel file for more info.");
            else :
                $records = [];
                $stock = Product::leftJoin('stock_compare_temps as sct', 'products.id', 'sct.product_id')->where('products.category', $request->category)->selectRaw("sct.product_id, SUM(sct.qty) AS qty, products.id, products.name, products.code")->groupBy('sct.product_id', 'products.id', 'products.name', 'products.code')->get();
                foreach ($stock as $key => $item) :
                    $current = getInventory($request->branch, $item->id, $request->category);
                    $qty = $item->qty ?? 0;
                    if ($current->sum('balanceQty') != 0 || $qty != 0) :
                        $records[] = [
                            'product_name' => $item->name,
                            'product_code' => $item->code,
                            'stock_in_hand' => $current->sum('balanceQty'),
                            'uploaded_qty' => $qty,
                            'difference' => ($qty > $current->sum('balanceQty')) ? abs($qty) - abs($current->sum('balanceQty')) : abs($current->sum('balanceQty')) - abs($qty),
                        ];
                    endif;
                endforeach;
                if ($records) :
                    StockCompareTemp::query()->delete();
                    return Excel::download(new ProductCompareExport(collect($records)), 'compare_difference.xlsx');
                endif;
            endif;*/
        } catch (Exception $e) {
            return back()->with("error", $e->getMessage());
        }
        return back()->with("success", "Products compared successfully and found no difference");
    }

    public function compareStock(string $category, string $branch)
    {
        $records = [];
        /*$stock = Product::leftJoin('stock_compare_temps as sct', 'products.id', 'sct.product_id')->where('products.category', $category)->selectRaw("sct.product_id, SUM(sct.qty) AS qty, products.id, products.name, products.code")->groupBy('sct.product_id', 'products.id', 'products.name', 'products.code')->get();*/
        $stock = StockCompareTemp::where('category', $category)->where('branch_id', $branch)->selectRaw("product_id, SUM(qty) AS qty, product_name, product_code")->groupBy('product_id', 'product_name', 'product_code')->get();;
        if ($stock->isNotEmpty()) :
            foreach ($stock as $key => $item) :
                $current = getInventory($branch, $item->product_id, $category);
                $qty = $item->qty ?? 0;
                if ($current->sum('balanceQty') != 0 || $qty != 0) :
                    $records[] = [
                        'product_name' => $item->product_name,
                        'product_code' => $item->product_code,
                        'stock_in_hand' => $current->sum('balanceQty'),
                        'uploaded_qty' => $qty,
                        'difference' => ($qty > $current->sum('balanceQty')) ? abs($qty) - abs($current->sum('balanceQty')) : abs($current->sum('balanceQty')) - abs($qty),
                    ];
                endif;
            endforeach;
            if ($records) :
                //StockCompareTemp::query()->delete();
                return Excel::download(new ProductCompareExport(collect($records)), 'compare_difference.xlsx');
            endif;
        else :
            return redirect()->back()->with("error", "Empty records");
        endif;
    }

    public function updateStock(Request $request, string $category, string $branch)
    {
        try {
            $transfer = Transfer::where('from_branch_id', $branch)->where('transfer_status', 0);
            $products = StockCompareTemp::all();
            if ($transfer->exists()) :
                return redirect()->back()->with("error", "Some pending transfer yet to be accepted");
            elseif ($products->count() == 0) :
                return redirect()->back()->with("error", "Empty records");
            else :
                DB::transaction(function () use ($category, $branch, $request, $products) {
                    $data = [];
                    Transfer::where('category', $category)->where('to_branch_id', $branch)->delete();
                    SalesReturn::where('returned_branch', $branch)->delete();
                    ProductDamage::where('from_branch', $branch)->delete();
                    Order::where('branch_id', $branch)->where('order_status', 'delivered')->update(['stock_updated_at' => Carbon::now()]);
                    $transfer = Transfer::create([
                        'transfer_number' => transferId($category)->tid,
                        'category' => $category,
                        'transfer_date' => Carbon::today(),
                        'from_branch_id' => 1000, // If branch id 1000, then treat as stock adjustment entry
                        'to_branch_id' => $branch,
                        'transfer_note' => "Stock Adjustment Entry",
                        'transfer_status' => 1,
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                    foreach ($products as $key => $item) :
                        if ($item->product_id) :
                            $data[] = [
                                'transfer_id' => $transfer->id,
                                'product_id' => $item->product_id,
                                'qty' => $item->qty,
                                'batch_number' => NULL,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ];
                        endif;
                    endforeach;
                    TransferDetails::insert($data);
                });
            endif;
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
        return redirect()->back()->with("success", "Stock updated successfully");
    }

    public function deleteTempItem(string $id)
    {
        StockCompareTemp::findOrFail(decrypt($id))->delete();
        return redirect()->back()->with("success", "Item deleted successfully");
    }

    public function uploadFailed()
    {
        return view('backend.failed-data');
    }

    public function uploadFailedExport()
    {
        $data = Session::get('fdata');
        Session::forget('fdata');
        return Excel::download(new FailedProductsExport($data), 'failed_upload.xlsx');
    }

    public function exportOrder(Request $request)
    {
        return Excel::download(new OrderExport($request), 'order.xlsx');
    }
}
