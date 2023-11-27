@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        <h3>{{ title() }}</h3>
        {{ $patient->camp->branch->name }}, {{ $patient->camp->branch->address }}, {{ $patient->camp->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">CAMP PATIENT MEDICAL RECORD</h4>
        <p class="text-center">{{ $patient->camp->name }} ({{ $patient->camp->camp_id }})</p>
        <table class="table bordered" width="100%" cellspacing="0" cellpadding="0">
            <tbody>
                <tr><td width="40%">Name: {{ strtoupper($patient->name) }}</td><td>Gender: {{ $patient->gender }}</td><td>Age: {{ $patient->age }}</td></tr>
                <tr><td>Contact: {{ $patient->mobile }}</td><td colspan="2">Address: {{ $patient->place }}</td></tr>
                <tr><td>Cordinator: {{ $patient->camp->getCordinator->name }}</td><td>Optomtrist: {{ $patient->camp->getOptometrist->name }}</td><td>Date: {{ $patient->created_at->format('d, M Y h:i A') }}</td></tr>
            </tbody>
        </table>
    </div>
    <div class="mt-30">
        <center><u>VISION</u></center>
        <table width="100%" class="mt-10" cellspacing="0" cellpadding="0">
            <thead><tr><th>EYE</th><th>VB</th><th>SPH</th><th>CYL</th><th>AXIS</th><th>ADD</th><th>VA</th></tr></thead>
            <tbody>
                <tr>
                    <td class="text-center">RE</td>
                    <td class="text-center">{{ $patient->vision->re_vb }}</td>
                    <td class="text-center">{{ $patient->vision->re_sph }}</td>
                    <td class="text-center">{{ $patient->vision->re_cyl }}</td>
                    <td class="text-center">{{ $patient->vision->re_axis }}</td>
                    <td class="text-center">{{ $patient->vision->re_add }}</td>
                    <td class="text-center">{{ $patient->vision->re_va }}</td>                    
                </tr>
                <tr>
                    <td class="text-center">LE</td>
                    <td class="text-center">{{ $patient->vision->le_va }}</td>
                    <td class="text-center">{{ $patient->vision->le_sph }}</td>
                    <td class="text-center">{{ $patient->vision->le_cyl }}</td>
                    <td class="text-center">{{ $patient->vision->le_axis }}</td>
                    <td class="text-center">{{ $patient->vision->le_add }}</td>
                    <td class="text-center">{{ $patient->vision->le_va }}</td>                    
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-30">
        <u>NOTES / REMARKS</u>
        <p>{{ $patient->notes }}</p>
    </div>
    <div class="mt-30">
        <table width="100%" class="border-0">
            <tr>
                <td class="border-0">
                    <u>Further Investigation Advised?</u>
                    <p>{{ $patient->isInvestigation() }}</p>
                </td>
                <td class="border-0">
                    <u>Glasses Advised?</u>
                    <p>{{ $patient->isGlasses() }}</p>
                </td>
                <td class="border-0">
                    <u>Yearly Test Advised?</u>
                    <p>{{ $patient->isYearlyTest() }}</p>
                </td>
                <td class="border-0">
                    <u>Surgery Advised?</u>
                    <p>{{ $patient->isSuregry() }}</p>
                </td>
            </tr>
            <tr>
                <td class="border-0" colspan="4">
                    <u>Review Date</u>
                    <p>{{ $patient->review_date?->format('d, M Y') }}</p>
                </td>
            </tr>
        </table>        
    </div>
    <footer>
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($patient->id , 'C39', 1, 30, array(110, 38, 14))}}" alt="barcode" />
    </footer>
</div>
@endsection