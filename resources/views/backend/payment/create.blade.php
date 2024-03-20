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
                        <div class="row mb-5">
                            <div class="col-md-2">
                                <h5 class="text-secondary">Patient Name: {{ $order->name ?? '' }}</h5>
                            </div>
                            <div class="col-md-2">
                                <h5 class="text-secondary">Order Number: {{ $order?->branch?->code }}/{{ $order?->id }}</h5>
                            </div>
                            <div class="col-md-2">
                                <h5 class="text-warning">Total Amount: {{ $order->invoice_total }}</h5>
                            </div>
                            <div class="col-md-2">
                                <h5 class="text-success">Paid Amount: {{ $order->payments->sum('amount') }}</h5>
                            </div>
                            <div class="col-md-2">
                                <h5 class="text-danger">Balance: {{ number_format($order->invoice_total - $order->payments->sum('amount'), 2)  }}</h5>
                            </div>
                        </div>
                        <h4>Create Patient Payment</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('patient.payment.save') }}">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order?->id ?? 0 }}" />
                                <input type="hidden" name="consultation_id" value="{{ $order?->consultation_id ?? 0 }}" />
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
                                <div class="col-md-2">
                                    {{ html()->checkbox($name = 'generate_invoice', $checked=true, $value = '1')->class('form-check-input') }}
                                    <label class="form-label">Generate Invoice</label>
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
                                        <th>Date</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $key => $payment)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $payment->consultation_id }}</td>
                                        <td>{{ $payment->paymentmode->name }}</td>
                                        <td>{{ $payment->payment_type }}</td>
                                        <td>{{ $payment->notes }}</td>
                                        <td>{{ $payment->created_at->format('d, M Y h:i a') }}</td>
                                        <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No records found!</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Total</td>
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
@endsection