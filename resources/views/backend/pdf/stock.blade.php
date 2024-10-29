@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        {{ branch()->address }}, {{ branch()->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">Stock Status As On: {{ date('d.M.Y') }}</h4>
        <p class="text-center">Branch: {{ $branch->name ?? 'All Branches' }}</p>
    </div>
    <div class="col">
        <table width="100%" class="mt-10" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Product Name</th>
                    <th>PID</th>
                    <th>Code</th>
                    <th>Booked</th>
                    <th>Billed</th>
                    <th>Transfer In</th>
                    <th>Transfer Out</th>
                    <th>Returned</th>
                    <th>Damaged</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @php($c=1)
                @forelse($stock->where('balanceQty', '>', 0) as $key => $item)
                <tr>
                    <td>{{ $c++ }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->pcode }}</td>
                    <td>{{ $item->soldQty }}</td>
                    <td>{{ $item->billedQty }}</td>
                    <td>{{ $item->purchasedQty }}</td>
                    <td>{{ $item->transferredQty }}</td>
                    <td>{{ $item->returnedQty }} </td>
                    <td>{{ $item->damagedQty }}</td>
                    <td>{{ $item->balanceQty }} </td>
                    @empty
                    @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="fw-bold text-end">Total</td>
                    <td class="fw-bold text-right">{{ $stock->sum('soldQty') }}</td>
                    <td class="fw-bold">{{ $stock->sum('billedQty') }}</td>
                    <td class="fw-bold text-right">{{ $stock->sum('purchasedQty') }}</td>
                    <td class="fw-bold text-right">{{ $stock->sum('transferredQty') }}</td>
                    <td class="fw-bold text-right">{{ $stock->sum('returnedQty') }}</td>
                    <td class="fw-bold text-right">{{ $stock->sum('damagedQty') }}</td>
                    <td class="fw-bold text-right">{{ $stock->sum('balanceQty') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <footer>
        <!--Printed On: {{ Carbon\Carbon::now()->format('d, M Y h:i a') }}-->
    </footer>
</div>
@endsection