@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="text-center">
        {{ $order->branch->name }}, {{ $order->branch->address }}, {{ $order->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">RECEIPT</h4>
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
                    <td widtd="10%">Order Number</td>
                    <td widtd="45%">{{ $order->branch->code }}/{{ $order->id }}</td>
                </tr>
                <tr>
                    <td widtd="20%">MRN</td>
                    <td widtd="25%">{{ $order->consultation_id }}</td>
                    <td widtd="10%">Order Date</td>
                    <td widtd="45%">{{ $order->created_at->format('d, M Y') }}</td>
                </tr>
            </thead>
        </table>
    </div>
    <center>
        <p>Product Details</p>
    </center>
    <table class="bordered" width="100%" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $slno = 1; @endphp
            @forelse($order->details as $key => $value)
            <tr>
                <td>{{ $slno++ }}</td>
                <td>{{ $value->product->name }}</td>
                <td class="text-right">{{ $value->qty }}</td>
                <td class="text-right">{{ $value->unit_price }}</td>
                <td class="text-right">{{ $value->total }}</td>
            </tr>
            @empty
            @endforelse
            <tr>
                <td colspan="4" class="text-right"><b>Total</b></td>
                <td class="text-right"><b>{{ $order->invoice_total }}</b></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><b>Discount</b></td>
                <td class="text-right"><b>{{ $order->discount ?? '0.00' }}</b></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><b>Advance</b></td>
                <td class="text-right"><b>{{ $order->advance }}</b></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><b>Balance</b></td>
                <td class="text-right"><b>{{ $order->balance }}</b></td>
            </tr>
        </tbody>
    </table>
    <center>
        <p class="txt">Heartly welcome to Devi Opticians Family. Thank you for your order. For any enquiry about your order please contact us on 93 88 611 622</p>
    </center>
    <!--<div class="text-center mt-30">
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($order->invoice_number , 'C39', 1, 30, array(110, 38, 14))}}" alt="barcode" />
    </div>-->
</div>
<hr style="border: 1px dotted;">
<div class="row mt-30">
    <div class="col">
        <table class="table" width="75%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td widtd="20%" class="no-border">Customer Name</td>
                    <td widtd="25%" class="no-border">{{ $order->name }}</td>
                    <td widtd="10%" class="no-border">Address</td>
                    <td widtd="45%" class="no-border">{{ $order->place }}</td>
                </tr>
                <tr>
                    <td widtd="20%" class="no-border">Product Adviser</td>
                    <td widtd="25%" class="no-border">{{ $order->adviser->name }}</td>
                    <td widtd="10%" class="no-border">Order Number</td>
                    <td widtd="45%" class="no-border">{{ $order->branch->code }}/{{ $order->id }}</td>
                </tr>
                <tr>
                    <td widtd="20%" class="no-border">MRN</td>
                    <td widtd="25%" class="no-border">{{ $order->consultation_id }}</td>
                    <td widtd="10%" class="no-border">Order Date</td>
                    <td widtd="45%" class="no-border">{{ $order->created_at->format('d, M Y') }}</td>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="row mt-30">
    <div class="col">
        <table class="bordered" width="75%" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="no-border">SL No</th>
                    <th class="no-border">Eye</th>
                    <th class="no-border">Product</th>
                    <th class="no-border">Qty</th>
                </tr>
            </thead>
            <tbody>
                @php $slno = 1; @endphp
                @forelse($order->details as $key => $value)
                <tr>
                    <td class="no-border">{{ $slno++ }}</td>
                    <td class="no-border">{{ $value->eye }}</td>
                    <td class="no-border">{{ $value->product->name }}</td>
                    <td class="text-right no-border">{{ $value->qty }}</td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="row mt-30">
    <div class="col">
        <table class="no-border" cellpadding="0" cellspacing="0" width="75%">
            <thead>
                <tr>
                    <th class="no-border"></th>
                    <th class="no-border">SPH</th>
                    <th class="no-border">CYL</th>
                    <th class="no-border">AXIS</th>
                    <th class="no-border">ADD</th>
                    <th class="no-border">IPD</th>
                    <th class="no-border">FH</th>
                    <th class="no-border">PRISM</th>
                </tr>
                @forelse($order->details->whereIn('eye', ['re', 'le']) as $key => $value)
                <tr>
                    <td class="no-border">{{ $value->eye }}</td>
                    <td class="no-border">{{ $value->sph }}</td>
                    <td class="no-border">{{ $value->cyl }}</td>
                    <td class="no-border">{{ $value->axis }}</td>
                    <td class="no-border">{{ $value->add }}</td>
                    <td class="no-border">{{ $value->ipd }}</td>
                    <td class="no-border"></td>
                    <td class="no-border"></td>
                </tr>
                @empty
                @endforelse
            </thead>
        </table>
    </div>
</div>
<div class="row mt-30">
    <div class="col">
        <p>Order Note / Comments: {{ $order->order_note }}</p>
    </div>
</div>
@endsection