@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Supplier</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                            <svg class="stroke-icon">
                                <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item">Supplier</li>
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
                    <h5>Update Supplier</h5><span>Update Supplier</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('supplier.update', $supplier->id) }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Supplier Name</label>
                                    {{ html()->text($name = 'name', $value = $supplier->name)->class('form-control')->placeholder('Supplier Name') }}
                                    @error('name')
                                        <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>                                
                                <div class="col-md-3">
                                    <label class="form-label req">Contact Person</label>
                                    {{ html()->text($name = 'contact_person', $value = $supplier->contact_person)->class('form-control')->placeholder('Contact Person') }}
                                    @error('contact_person')
                                        <small class="text-danger">{{ $errors->first('contact_person') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Mobile Number</label>
                                    {{ html()->text($name = 'mobile', $value = $supplier->mobile)->class('form-control')->maxlength(10)->placeholder('Mobile Number') }}
                                    @error('mobile')
                                        <small class="text-danger">{{ $errors->first('mobile') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Email</label>
                                    {{ html()->email($name = 'email', $value = $supplier->email)->class('form-control')->placeholder('Email') }}
                                    @error('email')
                                        <small class="text-danger">{{ $errors->first('email') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label req">Address</label>
                                    {{ html()->text($name = 'address', $value = $supplier->address)->class('form-control')->placeholder('Address') }}
                                    @error('address')
                                        <small class="text-danger">{{ $errors->first('address') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">GSTIN</label>
                                    {{ html()->text($name = 'gstin', $value = $supplier->gstin)->class('form-control')->placeholder('GSTIN') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Expiry Notification </label> <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<strong>Notify product expiry in days.</strong>"><i class="fa fa-info txt-info"></i></a>
                                    {{ html()->number($name = 'expiry_notification', $value = $supplier->expiry_notification)->class('form-control')->placeholder('0 Days') }}
                                    @error('expiry_notification')
                                        <small class="text-danger">{{ $errors->first('expiry_notification') }}</small>
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