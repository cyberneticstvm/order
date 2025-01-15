@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Vehicle Payment</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Vehicle Payment</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
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
                        <h5>Create Vehicle Payment</h5><span>Create Vehicle Payment for {{ $vehicle->reg_number }}</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('vehicle.payment.save', $vehicle->id) }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">Amount</label>
                                    {{ html()->number($name = 'amount', $value = $vehicle->fee, '1', '', '')->class('form-control')->attribute('readonly', 'true') }}
                                    @error('amount')
                                    <small class="text-danger">{{ $errors->first('amount') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Payment Mode</label>
                                    {{ html()->select('payment_mode', $pmodes->pluck('name', 'id'), old('payment_mode'))->class('form-control')->placeholder('Payment Mode') }}
                                    @error('payment_mode')
                                    <small class="text-danger">{{ $errors->first('payment_mode') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-7">
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
    <!-- Container-fluid Ends-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Payment Register for {{ $vehicle->reg_number }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Owner Name</th>
                                        <th>Contact Number</th>
                                        <th>Reg. number</th>
                                        <th>Amount</th>
                                        <th>Pmode</th>
                                        <th>Paid Date</th>
                                        <th>Status</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $key => $payment)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $payment->vehicle->owner_name }}</td>
                                        <td>{{ $payment->vehicle->contact_number }}</td>
                                        <td>{{ $payment->vehicle->reg_number }}</td>
                                        <td>{{ $payment->amount }}</td>
                                        <td>{{ $payment->paymentmode->name }}</td>
                                        <td>{{ $payment->created_at->format('d.M.Y') }}</td>
                                        <td class="text-center">{!! $payment->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('vehicle.payment.delete', encrypt($payment->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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
</div>
@endsection