<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentExport;
use App\Exports\CampPatientExport;
use App\Exports\FailedProductsExport;
use App\Exports\ProductFrameExport;
use App\Exports\ProductLensExport;
use App\Exports\ProductPharmacyExport;
use App\Imports\ProductLensPurchaseImport;
use App\Imports\ProductPurchaseImport;
use App\Models\Purchase;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:export-today-appointments-excel', ['only' => ['exportTodayAppointments']]);
    }

    public function exportTodayAppointments(Request $request)
    {
        return Excel::download(new AppointmentExport($request), 'appointments_' . Carbon::today()->format('d-M-Y') . '.xlsx');
    }

    public function exportCampPatientList(Request $request, $id)
    {
        return Excel::download(new CampPatientExport($request, $id), 'patient_list.xlsx');
    }

    public function exportProductPharmacy(Request $request)
    {
        return Excel::download(new ProductPharmacyExport($request), 'pharmacy_products.xlsx');
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
                return redirect()->route('upload.failed')->with("warning", "Some products weren't uploaded. Please check the downloaded excel file for more info.");
            endif;
        } catch (Exception $e) {
            return back()->with("error", $e->getMessage());
        }
        return back()->with("success", "Purchase Updated Successfully");
    }

    public function uploadFailed()
    {
        return view('backend.failed-data');
    }

    public function uploadFailedExport()
    {
        $data = Session::get('fdata');
        Session::forget('fdata');
        return Excel::download(new FailedProductsExport($data), 'products.xlsx');
    }
}
