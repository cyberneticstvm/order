@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        {{ branch()->address }}, {{ branch()->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">Vehicle List</h4>
    </div>
    <div class="col">
        <table width="100%" class="mt-10" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Name</th>
                    <th>Contact Number</th>
                    <th>Branch</th>
                    <th>Stand</th>
                    <th>Reg. No.</th>
                    <th>Status</th>
                    <th>Days Left</th>
                    <th>Reg. Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->owner_name }}</td>
                    <td>{{ $item->contact_number }}</td>
                    <td>{{ $item->branch?->name }}</td>
                    <td>{{ $item->place }}</td>
                    <td>{{ $item->reg_number }}</td>
                    <td>{{ $item->isVehicleActive() ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $item->daysLeft() }}</td>
                    <td>{{ $item->created_at->format('d.M.Y') }}</td>
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