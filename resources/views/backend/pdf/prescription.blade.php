@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        <h3>{{ title() }}</h3>
        {{ $consultation->branch->name }}, {{ $consultation->branch->address }}, {{ $consultation->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">PRESCRIPTION</h4>
        <table class="table bordered" width="100%" cellspacing="0" cellpadding="0">
            <tbody>
                <tr><td width="40%">Name / Age: {{ strtoupper($consultation->patient->name) }} / {{ $consultation->patient->age }}</td><td>ID: {{ $consultation->patient->patient_id }}</td><td>MRN: {{ $consultation->mrn }}</td></tr>
                <tr><td>Place: {{ $consultation->patient->place }}</td><td>Contact: {{ $consultation->patient->mobile }}</td><td>Doctor: {{ $consultation->doctor->name }}</td></tr>
            </tbody>
        </table>
    </div>
    <div class="col mt-50">
        <table class="table bordered" width="100%" cellspacing="0" cellpadding="0">
            <thead>
                <tr><th colspan="5" class="text-center">RE</th><th colspan="3" class="text-center">LE</th></tr>
                <tr><th colspan="2"></th><th>SPH</th><th>CYL</th><th>AXIS</th><th>SPH</th><th>CYL</th><th>AXIS</th></tr>
            </thead>
            <tbody class="h-50">
                <tr><td colspan="2">DIST</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                <tr><td colspan="2">READ</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            </tbody>
        </table>
    </div>
    <div class="col mt-30">
        <p>I.P.D ..............................</p>
    </div>
    <div class="col mt-30">
        <p>Remarks ....................................................................................................</p>
    </div>
    <div class="col mt-50">
        <table width="100%"><tr><td class="no-border">Optometrist</td><td class="text-right no-border">Ophthalmologist</td></tr></table>
    </div>
    <div class="col mt-50">
        <table width="100%"><tr><td class="no-border text-right">Single Vision</td><td class="text-center no-border">Bifocals</td><td class="no-border">Multifocals / PAL</td></tr></table>
    </div>
    <br /><br /><br /><br /><br />
    <div class="col text-right mt-50">
        <img src="data:image/png;base64, {!! $qrcode !!}"><br><br>
        <small>{{ $consultation->created_at->format('d, M Y h:i A') }}</small>
    </div>
</div>
@endsection