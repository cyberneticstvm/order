@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Lens Products</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Lens Product</li>
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
                        <h5>Create Product</h5><span>Update Lens Product</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('product.lens.update', $product->id) }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Product Name</label>
                                    {{ html()->text($name = 'name', $value = $product->name)->class('form-control')->placeholder('Product Name') }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Type of Product</label>
                                    {{ html()->select($name = 'type_id', $value = $ptypes->where('attribute', 'type')->pluck('name', 'id'), $product->type_id)->class('form-control select2')->placeholder('Select') }}
                                    @error('type_id')
                                    <small class="text-danger">{{ $errors->first('type_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Product Material</label>
                                    {{ html()->select($name = 'material', $value = $ptypes->where('attribute', 'material')->pluck('name', 'id'), $product->material)->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Coating</label>
                                    {{ html()->select($name = 'coating_id', $value = $ptypes->where('attribute', 'coating')->pluck('name', 'id'), $product->coating_id)->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Product Code</label>
                                    {{ html()->text($name = 'code', $value = $product->code)->class('form-control')->attribute('readonly', 'true')->placeholder('Product Code') }}
                                    @error('code')
                                    <small class="text-danger">{{ $errors->first('code') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Manufacturer</label>
                                    {{ html()->select($name = 'manufacturer_id', $value = $manufacturers, $product->manufacturer_id)->class('form-control select2')->placeholder('Select') }}
                                    @error('manufacturer_id')
                                    <small class="text-danger">{{ $errors->first('manufacturer_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">MRP</label>
                                    {{ html()->number($name = 'mrp', $value = $product->mrp, $min="", $max="", $step="any")->class('form-control')->placeholder('0.00') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Selling Price</label>
                                    {{ html()->number($name = 'selling_price', $value = $product->selling_price, $min="1", $max="", $step="any")->class('form-control')->placeholder('0.00') }}
                                    @error('selling_price')
                                    <small class="text-danger">{{ $errors->first('selling_price') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tax %</label>
                                    {{ html()->number($name = 'tax_percentage', $value = $product->tax_percentage, $min="", $max="100", $step="any")->class('form-control')->placeholder('0%') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Reorder Level</label>
                                    {{ html()->number($name = 'reorder_level', $value = $product->reorder_level, $min="1", $max="", $step="any")->class('form-control')->placeholder('0') }}
                                    @error('reorder_level')
                                    <small class="text-danger">{{ $errors->first('reorder_level') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Product Description</label>
                                    {{ html()->text($name = 'description', $product->description )->class('form-control')->placeholder('Product Description if any') }}
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