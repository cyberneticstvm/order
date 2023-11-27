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
                        <li class="breadcrumb-item active">Update</li>
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
                        <h4>Update Patient Payment</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('patient.payment.update', $payment->id) }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">Amount</label>
                                    {{ html()->number($name = 'amount', $value = $payment->amount, $min='1', $max='', $step='any')->class('form-control')->placeholder('0.00') }}
                                    @error('amount')
                                    <small class="text-danger">{{ $errors->first('amount') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Payment Mode</label>
                                    {{ html()->select($name = 'payment_mode', $value = $pmodes, $payment->payment_mode)->class('form-control select2')->placeholder('Select') }}
                                    @error('payment_mode')
                                    <small class="text-danger">{{ $errors->first('payment_mode') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Payment Type</label>
                                    {{ html()->select($name = 'payment_type', $value = paymentypes(), $payment->payment_type)->class('form-control select2')->placeholder('Select') }}
                                    @error('payment_type')
                                    <small class="text-danger">{{ $errors->first('payment_type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Notes</label>
                                    {{ html()->text($name = 'notes', $value = $payment->notes)->class('form-control')->placeholder('Notes') }}
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection