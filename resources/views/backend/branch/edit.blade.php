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
                        <h5>Update Branch</h5><span>Update Branch</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('branch.update', $branch->id) }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Branch Name</label>
                                    {{ html()->text($name = 'name', $value = $branch->name)->class('form-control')->placeholder('Branch Name') }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch Code</label>
                                    {{ html()->text($name = 'code', $value = $branch->code)->class('form-control')->placeholder('Branch Code') }}
                                    @error('code')
                                    <small class="text-danger">{{ $errors->first('code') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Email</label>
                                    {{ html()->email($name = 'email', $value = $branch->email)->class('form-control')->placeholder('Email') }}
                                    @error('email')
                                    <small class="text-danger">{{ $errors->first('email') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Phone Number</label>
                                    {{ html()->text($name = 'phone', $value = $branch->phone)->class('form-control')->maxlength(25)->placeholder('Phone') }}
                                    @error('phone')
                                    <small class="text-danger">{{ $errors->first('phone') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label req">Address</label>
                                    {{ html()->text($name = 'address', $value = $branch->address)->class('form-control')->placeholder('Address') }}
                                    @error('address')
                                    <small class="text-danger">{{ $errors->first('address') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">GSTIN</label>
                                    {{ html()->text($name = 'gstin', $value = $branch->gstin)->class('form-control')->maxlength(25)->placeholder('GSTIN') }}
                                    @error('gstin')
                                    <small class="text-danger">{{ $errors->first('gstin') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Display Capacity</label>
                                    {{ html()->number($name = 'display_capacity', $value = $branch->display_capacity, $min='0', $max='', $step='1')->class('form-control')->placeholder('0') }}
                                    @error('display_capacity')
                                    <small class="text-danger">{{ $errors->first('display_capacity') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Monthly Target</label>
                                    {{ html()->number($name = 'monthly_target', $value = $branch->monthly_target, $min='0', $max='', $step='1')->class('form-control')->placeholder('0.00') }}
                                    @error('monthly_target')
                                    <small class="text-danger">{{ $errors->first('monthly_target') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Target Percentage</label>
                                    {{ html()->number($name = 'target_percentage', $branch->target_percentage, $min='0', $max='100', $step='1')->class('form-control')->placeholder('0') }}
                                    @error('target_percentage')
                                    <small class="text-danger">{{ $errors->first('target_percentage') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Daily Expense Limit</label>
                                    {{ html()->number($name = 'daily_expense_limit', $value = $branch->daily_expense_limit, $min='1', $max='', $step='1')->class('form-control')->placeholder('0.00') }}
                                    @error('daily_expense_limit')
                                    <small class="text-danger">{{ $errors->first('daily_expense_limit') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Discount Percentage</label>
                                    {{ html()->number($name = 'discount_limit_percentage', $branch->discount_limit_percentage, $min='0', $max='100', $step='1')->class('form-control')->placeholder('0') }}
                                    @error('discount_limit_percentage')
                                    <small class="text-danger">{{ $errors->first('discount_limit_percentage') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Type</label>
                                    {{ html()->select('type', array('branch' => 'Branch', 'rx-lab' => 'Rx Lab', 'fitting-lab' => 'Fitting Lab', 'stock-lab' => 'Stock Lab', 'outside-lab' => 'Outside Lab'), $branch->type)->class('form-control')->placeholder('Select') }}
                                    @error('type')
                                    <small class="text-danger">{{ $errors->first('type') }}</small>
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