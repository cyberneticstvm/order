@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        <h3>{{ title() }}</h3>
        {{ $transfer->tobranch->name }}, {{ $transfer->tobranch->address }}, {{ $transfer->tobranch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">Product Transfer List ({{ $transfer->transfer_number }}) </h4>
        <p class="text-center">Transfer Date: {{ $transfer->transfer_date->format('d-M-Y') }}</p>
    </div>
    <div class="col">
        <table width="100%" class="mt-10" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Product Name</th>
                    <th>Product ID</th>
                    <th>Batch Number</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfer->details as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->code }}</td>
                    <td>{{ $item->batch_number }}</td>
                    <td class="text-end fw-bold">{{ $item->qty }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">
                        <h3 class="text-danger">No records found!</h3>
                    </td>
                    @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bold">Total</td>
                    <td class="text-end fw-bold">{{ $transfer->details->sum('qty') }}</td>
                </tr>
            </tfoot>
        </table>
        <div class="">
            <p>Transfer Note: {{ $transfer->transfer_note }}</p>
        </div>
    </div>
    <footer>
        Printed On: {{ Carbon\Carbon::now()->format('d, M Y h:i a') }}
    </footer>
</div>
@endsection