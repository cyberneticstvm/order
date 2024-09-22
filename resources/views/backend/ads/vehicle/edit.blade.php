@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Vehicle</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Vehicle</li>
                        <li class="breadcrumb-item active">Update</li>
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
                        <h5>Update Vehicle</h5><span>Update Vehicle</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('vehicle.update', $vehicle->id) }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Owner Name</label>
                                    {{ html()->text($name = 'owner_name', $value = $vehicle->owner_name)->class('form-control')->placeholder('Owner Name') }}
                                    @error('owner_name')
                                    <small class="text-danger">{{ $errors->first('owner_name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Reg. Number</label>
                                    {{ html()->text($name = 'reg_number', $value = $vehicle->reg_number)->class('form-control')->maxLength(10)->placeholder('Reg. Number') }}
                                    @error('reg_number')
                                    <small class="text-danger">{{ $errors->first('reg_number') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Contact Number</label>
                                    {{ html()->text($name = 'contact_number', $value = $vehicle->contact_number)->class('form-control')->maxLength(10)->placeholder('Contact Number') }}
                                    @error('contact_number')
                                    <small class="text-danger">{{ $errors->first('contact_number') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Fee</label>
                                    {{ html()->number($name = 'fee', $value = $vehicle->fee, '1', '', '')->class('form-control')->placeholder('0.00') }}
                                    @error('fee')
                                    <small class="text-danger">{{ $errors->first('fee') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Payment Terms (Days)</label>
                                    {{ html()->number($name = 'payment_terms', $value = $vehicle->payment_terms, '1', '', '')->class('form-control')->placeholder('0 days') }}
                                    @error('payment_terms')
                                    <small class="text-danger">{{ $errors->first('payment_terms') }}</small>
                                    @enderror
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
    <!-- Container-fluid Ends-->
</div>
@endsection