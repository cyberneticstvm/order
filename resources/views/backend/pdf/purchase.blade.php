@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        {{ $purchase->branch?->name }}, {{ $purchase->branch?->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">Purchase Details</h4>
        <table class="table border-0" width="100%" cellpadding="0" cellspacing="0" style="font-size: 10px;">
            <tr>
                <td>Bill No: {{ $purchase->purchase_invoice_number }}</td>
                <td>Branch: {{ $purchase->branch->name }}</td>
            </tr>
        </table>
    </div>
    <div class="col">
        <table width="100%" class="mt-10" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Tax Amount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchase->detail as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->unit_price_purchase }}</td>
                    <td>{{ $item->discount }}</td>
                    <td>{{ $item->tax_amount }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">
                        <h3 class="text-danger">No records found!</h3>
                    </td>
                    @endforelse
            <tfoot>
                <tr>
                    <td class="fw-bold text-end" colspan="2">Total</td>
                    <td class="fw-bold text-end">{{ number_format($purchase->detail()->sum('qty'), 2) }}</td>
                    <td colspan="3"></td>
                    <td class="fw-bold text-end">{{ number_format($purchase->detail()->sum('total'), 2) }}</td>
                </tr>
            </tfoot>
            </tbody>
        </table>
    </div>
    <footer>
        Printed On: {{ Carbon\Carbon::now()->format('d, M Y h:i a') }}
    </footer>
</div>
@endsection