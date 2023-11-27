<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentExport;
use App\Exports\CampPatientExport;
use App\Exports\ProductFrameExport;
use App\Exports\ProductLensExport;
use App\Exports\ProductPharmacyExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
}
