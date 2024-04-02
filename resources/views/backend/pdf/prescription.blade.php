@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="text-center">
        {{ $order->branch->name }}, {{ $order->branch->address }}, {{ $order->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">ORDER DETAILS</h4>
        <table class="table" width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td width="20%" class="border-0">Customer Name</td>
                    <td width="25%" class="border-0 fw-bold">{{ $order->name }}</td>
                    <td width="10%" class="border-0">Address</td>
                    <td width="45%" class="border-0 fw-bold" colspan="3">{{ $order->place }}</td>
                </tr>
                <tr>
                    <td width="20%" class="border-0">Product Adviser</td>
                    <td width="25%" class="border-0 fw-bold">{{ $order->adviser->name }}</td>
                    <td width="15%" class="border-0">Order Number</td>
                    <td width="40%" class="border-0 fw-bold" colspan="3">{{ $order->branch->code }}/{{ $order->id }}</td>
                </tr>
                <tr>
                    <td width="20%" class="border-0">MRN</td>
                    <td width="20%" class="border-0 fw-bold">{{ ($order->consultation_id == 0) ? 'Direct' : $order->consultation_id }}</td>
                    <td width="10%" class="border-0">Order Date</td>
                    <td width="15%" class="border-0 fw-bold">{{ $order->created_at->format('d, M Y') }}</td>
                    <td class="border-0" width="10%">Exp.Del.Date</td>
                    <td class="border-0 fw-bold" width="25%">{{ $order->expected_delivery_date->format('d, M Y') }}</td>
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
                <th class="border-0">Ipd</th>
                <th class="border-0">Int.Add</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->details as $key => $item)
            @if(in_array($item->eye, ['re', 'le']))
            <tr style="border-top: 1px solid gray;">
                <td class="border-0">{{ strtoupper($item->eye) }}</td>
                <td class="border-0">{{ $item->sph }}</td>
                <td class="border-0">{{ $item->cyl }}</td>
                <td class="border-0">{{ $item->axis }}</td>
                <td class="border-0">{{ $item->add }}</td>
                <td class="border-0">{{ $item->ipd }}</td>
                <td class="border-0">{{ $item->int_add }}</td>
            </tr>
            @endif
            @empty
            @endforelse
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