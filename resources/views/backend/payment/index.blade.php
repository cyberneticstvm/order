@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Patient Payment Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Patient Payments</li>
                        <li class="breadcrumb-item active">Patient Payment Register</li>
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
                            <form class="row g-3" method="post" action="{{ route('patient.payment.fetch') }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Medical Record Number</label>&nbsp;&nbsp;<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<strong>Number Parts Only.</strong>"><i class="fa fa-info txt-info"></i></a>
                                    <div class="input-group">
                                        {{ html()->number($name = 'medical_record_number', $value = old('medical_record_number'))->class('form-control')->placeholder('Medical Record Number') }}
                                        <button class="btn btn-outline-warning btn-submit" id="button-addon2" type="submit">Fetch</button>
                                    </div>
                                    @error('medical_record_number')
                                    <small class="text-danger">{{ $errors->first('medical_record_number') }}</small>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <h5>Patient Payment Register</h5>
                            </div>
                            <div class="col text-end"><a href="{{ route('patient.payment.create', ['id' => encrypt(0)]) }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Patient Name</th>
                                        <th>Patient ID</th>
                                        <th>Medical Record No.</th>
                                        <th>Mode</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $key => $payment)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $payment?->patient?->name }}</td>
                                        <td>{{ $payment?->patient?->patient_id }}</td>
                                        <td>{{ $payment?->consultation?->mrn }}</td>
                                        <td>{{ $payment->paymentmode->name }}</td>
                                        <td>{{ $payment->payment_type }}</td>
                                        <td>{{ $payment->amount }}</td>
                                        <td>{!! $payment->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('patient.payment.edit', encrypt($payment->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('patient.payment.delete', encrypt($payment->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
@endsection