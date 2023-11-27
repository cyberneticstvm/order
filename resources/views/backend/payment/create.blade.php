@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Patient Payment</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Payment</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row mb-3">
                            <div class="col">
                                <h5 class="text-secondary">Patient Name: {{ $patient->name ?? '' }}</h5>
                            </div>
                            <div class="col text-end">
                                <h5 class="text-secondary">Patient ID: {{ $patient->patient_id ?? '' }}</h5>
                            </div>
                        </div>
                        <h4>Create Patient Payment</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('patient.payment.save') }}">
                                @csrf
                                <input type="hidden" name="consultation_id" value="{{ $consultation?->id ?? 0 }}" />
                                <input type="hidden" name="patient_id" value="{{ $patient?->id ?? 0 }}" />
                                <div class="col-md-2">
                                    <label class="form-label req">Amount</label>
                                    {{ html()->number($name = 'amount', $value = old('amount'), $min='1', $max='', $step='any')->class('form-control')->placeholder('0.00') }}
                                    @error('amount')
                                    <small class="text-danger">{{ $errors->first('amount') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Payment Mode</label>
                                    {{ html()->select($name = 'payment_mode', $value = $pmodes, old('payment_mode'))->class('form-control select2')->placeholder('Select') }}
                                    @error('payment_mode')
                                    <small class="text-danger">{{ $errors->first('payment_mode') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Payment Type</label>
                                    {{ html()->select($name = 'payment_type', $value = paymentypes(), old('payment_type'))->class('form-control select2')->placeholder('Select') }}
                                    @error('payment_type')
                                    <small class="text-danger">{{ $errors->first('payment_type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Notes</label>
                                    {{ html()->text($name = 'notes', $value = old('notes'))->class('form-control')->placeholder('Notes') }}
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Save</button>
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
                        <h4>Patient Transaction Records</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Medical Record Number</th>
                                        <th>Payment Owed</th>
                                        <th>Payment Recieved</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                @if($patient)
                                <tbody>
                                    @forelse($patient?->consultation as $key => $item)
                                    @php
                                    $arr = json_decode(owedTotal($item->id)); $otot = 0; $ptot = 0;
                                    $tot = $arr->registration_fee + $arr->consultation_fee + $arr->procedure_fee + $arr->pharmacy + $arr->store;
                                    $paid = $item->payments->sum('amount');
                                    $otot += $tot; $ptot += $paid;
                                    @endphp
                                    <td><a href="javascript:void(0)" class="paymentDetails" data-consultation-id="{{ $item->id }}" data-drawer="drawer_{{ $item->id }}">{{ $item->mrn }}</a></td>
                                    <td class="text-end fw-bold">{{ number_format($tot, 2) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($paid, 2) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($tot - $paid, 2) }}</td>
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold">{{ number_format($otot, 2) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($ptot, 2) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($otot - $ptot, 2) }}</td>
                                    </tr>
                                </tfoot>
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Patient Payment Records</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Medical Record No</th>
                                        <th>Mode</th>
                                        <th>Type</th>
                                        <th>Notes</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $key => $payment)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $payment->consultation->mrn }}</td>
                                        <td>{{ $payment->paymentmode->name }}</td>
                                        <td>{{ $payment->payment_type }}</td>
                                        <td>{{ $payment->notes }}</td>
                                        <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No records found!</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold">{{ number_format($payments->sum('amount'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if($patient)
@forelse($patient?->consultation as $key => $item)
<div class="drawer drawer-left slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="drawer_{{ $item->id }}">
    <div class="drawer-content drawer-content-scrollable" role="document">
        <div class="drawer-header">
            <h6 class="drawer-title" id="drawer-3-title">Medical Record Number: {{ $item->mrn }}</h6>
        </div>
        <div class="drawer-body table-responsive"></div>
        <div class="drawer-footer">Patient Name: {{ $patient->name }}, Patient ID: {{ $patient->patient_id }}</div>
    </div>
</div>
@empty
@endforelse
@endif
@endsection