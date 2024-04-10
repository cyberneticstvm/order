@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="text-center">
        {{ $spectacle->branch->address }}, {{ $spectacle->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center"><u>SPECTACLE PRESCRIPTION</u></h4>
        <table class="table" width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td width="20%" class="border-0">Customer Name</td>
                    <td width="25%" class="border-0 fw-bold">{{ $spectacle->customer->name }}</td>
                    <td width="10%" class="border-0">Address</td>
                    <td width="45%" class="border-0 fw-bold">{{ $spectacle->customer->place }}</td>
                </tr>
                <tr>
                    <td width="10%" class="border-0">Date</td>
                    <td width="15%" class="border-0 fw-bold">{{ $spectacle->created_at->format('d, M Y') }}</td>
                    <td width="10%" class="border-0">Cust. ID</td>
                    <td width="15%" class="border-0 fw-bold">{{ $spectacle->customer->id }}</td>
                </tr>
            </thead>
        </table>
    </div>
    <hr style="border: 1px solid; color:red;">
    <table class="table mt-30" width="100%" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th class="border-0">Eye</th>
                <th class="border-0">Sph</th>
                <th class="border-0">Cyl</th>
                <th class="border-0">Axis</th>
                <th class="border-0">Add</th>
                <th class="border-0">IPD</th>
                <th class="border-0">Int.Add</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-top: 1px solid gray;">
                <td class="border-0">RE</td>
                <td class="border-0">{{ $spectacle->re_sph }}</td>
                <td class="border-0">{{ $spectacle->re_cyl }}</td>
                <td class="border-0">{{ $spectacle->re_axis }}</td>
                <td class="border-0">{{ $spectacle->re_add }}</td>
                <td class="border-0">{{ $spectacle->re_pd }}</td>
                <td class="border-0">{{ $spectacle->re_int_add }}</td>
            </tr>
            <tr style="border-top: 1px solid gray;">
                <td class="border-0">LE</td>
                <td class="border-0">{{ $spectacle->le_sph }}</td>
                <td class="border-0">{{ $spectacle->le_cyl }}</td>
                <td class="border-0">{{ $spectacle->le_axis }}</td>
                <td class="border-0">{{ $spectacle->le_add }}</td>
                <td class="border-0">{{ $spectacle->le_pd }}</td>
                <td class="border-0">{{ $spectacle->le_int_add }}</td>
            </tr>
        </tbody>
    </table>
    <div class="col text-end mt-50">
        <p>For DEVI OPTICIANS</p>
        <br />
        <p>Authorised Signatory</p>
    </div>
    <footer>
        <p>Thank You and Visit Again..</p>
    </footer>
</div>
@endsection