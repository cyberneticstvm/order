@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Update Promotion Contact</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Promotion Contact</li>
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
                        <h4>Update Promotion Contact</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('promotion.contact.update', $contact->id) }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Name</label>
                                    {{ html()->text($name = 'name', $contact->name)->class('form-control')->placeholder('Customer Name') }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Contact Number</label>
                                    {{ html()->text($name = 'contact_number', $contact->contact_number)->class('form-control')->maxlength('10')->placeholder('Contact Number') }}
                                    @error('contact_number')
                                    <small class="text-danger">{{ $errors->first('contact_number') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Type</label>
                                    {{ html()->select('type', array('include' => 'Include', 'exclude' => 'Exclude'), $contact->type)->class('form-control select2')->placeholder('Select') }}
                                    @error('type')
                                    <small class="text-danger">{{ $errors->first('type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Entity</label>
                                    {{ html()->select('entity', array('store' => 'Store'), $contact->entity)->class('form-control select2')->placeholder('Select') }}
                                    @error('entity')
                                    <small class="text-danger">{{ $errors->first('entity') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch</label>
                                    {{ html()->select('branch_id', $branches, $contact->branch_id)->class('form-control select2')->placeholder('Select') }}
                                    @error('branch_id')
                                    <small class="text-danger">{{ $errors->first('branch_id') }}</small>
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
</div>
@endsection