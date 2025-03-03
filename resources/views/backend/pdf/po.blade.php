<!DOCTYPE html>
<html>

<head>
    <title>Devi Eye hospitals</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: normal;
        }

        .font-big {
            font-size: 15px;
        }

        .text-center {
            text-align: center;
        }

        .text-right,
        .text-end {
            text-align: right;
        }

        .table,
        .no-border {
            border: none !important;
        }

        .mx-auto {
            margin: 0 auto !important;
        }

        .bordered td,
        .bordered th,
        .bordered {
            border: 1px solid #e6e6e6;
        }

        .border-0 {
            border: 0;
        }

        th,
        td {
            border: 1px solid #262525;
            padding: 5px;
            text-align: left;
        }

        .pd-1 {
            padding: 3px !important;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-30 {
            margin-top: 30px;
        }

        .mt-50 {
            margin-top: 50px;
        }

        .pt-50 {
            padding-top: 50px;
        }

        .mt-100 {
            margin-top: 100%;
        }

        .mb-50 {
            margin-bottom: 50px;
        }

        .ms-15 {
            margin-left: 15px;
        }

        .h-50>tr>td {
            height: 50px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-danger {
            color: red;
        }

        .txt {
            font-size: 10px !important;
        }

        .b-0 {
            border-bottom: none !important;
            border-top: none !important;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            line-height: 35px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <table width=100%>
                    <tr>
                        <td class="no-border" width="50%">
                            @if($po->customer == 'store')
                            <img src="./backend/assets/images/logo/devi-logo.png" width='50%' />
                            @else
                            <img src="./backend/assets/images/logo/hospital-logo.jpeg" width='50%' />
                            @endif
                            <div class="ms-15">{!! nl2br($po->branch_address) !!}</div>
                        </td>
                        <td class="no-border">
                            <h4 class="text-end">PURCHASE ORDER</h4>
                            <p class="text-end">
                                PO NO: {{ $po->po_number }}<br />
                                DATE: {{ $po->date->format('d.M.Y') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="text-end">For: {!! nl2br($po->for) !!}</div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table width=100%>
                    <tr>
                        <td class="no-border" width="50%">
                            <h3>TO</h3>
                            {!! nl2br($po->to) !!}
                        </td>
                        <td class="no-border text-end">
                            <h3>Shipping Address</h3>
                            {!! nl2br($po->shipping_address) !!}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table width=100% class="mt-10" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>SL No</td>
                        <td>Description</td>
                        <td>Qty</td>
                        <td>Rate</td>
                        <td>Tax %</td>
                        <td>Tax Amount</td>
                        <td>Total</td>
                    </tr>
                    @forelse($po->details as $key => $item)
                    <tr>
                        <td class="text-center b-0">{{ $key + 1 }}</td>
                        <td class="b-0">{{ $item->product }}</td>
                        <td class="b-0 text-center">{{ $item->qty }}</td>
                        <td class="b-0 text-end">{{ $item->rate }}</td>
                        <td class="b-0 text-end">{{ number_format($item->tax_percentage, 2) }}</td>
                        <td class="b-0 text-end">{{ $item->tax_amount }}</td>
                        <td class="b-0 text-end">{{ $item->total }}</td>
                    </tr>
                    @empty
                    @endforelse
                    <tr style="border-top: 1px solid #000;">
                        <td colspan="6" class="text-end no-border">Sub Total</td>
                        <td class="text-end no-border">{{ number_format($po->sub_total, 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #000;">
                        <td colspan="6" class="text-end no-border">Shipping & Delivery</td>
                        <td class="text-end no-border">{{ number_format($po->shipping, 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #000;">
                        <td colspan="4" class="no-border">{{ $po->additional_expense_remarks }}</td>
                        <td colspan="2" class="text-end no-border">Additional Expenses</td>
                        <td class="text-end no-border">{{ number_format($po->additional_expense, 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #000;">
                        <td colspan="4" class="no-border">{{ $po->discount_remarks }}</td>
                        <td colspan="2" class="text-end no-border">Discount</td>
                        <td class="text-end no-border">{{ number_format($po->discount, 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #000;">
                        <td colspan="4" class="no-border">Payment Mode: {{ $po->pmode?->name }}</td>
                        <td colspan="2" class="text-end no-border">Advance</td>
                        <td class="text-end no-border">{{ number_format($po->advance, 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #000;">
                        <td colspan="6" class="text-end no-border fw-bold">Balance</td>
                        <td class="text-end no-border fw-bold">{{ number_format($po->balance, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col text-end mt-30">
                <p class="fw-bold">Authorised Signatory</p>
            </div>
            <div class="col mt-10">
                <p class="fw-bold">Notes</p>
                {!! nl2br($po->notes) !!}
            </div>
            <div class="col mt-10">
                <p class="fw-bold">Terms & Conditions</p>
                {!! nl2br($po->terms) !!}
            </div>
        </div>
        <footer>
            <p>This PO is system-generated and does not require a signature for validation.</p>
        </footer>
    </div>
</body>

</html>