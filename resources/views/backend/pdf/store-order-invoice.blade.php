@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        {{ $order->branch->name }}, {{ $order->branch->address }}, {{ $order->branch->phone }}
    </div>
    <div class="col text-center">
        GSTIN: {{ $order->branch->gstin }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center"><u>GST INVOICE</u></h4>
        <table width=100%>
            <tr>
                <td class="no-border" width="15%">Bill / Invoice No: </td>
                <td class="no-border" width="35%">{{ $order->invoice_number }}</td>
                <td class="no-border" width="15%">Bill / Invoice Date:</td>
                <td class="no-border" width="35%"> {{ $order->payments?->first()?->created_at?->format('d, M Y') }}</td>
            </tr>
        </table>
    </div>
    <div class="col mt-10">
        <table width=100%>
            <tr>
                <td class="no-border"><strong>Billed To</strong></td>
                <td class="no-border"><strong>Shipped To</strong></td>
            </tr>
            <tr>
                <td colspan="2" class="no-border">
                    <hr style="border: 1px solid; color:red;">
                </td>
            </tr>
            <tr>
                <td class="no-border">
                    <table>
                        <tr>
                            <td class="no-border">MRN: </td>
                            <td class="no-border">{{ $order->consultation_id }}</td>
                        </tr>
                        <tr>
                            <td class="no-border">Customer Name: </td>
                            <td class="no-border">{{ $order->name }}</td>
                        </tr>
                        <tr>
                            <td class="no-border">Address: </td>
                            <td class="no-border">{{ $order->place }}</td>
                        </tr>
                    </table>
                </td>
                <td class="no-border">
                    <table>
                        <tr>
                            <td class="no-border">MRN: </td>
                            <td class="no-border">{{ $order->consultation_id }}</td>
                        </tr>
                        <tr>
                            <td class="no-border">Customer Name: </td>
                            <td class="no-border">{{ $order->name }}</td>
                        </tr>
                        <tr>
                            <td class="no-border">Address: </td>
                            <td class="no-border">{{ $order->place }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width=100% class="mt-30" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="7" class=""></td>
                <td colspan="3" class="text-center ">GST</td>
                <td class=""></td>
            </tr>
            <tr>
                <td>SL No</td>
                <td>Product Name</td>
                <td>HSN / SAC</td>
                <td>Qty</td>
                <td>Rate</td>
                <td>Amount</td>
                <td>Disc.</td>
                <td class="text-center">CGST</td>
                <td class="text-center">SGST</td>
                <td class="text-center">IGST</td>
                <td>Total</td>
            </tr>
            @forelse($order->details as $key => $item)
            <tr>
                <td class="text-center b-0">{{ $key + 1 }}</td>
                <td class="b-0">{{ $item->product->name }}</td>
                <td class="text-center b-0">{{ ($item->eye) ? hsn()[$item->eye] : '' }}</td>
                <td class="text-center b-0">{{ $item->qty }}</td>
                <td class="text-end b-0">{{ $item->unit_price }}</td>
                <td class="text-end b-0">{{ number_format($item->qty*$item->unit_price, 2) }}</td>
                <td class="text-end b-0">0.00</td>
                <td class="text-end b-0">{{ ($item->tax_amount) ? number_format($item->tax_amount/2, 2) : 0 }}</td>
                <td class="text-end b-0">{{ ($item->tax_amount) ? number_format($item->tax_amount/2, 2) : 0 }}</td>
                <td class="text-end b-0">0.00</td>
                <td class="text-end b-0">{{ $item->total }}</td>
            </tr>
            @empty
            @endforelse
            <tr>
                @for($i=1;$i<=11;$i++) <td class="b-0 pt-50">
                    </td>
                    @endfor
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="10" class="text-end no-border">Order Total</td>
                <td class="text-end no-border">{{ number_format($order->order_total, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="10" class="text-end no-border">Taxable Value</td>
                <td class="text-end no-border">{{ number_format(($order->order_total - $order->details->sum('tax_amount')), 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="10" class="text-end no-border">CGST</td>
                <td class="text-end no-border">{{ number_format($order->details->sum('tax_amount') / 2, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="10" class="text-end no-border">SGST</td>
                <td class="text-end no-border">{{ number_format($order->details->sum('tax_amount') / 2, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="10" class="text-end no-border">IGST</td>
                <td class="text-end no-border">0.00</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="10" class="text-end no-border">Grand Total</td>
                <td class="text-end no-border fw-bold">{{ number_format($order->order_total, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="10" class="text-end no-border">Special Discount</td>
                <td class="text-end no-border">{{ number_format($order->discount, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="8" class="no-border">Amount in words: {{ $nums }} </td>
                <td colspan="2" class="text-end no-border">Bill Amount</td>
                <td class="text-end no-border fw-bold">{{ number_format($order->invoice_total, 2) }}</td>
            </tr>
        </table>
    </div>
    <div class="col text-end mt-50">
        <p>For DEVI OPTICIANS</p>
        <br />
        <p>Authorised Signatory</p>
    </div>
    <footer>
        <p>E & OE. Goods once sold will not be taken back. Thank You and Visit Again..</p>
    </footer>
</div>
@endsection