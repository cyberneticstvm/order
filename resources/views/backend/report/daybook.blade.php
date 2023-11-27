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
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="90%">Particulars</th>
                                        <th>Income</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                @if($data)
                                @php
                                $data = json_decode($data);
                                $tot = $data->reg_fee_total + $data->consultation_fee_total + $data->procedure_fee_total + $data->order_total + $data->pharmacy_total;
                                @endphp
                                <tbody>
                                    <tr>
                                        <td>
                                            Registration Fee
                                        </td>
                                        <td class="text-end">
                                            <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="reg" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->reg_fee_total, 2) }}</a>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Consultation Fee
                                        </td>
                                        <td class="text-end">
                                            <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="con" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->consultation_fee_total, 2) }}</a>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Procedure Fee
                                        </td>
                                        <td class="text-end">
                                            <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="proc" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->procedure_fee_total, 2) }}</a>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Order
                                        </td>
                                        <td class="text-end">
                                            <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="ord" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->order_total, 2) }}</a>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Pharmacy
                                        </td>
                                        <td class="text-end">
                                            <a href="javascript:void(0)" class="dayBook" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-type="med" data-branch="{{ $inputs[2] }}" data-drawer="daybookDrawer">{{ number_format($data->pharmacy_total, 2) }}</a>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold text-danger">
                                            Total
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            {{ number_format($tot, 2) }}
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold text-success">
                                            Payment collected
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            {{ number_format($data->paid_total, 2) }}
                                        </td>
                                        <td>Cash</td>
                                        <td>{{ number_format($data->paid_total_cash, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>Bank</td>
                                        <td>{{ number_format($data->paid_total_other, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold text-danger">
                                            Balance to be collected
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            {{ number_format($tot - $data->paid_total, 2) }}
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">
                                            Expenses
                                        </td>
                                        <td class="text-end text-secondary">
                                            {{ number_format($data->expense_total, 2) }}
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold text-success">
                                            Day Total
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            {{ number_format($tot - $data->expense_total, 2) }}
                                        </td>
                                        <td></td>
                                        <td></td>
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