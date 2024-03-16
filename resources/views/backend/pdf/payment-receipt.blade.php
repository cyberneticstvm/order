@extends("backend.pdf.base")
@section("pdfcontent")
<div class="row">
    <div class="col text-center">
        <h3>{{ title() }}</h3>
        {{ $payment->order->branch->name }}, {{ $payment->order->branch->address }}, {{ $payment->order->branch->phone }}
    </div>
</div>
<div class="row">
    <div class="col">
        <h4 class="text-center">PAYMENT RECEIPT</h4>
        <table class="table" width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td width="20%" class="no-border">Customer Name</td>
                    <td width="30%" class="no-border fw-bold">{{ $payment->order->name }}</td>
                    <td width="20%" class="no-border">Address</td>
                    <td width="30%" class="no-border fw-bold">{{ $payment->order->place }}</td>
                </tr>
                <tr>
                    <td width="20%" class="no-border">Payment ID</td>
                    <td width="30%" class="no-border fw-bold">{{ $payment->id }}</td>
                    <td width="20%" class="no-border">Order Number</td>
                    <td width="30%" class="no-border fw-bold">{{ $payment->order->branch->code.'/'.$payment->order->id }}</td>
                </tr>
                <tr>
                    <td width="20%" class="no-border">Customer Mobile</td>
                    <td width="30%" class="no-border fw-bold">{{ $payment->order->mobile }}</td>
                    <td width="20%" class="no-border">Payment Date</td>
                    <td width="30%" class="no-border fw-bold">{{ $payment->created_at->format('d, M Y') }}</td>
                </tr>
            </thead>
        </table>
    </div>
    <div class="col mt-50">
        <table class="table" width="100%" cellpadding="0" cellspacing="0">
            <thead class="">
                <tr>
                    <th colspan="4" class="no-border">
                        <hr style="border: 1px solid; color:red;">
                    </th>
                </tr>
                <tr class="">
                    <td class="no-border">SL No</td>
                    <td class="no-border">Particulars</td>
                    <td class="no-border">Payment Mode</td>
                    <td class="no-border">Amount</td>
                </tr>
                <tr>
                    <th colspan="4" class="no-border">
                        <hr style="border: 1px solid; color:red;">
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="no-border">1</td>
                    <td class="no-border">Payment received against order number {{ $payment->order->branch->code.'/'.$payment->order->id }}</td>
                    <td class="no-border">{{ $payment->paymentmode->name }}</td>
                    <td class="no-border text-end">{{ $payment->amount }}</td>
                </tr>
                <tr>
                    <td class="text-end no-border" colspan="3"><strong>Total</strong></td>
                    <td class="text-end no-border"><strong>{{ $payment->amount }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
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