<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentExport;
use App\Exports\CampPatientExport;
use App\Exports\FailedProductsExport;
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
use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockCompareTemp;
use App\Models\Transfer;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function stockComparison()
    {
        $branches = Branch::where('type', 'branch')->orderBy('name', 'ASC')->get();
        return view('backend.extras.stock-compare', compact('branches'));
    }

    public function stockComparisonUpdate(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
            'branch' => 'required',
            'category' => 'required',
        ]);
        try {
            $import = new ProductCompareImport($request);
            Excel::import($import, $request->file('file')->store('temp'));
            if ($import->pdct) :
                Session::put('fdata', $import->pdct);
                return redirect()->route('upload.failed')->with("warning", "Some products weren't uploaded. Please check the excel file for more info.");
            else :
                $records = [];
                $stock = Product::leftJoin('stock_compare_temps as sct', 'products.id', 'sct.product_id')->where('products.category', $request->category)->selectRaw("sct.product_id, SUM(sct.qty) AS qty, products.id, products.name, products.code")->groupBy('sct.product_id', 'products.id', 'products.name', 'products.code')->orderBy('sct.id')->get();
                foreach ($stock as $key => $item) :
                    $current = getInventory($request->branch, $item->id, $request->category);
                    //if ($current->sum('balanceQty') != $item->qty) :
                    $qty = $item->qty ?? 0;
                    $records[] = [
                        'product_name' => $item->name,
                        'product_code' => $item->code,
                        'stock_in_hand' => $current->sum('balanceQty'),
                        'uploaded_qty' => $qty,
                        'difference' => ($qty > $current->sum('balanceQty')) ? abs($qty) - abs($current->sum('balanceQty')) : abs($current->sum('balanceQty')) - abs($qty),
                    ];
                //endif;
                endforeach;
                if ($records) :
                    StockCompareTemp::query()->delete();
                    return Excel::download(new ProductCompareExport(collect($records)), 'compare_difference.xlsx');
                endif;
            endif;
        } catch (Exception $e) {
            return back()->with("error", $e->getMessage());
        }
        return back()->with("success", "Products compared successfully and found no difference");
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
}
