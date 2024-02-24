@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        <h3>{{ title() }}</h3>
        {{ $order->branch->name }}, {{ $order->branch->address }}, {{ $order->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">INVOICE</h4>
        <table class="table" width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td widtd="20%">Customer Name</td>
                    <td widtd="25%">{{ $order->name }}</td>
                    <td widtd="10%">Address</td>
                    <td widtd="45%">{{ $order->place }}</td>
                </tr>
                <tr>
                    <td widtd="20%">Product Adviser</td>
                    <td widtd="25%">{{ $order->adviser->name }}</td>
                    <td widtd="10%">Invoice Number</td>
                    <td widtd="45%">{{ $order->invoice_number }}</td>
                </tr>
                <tr>
                    <td widtd="20%">Customer Mobile</td>
                    <td widtd="25%">{{ $order->mobile }}</td>
                    <td widtd="10%">Invoice Date</td>
                    <td widtd="45%">{{ $order->created_at->format('d, M Y') }}</td>
                </tr>
            </thead>
        </table>
    </div>
    <!--<div class="text-center mt-30">
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($order->invoice_number , 'C39', 1, 30, array(110, 38, 14))}}" alt="barcode" />
    </div>-->
</div>
@endsection