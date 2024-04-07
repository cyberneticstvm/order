@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Customer</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Customer</li>
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
                        <div class="row">
                            <div class="col">
                                <h5>Update Customer</h5><span>Update Customer</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('customer.update', $customer->id) }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Name</label>
                                    {{ html()->text($name = 'name', $value = $customer->name)->class('form-control')->placeholder('Customer Name')->required() }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Age</label>
                                    {{ html()->number($name = 'age', $value = $customer->age)->class('form-control')->placeholder('0') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ">Address</label>
                                    {{ html()->text($name = 'address', $value = $customer->address)->class('form-control')->placeholder('Address') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Mobile</label>
                                    {{ html()->text($name = 'mobile', $value = $customer->mobile)->class('form-control custmob')->maxlength('10')->placeholder('Mobile') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Alt. Mobile</label>
                                    {{ html()->text($name = 'alt_mobile', $customer->alt_mobile)->class('form-control')->maxlength('10')->placeholder('Alt Mobile') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">GSTIN</label>
                                    {{ html()->text($name = 'gstin', $value = $customer->gstin)->class('form-control')->placeholder('GSTIN') }}
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Company Name</label>
                                    {{ html()->text($name = 'company_name', $value = $customer->company_name)->class('form-control')->placeholder('Company Name') }}
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