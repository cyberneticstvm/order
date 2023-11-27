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
        <h4 class="text-center">OUT PATIENT RECORD</h4>
        <table class="table bordered" width="100%" cellspacing="0" cellpadding="0">
            <tbody>
                <tr><td width="40%">Name: {{ strtoupper($consultation->patient->name) }}</td><td>ID: {{ $consultation->patient->patient_id }}</td><td>MRN: {{ $consultation->mrn }}</td></tr>
                <tr><td>Age: {{ $consultation->patient->age }}</td><td>Contact: {{ $consultation->patient->mobile }}</td><td>Doctor: {{ $consultation->doctor->name }}</td></tr>
                <tr><td colspan="2">Address: {{ $consultation->patient->place }}</td><td>Date: {{ $consultation->created_at->format('d, M Y h:i A') }}</td></tr>
            </tbody>
        </table>
    </div>
    <div class="mt-10">History</div>
    <div class="col mt-10">
        <table width="100%"><tr><td height="100"></td></tr></table>
    </div>
    <div class="col">
        <table width="100%">
            <tr>
                <td width="50%" class="no-border">RE</td>
                <td width="50%" class="no-border">LE</td>
            </tr>
        </table>
        <table width="100%">
            <tr>
                <td height="150" width="50%"></td>
                <td height="150" width="50%"></td>
            </tr>
        </table>
    </div>
    <div class="mt-10">Provisional Diagnosis / Treatment</div>
    <div class="col mt-10">
        <table width="100%"><tr><td height="150"></td></tr></table>
    </div>
    <div class="text-center mt-30">
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($consultation->mrn , 'C39', 1, 30, array(110, 38, 14))}}" alt="barcode" />
    </div>
</div>
@endsection