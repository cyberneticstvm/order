@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Daybook</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Daybook</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('report.daybook.fetch') }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">From Date</label>
                                    {{ html()->date($name = 'from_date', $value = ($inputs[0]) ?? date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('from_date')
                                    <small class="text-danger">{{ $errors->first('from_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">To Date</label>
                                    {{ html()->date($name = 'to_date', $value = ($inputs[1]) ?? date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('to_date')
                                    <small class="text-danger">{{ $errors->first('to_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch</label>
                                    {{ html()->select($name = 'branch', $value = $branches, ($inputs[2]) ?? old('branch'))->class('form-control select2')->placeholder('Select') }}
                                    @error('branch')
                                    <small class="text-danger">{{ $errors->first('branch') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Fetch</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Daybook</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mx-auto w-75">
                                <thead>
                                    <tr>
                                        <th width="45%">Particulars</th>
                                        <th width="15%">Income</th>
                                        <th width="15%">Expense</th>
                                        <th width="25%" colspan="5" class="text-center">Split up</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3"></th>
                                        <th width="">Cash</th>
                                        <th width="">Bank</th>
                                        <th width="">Card</th>
                                        <th width="">UPI</th>
                                        <th width="">Other</th>
                                    </tr>
                                </thead>
                                @if($data)
                                @php
                                $data = json_decode($data);
                                $tot = $data->paid_total + $data->income_total + $opening_balance;
                                @endphp
                                <tbody>
                                    <tr>
                                        <td class="text-success">
                                            Opening Balance
                                        </td>
                                        <td class="text-end text-success fw-bold">
                                            {{ number_format($opening_balance, 2) }}
                                        </td>
                                        <td></td>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Order
                                        </td>
                                        <td class="text-end">
                                            <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="ord" data-mode="0" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->paid_total, 2) }}</a>
                                        </td>
                                        <td></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="ord" data-mode="1" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->paid_total_cash, 2) }}</a></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="ord" data-mode="4" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->paid_total_bank, 2) }}</a></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="ord" data-mode="2" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->paid_total_card, 2) }}</a></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="ord" data-mode="3" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->paid_total_upi, 2) }}</a></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="ord" data-mode="5" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->paid_total_other, 2) }}</a></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Order Split up
                                        </td>
                                        <td class="text-center text-secondary">Advance:<br /> <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="advance" data-mode="advance" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->advance_total, 2) }}</a> </td>
                                        <td colspan="2" class="text-center text-secondary">Advance via Receipts:<br /> <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="advance" data-mode="advance1" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->advance_after_order_total, 2) }}</a> </td>
                                        <td class="text-center text-secondary">Receipts:<br /> <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="advance" data-mode="other" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->receipts_order_total, 2) }}</a> </td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Other Income
                                        </td>
                                        <td class="text-end">
                                            <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="income" data-mode="0" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->income_total, 2) }}</a>
                                        </td>
                                        <td></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="income" data-mode="1" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->income_total_cash, 2) }}</a></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="income" data-mode="4" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->income_total_bank, 2) }}</a></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="income" data-mode="2" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->income_total_card, 2) }}</a></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="income" data-mode="3" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->income_total_upi, 2) }}</a></td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="income" data-mode="5" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->income_total_other, 2) }}</a></td>
                                    </tr>
                                    <tr>
                                        <td>Voucher</td>
                                        <td class="text-end">{{ number_format($data->voucher_receipt_total, 2) }}</td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="voucher" data-mode="5" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->voucher_payment_total, 2) }}</a></td>
                                        <td class="text-end">{{ number_format($data->voucher_income_total_cash, 2) }}</td>
                                        <td class="text-end">{{ number_format($data->voucher_income_total_bank, 2) }}</td>
                                        <td class="text-end">{{ number_format($data->voucher_income_total_card, 2) }}</td>
                                        <td class="text-end">{{ number_format($data->voucher_income_total_upi, 2) }}</td>
                                        <td class="text-end">{{ number_format($data->voucher_income_total_other, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Ad Payment</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end"><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="ads" data-mode="5" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->vehicle_payment_total_cash + $data->vehicle_payment_total_upi, 2) }}</a></td>
                                        <td class="text-end">{{ number_format($data->vehicle_payment_total_cash, 2) }}</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">{{ number_format($data->vehicle_payment_total_upi, 2) }}</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold text-danger">
                                            Total
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            {{ number_format($tot, 2) }}
                                        </td>
                                        <td></td>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold text-success">
                                            Day Total
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            {{ number_format($tot, 2) }}
                                        </td>
                                        <td></td>
                                        <td class="text-end">{{ number_format($data->paid_total_cash + $data->income_total_cash + $data->voucher_income_total_cash, 2) }}</td>
                                        <td class="text-end">{{ number_format($data->paid_total_bank + $data->income_total_bank + $data->voucher_income_total_bank, 2) }}</td>
                                        <td class="text-end">{{ number_format($data->paid_total_card + $data->income_total_card + $data->voucher_income_total_card, 2) }}</td>
                                        <td class="text-end">{{ number_format($data->paid_total_upi + $data->income_total_upi + $data->voucher_income_total_upi, 2) }}</td>
                                        <td class="text-end">{{ number_format($data->paid_total_other + $data->income_total_other + $data->voucher_income_total_other, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">
                                            Expenses
                                        </td>
                                        <td></td>
                                        <td class="text-end text-secondary">
                                            <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="expense" data-mode="0" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->expense_total, 2) }}</a>
                                        </td>
                                        <td class="text-end">{{ number_format($data->expense_total_cash, 2) }}</td>
                                        <td colspan="4"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">
                                            Bank Transfer
                                        </td>
                                        <td></td>
                                        <td class="text-end text-secondary">
                                            <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="bank" data-mode="0" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->bank_transfer_total, 2) }}</a>
                                        </td>
                                        <td class="text-center text-secondary">CDM<br />{{ number_format($data->bank_transfer_cdm, 2) }}</td>
                                        <td class="text-center text-secondary">Cash<br />{{ number_format($data->bank_transfer_cash, 2) }}</td>
                                        <td class="text-center">Other<br /><a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="ord" data-mode="5" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->paid_total_other, 2) }}</a></td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold text-success">
                                            Day Closing
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            {{ number_format(($data->paid_total_cash + $data->income_total_cash + $data->voucher_income_total_cash + $opening_balance) - ($data->expense_total_cash + $data->bank_transfer_total + $data->voucher_paid_total_cash + $data->vehicle_payment_total_cash), 2) }}
                                        </td>
                                        <td></td>
                                        <td colspan="5"></td>
                                    </tr>
                                </tbody>
                                @else
                                <tr>
                                    <td colspan="4" class="text-center">No records found!</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="drawer drawer-left slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="daybookDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document"></div>
</div>
@endsection