@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        <h3>{{ title() }}</h3>
        {{ $mrecord->consultation->branch->name }}, {{ $mrecord->consultation->branch->address }}, {{ $mrecord->consultation->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">MEDICAL RECORD</h4>
        <table class="table bordered" width="100%" cellspacing="0" cellpadding="0">
            <tbody>
                <tr><td width="40%">Name: {{ strtoupper($mrecord->consultation->patient->name) }}</td><td>ID: {{ $mrecord->consultation->patient->patient_id }}</td><td>MRN: {{ $mrecord->consultation->mrn }}</td></tr>
                <tr><td>Age: {{ $mrecord->consultation->patient->age }}</td><td>Contact: {{ $mrecord->consultation->patient->mobile }}</td><td>Doctor: {{ $mrecord->consultation->doctor->name }}</td></tr>
                <tr><td colspan="2">Address: {{ $mrecord->consultation->patient->place }}</td><td>Date: {{ $mrecord->created_at->format('d, M Y h:i A') }}</td></tr>
            </tbody>
        </table>
    </div>
    <div class="mt-30">
        <u>SYMPTOMS</u>
        <p>{{ $mrecord->symptoms()->pluck('name')->implode(', ') }}</p>
    </div>
    <div class="mt-30">
        <table width="100%" class="border-0">
            <tr>
                <td class="border-0">
                    <u>PATIENT HISTORY</u>
                    <p>{{ $mrecord->patient_history }}</p>
                </td>
                <td class="border-0">
                    <u>ALLERGIC DRUGS</u>
                    <p>{{ $mrecord->allergic_drugs }}</p>
                </td>
            </tr>
        </table>        
    </div>
    <div class="mt-30">
        <u>DIAGNOSES</u>
        <p>{{ $mrecord->diagnoses()->pluck('name')->implode(', ') }}</p>
    </div>
    <div class="mt-30">
        <u>DOCTOR RECOMMONDATIONS / ADVICE</u>
        <p>{{ $mrecord->doctor_recommondation }}</p>
    </div>
    <div class="mt-30">
        <center><u>VISION</u></center>
        <table width="100%" class="mt-10" cellspacing="0" cellpadding="0">
            <thead><tr><th>EYE</th><th>SPH</th><th>CYL</th><th>AXIS</th><th>ADD</th><th>VA</th><th>NV</th></tr></thead>
            <tbody>
                <tr>
                    <td class="text-center">RE</td>
                    <td class="text-center">{{ $mrecord->vision->re_sph }}</td>
                    <td class="text-center">{{ $mrecord->vision->re_cyl }}</td>
                    <td class="text-center">{{ $mrecord->vision->re_axis }}</td>
                    <td class="text-center">{{ $mrecord->vision->re_add }}</td>
                    <td class="text-center">{{ $mrecord->vision->re_va }}</td>
                    <td class="text-center">{{ $mrecord->vision->re_nv }}</td>
                </tr>
                <tr>
                    <td class="text-center">LE</td>
                    <td class="text-center">{{ $mrecord->vision->le_sph }}</td>
                    <td class="text-center">{{ $mrecord->vision->le_cyl }}</td>
                    <td class="text-center">{{ $mrecord->vision->le_axis }}</td>
                    <td class="text-center">{{ $mrecord->vision->le_add }}</td>
                    <td class="text-center">{{ $mrecord->vision->le_va }}</td>
                    <td class="text-center">{{ $mrecord->vision->le_nv }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-30">
        <table width="100%" class="border-0">
            <tr>
                <td class="border-0">
                    <u>SURGERY ADVISED</u>
                    <p>{{ $mrecord->isSuregry() }}</p>
                </td>
                <td class="border-0 text-end">
                    <u>NEXT REVIEW DATE</u>
                    <p>{{ $mrecord->review_date?->format('d, M Y') }}</p>
                </td>
            </tr>
        </table>        
    </div>
    <div class="text-center mt-30">
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($mrecord->consultation->mrn , 'C39', 1, 30, array(110, 38, 14))}}" alt="barcode" />
        <p>{{ $mrecord->consultation->mrn }}</p>
    </div>
</div>
@endsection