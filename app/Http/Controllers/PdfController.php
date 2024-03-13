<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Camp;
use App\Models\CampPatient;
use App\Models\Consultation;
use App\Models\MedicalRecord;
use App\Models\Order;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:export-today-appointments-pdf', ['only' => ['exportTodaysAppointment']]);
    }

    public function opt($id)
    {
        $consultation = Consultation::with('patient', 'doctor', 'branch')->findOrFail(decrypt($id));
        $qrcode = base64_encode(QrCode::format('svg')->size(50)->errorCorrection('H')->generate(qrCodeText()));
        $pdf = PDF::loadView('/backend/pdf/opt', compact('consultation', 'qrcode'));
        return $pdf->stream($consultation->mrn . '.pdf');
    }

    public function prescription($id)
    {
        $consultation = Consultation::with('patient', 'doctor', 'branch')->findOrFail(decrypt($id));
        $qrcode = base64_encode(QrCode::format('svg')->size(50)->errorCorrection('H')->generate(qrCodeText()));
        $pdf = PDF::loadView('/backend/pdf/prescription', compact('consultation', 'qrcode'));
        return $pdf->stream($consultation->mrn . '.pdf');
    }

    public function cReceipt($id)
    {
        $consultation = Consultation::with('patient', 'doctor', 'branch')->findOrFail(decrypt($id));
        $qrcode = base64_encode(QrCode::format('svg')->size(50)->errorCorrection('H')->generate(qrCodeText()));
        $pdf = PDF::loadView('/backend/pdf/consultation_receipt', compact('consultation', 'qrcode'));
        return $pdf->stream($consultation->mrn . '.pdf');
    }

    public function medicalRecord($id)
    {
        $mrecord = MedicalRecord::with('consultation')->findOrFail(decrypt($id));
        $pdf = PDF::loadView('/backend/pdf/medical_record', compact('mrecord'));
        return $pdf->stream($mrecord->consultation->mrn . '.pdf');
    }

    public function exportTodaysAppointment()
    {
        $appointments = Appointment::with('doctor', 'branch')->whereDate('date', Carbon::today())->where('branch_id', Session::get('branch'))->orderBy('time')->get();
        $pdf = PDF::loadView('/backend/pdf/today-appointment', compact('appointments'));
        return $pdf->stream('appointment.pdf');
    }

    public function exportCampPatientList($id)
    {
        $camp = Camp::findOrFail(decrypt($id));
        $pdf = PDF::loadView('/backend/pdf/camp_patient_list', compact('camp'));
        return $pdf->stream('camp.pdf');
    }

    public function exportCampPatientMedicalRecord($id)
    {
        $patient = CampPatient::findOrFail(decrypt($id));
        $pdf = PDF::loadView('/backend/pdf/camp_patient_medical_record', compact('patient'));
        return $pdf->stream($patient->id . '.pdf');
    }

    public function exportProductPharmacy()
    {
        $products = Product::with('manufacturer')->where('category', 'pharmacy')->orderBy('name')->get();
        $pdf = PDF::loadView('/backend/pdf/product-pharmacy', compact('products'));
        return $pdf->stream('pharmacy-products.pdf');
    }

    public function exportProductLens()
    {
        $products = Product::with('manufacturer')->where('category', 'lens')->orderBy('name')->get();
        $pdf = PDF::loadView('/backend/pdf/product-lens', compact('products'));
        return $pdf->stream('lens-products.pdf');
    }

    public function exportProductFrame()
    {
        $products = Product::with('manufacturer')->where('category', 'frame')->orderBy('name')->get();
        $pdf = PDF::loadView('/backend/pdf/product-frame', compact('products'));
        return $pdf->stream('frame-products.pdf');
    }

    public function exportPaymentReceipt($id)
    {
        $payment = Payment::findOrFail(decrypt($id));
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('https://devieh.com'));
        $pdf = PDF::loadView('/backend/pdf/payment-receipt', compact('payment', 'qrcode'));
        return $pdf->stream($payment->id . '.pdf');
    }

    public function exportProductTransfer($id)
    {
        $transfer = Transfer::findOrFail(decrypt($id));
        $pdf = PDF::loadView('/backend/pdf/product-transfer', compact('transfer'));
        return $pdf->stream($transfer->transfer_number . '.pdf');
    }

    public function exportOrderInvoice($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $pdf = PDF::loadView('/backend/pdf/store-order-invoice', compact('order'));
        return $pdf->stream($order->invoice_number . '.pdf');
    }

    public function exportOrderReceipt($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('upi://pay?pa=9995050149@okbizaxis&pn=' . $order->name . '&tn=' . $order->id . '&am=' . $order->balance . '&cu=INR'));
        $pdf = PDF::loadView('/backend/pdf/store-order-receipt', compact('order', 'qrcode'));
        return $pdf->stream('ORDER-' . $order->id . '.pdf');
    }
}
