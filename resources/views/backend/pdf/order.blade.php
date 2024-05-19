@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        {{ branch()->address }}, {{ branch()->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">Date: {{ $request->fdate }} to {{ $request->tdate }}</h4>
        <p class="text-center">Branch: {{ $branch->name }}, Status: {{ $request->status }}</p>
    </div>
    <div class="col">
        <table width="100%" class="mt-10" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Order No.</th>
                    <th>Bill No.</th>
                    <th>Order Date</th>
                    <th>Advance</th>
                    <th>Balance</th>
                    <th>Order/Invoice Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->ono() }}</td>
                    <td>{{ $item->ino() }}</td>
                    <td>{{ $item->created_at->format('d.M.Y') }}</td>
                    <td class="text-end">{{ $item->advance }}</td>
                    <td class="text-end">{{ $item->balance }}</td>
                    <td class="text-end">{{ $item->invoice_total }}</td>
                </tr>
                @empty
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8" class="text-end">Total</th>
                    <th class="text-end">{{ ($sales) ? number_format($sales->sum('invoice_total'), 2) : '0.00' }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <footer>
        <!--Printed On: {{ Carbon\Carbon::now()->format('d, M Y h:i a') }}-->
    </footer>
</div>
@endsection