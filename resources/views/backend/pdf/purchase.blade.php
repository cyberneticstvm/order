@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        {{ $purchase->branch->address }}, {{ $purchase->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">Purchase Details - {{ $purchase->id }}</h4>
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
            </tbody>
        </table>
    </div>
    <footer>
        Printed On: {{ Carbon\Carbon::now()->format('d, M Y h:i a') }}
    </footer>
</div>
@endsection