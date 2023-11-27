@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Camp</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                            <svg class="stroke-icon">
                                <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item">Camp</li>
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
                    <h5>Create Camp</h5><span>Create New Camp</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('camp.save') }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">From Date</label>
                                    {{ html()->date($name = 'from_date', $value = old('from_date'))->class('form-control')->placeholder(date('Y-m-d')) }}
                                    @error('from_date')
                                        <small class="text-danger">{{ $errors->first('from_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">To Date</label>
                                    {{ html()->date($name = 'to_date', $value = old('to_date'))->class('form-control')->placeholder(date('Y-m-d')) }}
                                    @error('to_date')
                                        <small class="text-danger">{{ $errors->first('to_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Camp Name</label>
                                    {{ html()->text($name = 'name', $value = old('name'))->class('form-control')->placeholder('Camp Name') }}
                                    @error('name')
                                        <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>                                
                                <div class="col-md-4">
                                    <label class="form-label req">Venue</label>
                                    {{ html()->text($name = 'venue', $value = old('venue'))->class('form-control')->placeholder('Venue') }}
                                    @error('venue')
                                        <small class="text-danger">{{ $errors->first('venue') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Address</label>
                                    {{ html()->text($name = 'address', $value = old('address'))->class('form-control')->placeholder('Address') }}
                                    @error('address')
                                        <small class="text-danger">{{ $errors->first('address') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Cordinator</label>
                                    {{ html()->select($name = 'cordinator', $users, old('cordinator'))->class('form-control select2')->placeholder('Select') }}
                                    @error('cordinator')
                                        <small class="text-danger">{{ $errors->first('cordinator') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Optometrist</label>
                                    {{ html()->select($name = 'optometrist', $users, old('optometrist'))->class('form-control select2')->placeholder('Select') }}                                    
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Camp Type</label>
                                    {{ html()->select($name = 'camp_type', $ctypes, old('camp_type'))->class('form-control select2')->placeholder('Select') }}
                                    @error('camp_type')
                                        <small class="text-danger">{{ $errors->first('camp_type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Notes / Remarks</label>
                                    {{ html()->textarea($name = 'notes', old('notes'))->class('form-control')->rows('3')->placeholder('Notes / Remarks') }}                                    
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