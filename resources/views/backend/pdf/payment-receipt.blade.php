@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        <h3>{{ title() }}</h3>
        {{ $payment->order->branch->name }}, {{ $payment->order->branch->address }}, {{ $payment->order->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">PAYMENT RECEIPT</h4>
        <table class="table" width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td widtd="20%">Customer Name</td>
                    <td widtd="25%">{{ $payment->order->name }}</td>
                    <td widtd="10%">Address</td>
                    <td widtd="45%">{{ $payment->order->place }}</td>
                </tr>
                <tr>
                    <td widtd="20%">Payment ID</td>
                    <td widtd="25%">{{ $payment->id }}</td>
                    <td widtd="10%">Order Number</td>
                    <td widtd="45%">{{ $payment->order->branch->code.'/'.$payment->order->id }}</td>
                </tr>
                <tr>
                    <td widtd="20%">Customer Mobile</td>
                    <td widtd="25%">{{ $payment->order->mobile }}</td>
                    <td widtd="10%">Payment Date</td>
                    <td widtd="45%">{{ $payment->created_at->format('d, M Y') }}</td>
                </tr>
            </thead>
        </table>
    </div>
    <div class="col mt-50">
        <table class="table" width="100%" cellpadding="0" cellspacing="0">
            <thead class="">
                <tr class="">
                    <td class="">SL No</td>
                    <td class="">Particulars</td>
                    <td class="">Amount</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="">1</td>
                    <td class="">{{ $payment->notes }} ({{ $payment->paymentmode->name }})</td>
                    <td class="text-end">{{ $payment->amount }}</td>
                </tr>
                <tr>
                    <td class="text-end" colspan="2"><strong>Total</strong></td>
                    <td class="text-end"><strong>{{ $payment->amount }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    <!--<div class="text-center mt-30">
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($payment->order->invoice_number , 'C39', 1, 30, array(110, 38, 14))}}" alt="barcode" />
    </div>-->
</div>
@endsection