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
        <h4 class="text-center">ORDER RECEIPT</h4>

    </div>

    <div class="text-center mt-30">
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($order->consultation?->mrn , 'C39', 1, 30, array(110, 38, 14))}}" alt="barcode" />
    </div>
</div>
@endsection