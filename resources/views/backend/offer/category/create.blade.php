@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Offer</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Category</li>
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
                        <h5>Create Category</h5><span>Create New Offer Category</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('offer.category.save') }}">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label req">Offer Category Name</label>
                                    {{ html()->text($name = 'name', $value = old('name'))->class('form-control')->placeholder('Category Name') }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Discount %</label>
                                    {{ html()->text($name = 'discount_percentage', $value = old('discount_percentage'))->class('form-control')->maxlength('3')->placeholder('0%') }}
                                    @error('discount_percentage')
                                    <small class="text-danger">{{ $errors->first('discount_percentage') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Buy</label>
                                    {{ html()->text($name = 'buy_number', $value = old('buy_number'))->class('form-control')->maxlength('3')->placeholder('0') }}
                                    @error('buy_number')
                                    <small class="text-danger">{{ $errors->first('buy_number') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Get</label>
                                    {{ html()->text($name = 'get_number', $value = old('get_number'))->class('form-control')->maxlength('3')->placeholder('0') }}
                                    @error('get_number')
                                    <small class="text-danger">{{ $errors->first('get_number') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch</label>
                                    {{ html()->select($name = 'branch_id', $value = $branches->pluck('name', 'id'), old('branch_id'))->class('form-control select2')->placeholder('Select') }}
                                    @error('branch_id')
                                    <small class="text-danger">{{ $errors->first('branch_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Valid From</label>
                                    {{ html()->date($name = 'valid_from', $value = old('valid_from') ?? date('Y-m-d'))->class('form-control') }}
                                    @error('valid_from')
                                    <small class="text-danger">{{ $errors->first('valid_from') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Valid To</label>
                                    {{ html()->date($name = 'valid_to', $value = old('valid_to') ?? date('Y-m-d'))->class('form-control') }}
                                    @error('valid_to')
                                    <small class="text-danger">{{ $errors->first('valid_to') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Description</label>
                                    {{ html()->text($name = 'description', $value = old('description'))->class('form-control')->placeholder('Description') }}
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