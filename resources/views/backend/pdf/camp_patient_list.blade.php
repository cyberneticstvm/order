@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        <h3>{{ title() }}</h3>
        {{ $camp->branch->name }}, {{ $camp->branch->address }}, {{ $camp->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">PATIENT LIST OF {{ $camp->name }} ({{ $camp->camp_id }}) </h4>
        <p class="text-center">From {{ $camp->from_date->format('d-M-Y') }} To {{ $camp->to_date->format('d-M-Y') }}</p>       
    </div>
    <div class="col">
        <table width="100%" class="mt-10" cellspacing="0" cellpadding="0">
            <thead><tr><th>SL No</th><th>Patient Name</th><th>Age</th><th>Gender</th><th>Place</th><th>Mobile</th></tr></thead>
            <tbody>
                @forelse($camp->patients as $key => $patient)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $patient->name }}</td>
                        <td>{{ $patient->age }}</td>
                        <td>{{ $patient->gender }}</td>
                        <td>{{ $patient->place }}</td>
                        <td>{{ $patient->mobile }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center"><h3 class="text-danger">No records found!</h3></td>
                @endforelse
            </tbody>
        </table>
    </div>
    <footer>
        Printed On: {{ Carbon\Carbon::now()->format('d, M Y h:i a') }}
    </footer>
</div>
@endsection