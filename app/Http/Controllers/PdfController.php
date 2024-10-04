<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Camp;
use App\Models\CampPatient;
use App\Models\Consultation;
use App\Models\MedicalRecord;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Spectacle;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;

class PdfController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:export-today-appointments-pdf', ['only' => ['exportTodaysAppointment']]);
        $this->middleware('permission:invoice-register', ['only' => ['invoices']]);
        $this->middleware('permission:invoice-view-download', ['only' => ['exportOrderInvoice']]);
        $this->middleware('permission:invoice-register-not-generated', ['only' => ['invoicesNotGenerated']]);
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
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('https://devieh.com'));
        $pdf = PDF::loadView('/backend/pdf/product-lens', compact('products', 'qrcode'));
        return $pdf->stream('lens-products.pdf');
    }

    public function exportProductFrame()
    {
        $products = Product::with('manufacturer')->where('category', 'frame')->orderBy('name')->get();
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('https://devieh.com'));
        $pdf = PDF::loadView('/backend/pdf/product-frame', compact('products', 'qrcode'));
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
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('https://devieh.com'));
        $pdf = PDF::loadView('/backend/pdf/product-transfer', compact('transfer', 'qrcode'));
        return $pdf->stream($transfer->transfer_number . '.pdf');
    }

    public function invoices()
    {
        $invoices = Order::where('branch_id', Session::get('branch'))->WhereNotNull('invoice_number')->whereDate('invoice_generated_at', Carbon::today())->orderByDesc('order_sequence')->get();
        return view('backend.order.invoices', compact('invoices'));
    }

    public function invoicesNotGenerated()
    {
        $invoices = Order::where('branch_id', Session::get('branch'))->WhereNull('invoice_number')->latest()->get();
        return view('backend.order.not-generated-invoices', compact('invoices'));
    }

    public function generateInvoice(string $id)
    {
        $order = Order::findOrFail(decrypt($id));
        if (!isFullyPaid($order->id, $status = 'delivered')) :
            return redirect()->back()->with("error", "Amount due.");
        else :
            $order->update([
                'invoice_number' => invoicenumber(decrypt($id))->ino,
                'order_sequence' => branchInvoiceNumber(),
                'invoice_generated_by' => Auth::id(),
                'invoice_generated_at' => Carbon::now(),
                'order_status' => 'delivered',
            ]);
            updateLabOrderStatus($order->id);
            recordOrderEvent($order->id, 'Invoice has been generated');
        endif;
        return redirect()->back()->with("success", "Invoice generated successfully!");
    }

    public function exportOrderInvoice($id)
    {
        $order = Order::findOrFail(decrypt($id));
        if ($order->invoice_number) :
            $oid = $order->id * 100;
            $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('https://order.speczone.net/bill/details/' . $oid));
            $nums = $this->NumberintoWords($order->invoice_total);
            $pdf = PDF::loadView('/backend/pdf/store-order-invoice', compact('order', 'qrcode', 'nums'));
            return $pdf->stream($order->invoice_number . '.pdf');
        else :
            return redirect()->back()->with("error", "Invoice yet to be generated");
        endif;
    }

    public function exportOrderReceipt($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $advance = $order->payments->where('payment_type', 'advance1')->sum('amount');
        $pn = $order->name . ' - ' . $order->branch->code;
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('upi://pay?pa=9995050149@okbizaxis&pn=' . $pn . '&tn=' . $order->id . '&am=' . $order->balance . '&cu=INR'));
        $pdf = PDF::loadView('/backend/pdf/store-order-receipt', compact('order', 'qrcode', 'advance'));
        return $pdf->stream('ORDER-' . $order->id . '.pdf');
    }

    public function exportOrderPrescription($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('https://devieh.com'));
        $pdf = PDF::loadView('/backend/pdf/prescription', compact('order', 'qrcode'));
        return $pdf->stream($order->invoice_number . '.pdf');
    }

    public function exportCustomerPrescription($id)
    {
        $spectacle = Spectacle::findOrFail(decrypt($id));
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('https://devieh.com'));
        $pdf = PDF::loadView('/backend/pdf/spectacle', compact('spectacle', 'qrcode'));
        return $pdf->stream('spectacle.pdf');
    }

    public function exportOrder(Request $request)
    {
        $sales = Order::whereBetween(($request->status != 'delivered') ? 'order_date' : 'invoice_generated_at', [Carbon::parse($request->fdate)->startOfDay(), Carbon::parse($request->tdate)->endOfDay()])->when($request->branch > 0, function ($q) use ($request) {
            return $q->where('branch_id', $request->branch);
        })->when($request->status != 'all', function ($q) use ($request) {
            return $q->where('order_status', $request->status);
        })->orderBy('order_sequence', 'ASC')->get();
        $branch = Branch::find($request->branch);
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('https://devieh.com'));
        $pdf = PDF::loadView('/backend/pdf/order', compact('sales', 'qrcode', 'request', 'branch'));
        return $pdf->stream('order.pdf');
    }

    public function exportStockStatus(Request $request)
    {
        $stock = getInventory($request->branch, 0, $request->category);
        $branch = Branch::find($request->branch);
        $qrcode = base64_encode(QrCode::format('svg')->size(75)->errorCorrection('H')->generate('https://devieh.com'));
        $pdf = PDF::loadView('/backend/pdf/stock', compact('stock', 'qrcode', 'request', 'branch'));
        return $pdf->stream('stock.pdf');
    }

    public function NumberintoWords(float $number)
    {
        $number_after_decimal = round($number - ($num = floor($number)), 2) * 100;

        // Check if there is any number after decimal
        $amt_hundred = null;
        $count_length = strlen($num);
        $x = 0;
        $string = array();
        $change_words = array(
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Fourty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety'
        );
        $here_digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($x < $count_length) {
            $get_divider = ($x == 2) ? 10 : 100;
            $number = floor($num % $get_divider);
            $num = floor($num / $get_divider);
            $x += $get_divider == 10 ? 1 : 2;
            if ($number) {
                $add_plural = (($counter = count($string)) && $number > 9) ? 's' : null;
                $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
                $string[] = ($number < 21) ? $change_words[$number] . ' ' . $here_digits[$counter] . $add_plural . '
       ' . $amt_hundred : $change_words[floor($number / 10) * 10] . ' ' . $change_words[$number % 10] . '
       ' . $here_digits[$counter] . $add_plural . ' ' . $amt_hundred;
            } else $string[] = null;
        }
        $implode_to_Words = implode('', array_reverse($string));
        $get_word_after_point = ($number_after_decimal > 0) ? "Point " . ($change_words[$number_after_decimal / 10] . "
        " . $change_words[$number_after_decimal % 10]) : '';
        return ($implode_to_Words ? $implode_to_Words : ' ') . $get_word_after_point;
    }
}
