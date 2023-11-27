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
        <h4 class="text-center">RECEIPT</h4>
        <table class="table bordered" width="100%" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td width="40%">Name: {{ strtoupper($consultation->patient->name) }}</td>
                    <td>ID: {{ $consultation->patient->patient_id }}</td>
                    <td>MRN: {{ $consultation->mrn }}</td>
                </tr>
                <tr>
                    <td>Age: {{ $consultation->patient->age }}</td>
                    <td>Contact: {{ $consultation->patient->mobile }}</td>
                    <td>Doctor: {{ $consultation->doctor->name }}</td>
                </tr>
                <tr>
                    <td colspan="2">Address: {{ $consultation->patient->place }}</td>
                    <td>Date: {{ $consultation->created_at->format('d, M Y h:i A') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <h4 class="text-center">Description of Services</h4>
    <div class="col">
        <table class="table bordered" width="100%" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th width="15%">SL No</th>
                    <th width="65%">Particulars</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Registration Fee</td>
                    <td class="text-right">{{ number_format($consultation->patient->registration_fee, 2) }}</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Consultation Fee</td>
                    <td class="text-right">{{ number_format($consultation->doctor_fee, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right fw-bold">{{ number_format($consultation->doctor_fee + $consultation->patient->registration_fee, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection