@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="text-center">
        {{ $order->branch->address }}, {{ $order->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">ORDER DETAILS</h4>
        <table class="table" width="100%" cellpadding="0" cellspacing="0" style="font-size: 10px;">
            <thead>
                <tr>
                    <td width="20%" class="border-0">Customer Name</td>
                    <td width="25%" class="border-0 fw-bold">{{ $order?->name }}</td>
                    <td width="10%" class="border-0">Address</td>
                    <td width="45%" class="border-0 fw-bold" colspan="3">{{ $order->place }}</td>
                </tr>
                <tr>
                    <td width="20%" class="border-0">Product Adviser</td>
                    <td width="25%" class="border-0 fw-bold">{{ $order?->adviser?->name }}</td>
                    <td width="15%" class="border-0">Order Number</td>
                    <td width="40%" class="border-0 fw-bold font-big" colspan="3">{{ $order->ono() }}</td>
                </tr>
                <tr>
                    <td width="20%" class="border-0">MRN</td>
                    <td width="20%" class="border-0 fw-bold">{{ ($order->consultation_id == 0) ? 'Direct' : $order->consultation_id }}</td>
                    <td width="10%" class="border-0">Order Date</td>
                    <td width="15%" class="border-0 fw-bold">{{ $order->created_at->format('d, M Y') }}</td>
                    <td class="border-0" width="10%">Exp.Del.Date</td>
                    <td class="border-0 fw-bold" width="25%">{{ $order->expected_delivery_date->format('d, M Y') }}</td>
                </tr>
            </thead>
        </table>
    </div>
    <hr style="border: 1px solid; color:red;">
    <table class="border-0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 10px;">
        <thead>
            <tr>
                <th class="border-0">SL No</th>
                <th class="border-0">Product</th>
                <th class="border-0">Qty</th>
                <th class="border-0">Price</th>
                <th class="border-0">Total</th>
            </tr>
            <tr>
                <td class="border-0" colspan="5">
                    <hr style="border: 1px solid; color:red;">
                </td>
            </tr>
        </thead>
        <tbody>
            @php $slno = 1; @endphp
            @forelse($order->details as $key => $value)
            <tr>
                <td class="border-0 pd-1">{{ $slno++ }}</td>
                <td class="border-0 pd-1">{{ $value?->product?->name }} ({{ strtoupper($value->eye) }})</td>
                <td class="text-right border-0 pd-1">{{ $value->qty }}</td>
                <td class="text-right border-0 pd-1">{{ $value->unit_price }}</td>
                <td class="text-right border-0 pd-1">{{ $value->total }}</td>
            </tr>
            @empty
            @endforelse
            <tr>
                <td colspan="4" class="text-right border-0"><b>Total</b></td>
                <td class="text-right border-0"><b>{{ $order->order_total }}</b></td>
            </tr>
            @if($order->discount > 0)
            <tr>
                <td colspan="4" class="text-right border-0"><b>Discount</b></td>
                <td class="text-right border-0"><b>{{ $order->discount ?? '0.00' }}</b></td>
            </tr>
            @endif
            <tr>
                <td colspan="4" class="text-right border-0"><b>Toal After Discount</b></td>
                <td class="text-right border-0"><b>{{ $order->invoice_total ? number_format($order->invoice_total, 2) : '0.00' }}</b></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right border-0"><b>Advance</b></td>
                <td class="text-right border-0"><b>{{ ($order->advance + $advance) ? number_format($order->advance + $advance, 2) : '0.00' }}</b></td>
            </tr>
            @if($order->credit_used > 0)
            <tr>
                <td colspan="4" class="text-right border-0"><b>Availbale Credit Used</b></td>
                <td class="text-right border-0"><b>{{ ($order->credit_used) ?? '0.00' }}</b></td>
            </tr>
            @endif
            <tr>
                <td colspan="4" class="text-right border-0"><b>Balance</b></td>
                <td class="text-right border-0"><b>{{ number_format($order->balance - $advance, 2) }}</b></td>
            </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col">
            <p>Order Note / Comments: {{ $order->order_note }}</p>
        </div>
    </div>
    <center>
        <p class="txt">Heartly welcome to Devi Opticians Family. Thank you for your order. For any enquiry about your order please contact us on 93 88 611 622</p>
    </center>
    <!--<div class="text-center mt-30">
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($order->invoice_number , 'C39', 1, 30, array(110, 38, 14))}}" alt="barcode" />
    </div>-->
</div>
<hr style="border: 1px dotted; color:blue;">
@endsection