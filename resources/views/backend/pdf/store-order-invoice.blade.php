@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        {{ $order->branch->address }}, {{ $order->branch->phone }}
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
                <td class="no-border" width="35%">{{ $order->ino() }}</td>
                <td class="no-border" width="20%">Bill / Invoice Date:</td>
                <td class="no-border" width="30%"> {{ $order->invoice_generated_at?->format('d, M Y h:i a') }}</td>
            </tr>
        </table>
    </div>
    <div class="col">
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
                            <td class="no-border">{{ ($order->consultation_id == 0) ? 'Direct' : $order->consultation_id }}</td>
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
                            <td class="no-border">{{ ($order->consultation_id == 0) ? 'Direct' : $order->consultation_id }}</td>
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
        <hr style="border: 1px solid; color:red;">
        <table width="100%">
            <tr>
                <td class="no-border" width="20%">Company Name: </td>
                <td class="no-border" width="30%">{{ ($order->company_name) ?? 'Na' }}</td>
                <td class="no-border" width="10%">GSTIN: </td>
                <td class="no-border" width="15%">{{ ($order->gstin) ?? 'Na' }}</td>
                <td class="no-border" width="15%">State Code: </td>
                <td class="no-border" width="10%">{{ $order->state }}</td>
            </tr>
        </table>
        <table width=100% class="mt-30" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="5" class=""></td>
                <td colspan="3" class="text-center ">GST</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td>SL No</td>
                <td>Product Name</td>
                <td>HSN / SAC</td>
                <td>Qty</td>
                <td>Rate</td>
                <td class="text-center">CGST</td>
                <td class="text-center">SGST</td>
                <td class="text-center">IGST</td>
                <td>Amount</td>
                <td>Total</td>
            </tr>
            @forelse($order->details as $key => $item)
            <tr>
                <td class="text-center b-0">{{ $key + 1 }}</td>
                <td class="b-0">{{ $item->product->name }} ({{ ($item->eye == 're' || $item->eye == 'le') ? 'Lens' : $item->eye }}) {{ ($item->unit_price == 0) ? ' - (Free)' : '' }}</td>
                <td class="text-center b-0">{{ ($item->eye) ? hsn()[$item->eye] : '' }}</td>
                <td class="text-center b-0">{{ $item->qty }}</td>
                <td class="text-end b-0">{{ number_format($item->unit_price - $item->tax_amount, 2) }}</td>
                <td class="text-end b-0">{{ ($item->tax_amount && $order->state == 32) ? number_format($item->tax_amount/2, 2) : 0 }}</td>
                <td class="text-end b-0">{{ ($item->tax_amount && $order->state == 32) ? number_format($item->tax_amount/2, 2) : 0 }}</td>
                <td class="text-end b-0">{{ ($item->tax_amount && $order->state != 32) ? number_format($item->tax_amount, 2) : 0 }}</td>
                <td class="text-end b-0">{{ number_format($item->qty*$item->unit_price, 2) }}</td>
                <td class="text-end b-0">{{ $item->total }}</td>
            </tr>
            @empty
            @endforelse
            <tr>
                @for($i=1;$i<=10;$i++) <td class="b-0 pt-50">
                    </td>
                    @endfor
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="9" class="text-end no-border">Order Total</td>
                <td class="text-end no-border">{{ number_format($order->order_total, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="9" class="text-end no-border">Taxable Value</td>
                <td class="text-end no-border">{{ number_format(($order->order_total - $order->details->sum('tax_amount')), 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="9" class="text-end no-border">CGST</td>
                <td class="text-end no-border">{{ number_format($order->details->sum('tax_amount') / 2, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="9" class="text-end no-border">SGST</td>
                <td class="text-end no-border">{{ number_format($order->details->sum('tax_amount') / 2, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="9" class="text-end no-border">IGST</td>
                <td class="text-end no-border">0.00</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="9" class="text-end no-border">Grand Total</td>
                <td class="text-end no-border fw-bold">{{ number_format($order->order_total, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="9" class="text-end no-border">Special Discount</td>
                <td class="text-end no-border">{{ number_format($order->discount, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td colspan="7" class="no-border">Amount in words: {{ $nums }} </td>
                <td colspan="2" class="text-end no-border">Bill Amount</td>
                <td class="text-end no-border fw-bold">{{ number_format($order->invoice_total, 2) }}</td>
            </tr>
        </table>
    </div>
    <p>Invoice Note: {{ $order->invoice_note ?? 'Na' }}</p>
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