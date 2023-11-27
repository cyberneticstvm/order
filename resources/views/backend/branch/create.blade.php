@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Branch</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                            <svg class="stroke-icon">
                                <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item">Branch</li>
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
                    <h5>Create Branch</h5><span>Create New Branch</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('branch.save') }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Branch Name</label>
                                    {{ html()->text($name = 'name', $value = old('name'))->class('form-control')->placeholder('Branch Name') }}
                                    @error('name')
                                        <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch Code</label>
                                    {{ html()->text($name = 'code', $value = old('code'))->class('form-control')->placeholder('Branch Code') }}
                                    @error('code')
                                        <small class="text-danger">{{ $errors->first('code') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Email</label>
                                    {{ html()->email($name = 'email', $value = old('email'))->class('form-control')->placeholder('Email') }}
                                    @error('email')
                                        <small class="text-danger">{{ $errors->first('email') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Phone Number</label>
                                    {{ html()->text($name = 'phone', $value = old('phone'))->class('form-control')->maxlength(10)->placeholder('Phone') }}
                                    @error('phone')
                                        <small class="text-danger">{{ $errors->first('phone') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label req">Address</label>
                                    {{ html()->text($name = 'address', $value = old('address'))->class('form-control')->placeholder('Address') }}
                                    @error('address')
                                        <small class="text-danger">{{ $errors->first('address') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">GSTIN</label>
                                    {{ html()->text($name = 'gstin', $value = old('gstin'))->class('form-control')->maxlength(10)->placeholder('GSTIN') }}
                                    @error('gstin')
                                        <small class="text-danger">{{ $errors->first('gstin') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Display Capacity</label>
                                    {{ html()->number($name = 'display_capacity', $value = old('display_capacity'), $min='0', $max='1000', $step='1')->class('form-control')->placeholder('0') }}
                                    @error('display_capacity')
                                        <small class="text-danger">{{ $errors->first('display_capacity') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Registration Fee</label>
                                    {{ html()->number($name = 'registration_fee', $value = old('registration_fee'), $min='0', $max='1000', $step='1')->class('form-control')->placeholder('0.00') }}
                                    @error('registration_fee')
                                        <small class="text-danger">{{ $errors->first('registration_fee') }}</small>
                                    @enderror
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
</div>
@endsection