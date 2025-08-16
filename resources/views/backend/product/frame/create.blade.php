@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Frame Products</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Frame Product</li>
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
                        <h5>Create Product</h5><span>Create New Frame Product</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('product.frame.save') }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Product Name</label>
                                    {{ html()->text($name = 'name', $value = old('name'))->class('form-control')->placeholder('Product Name') }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Type of Product</label>
                                    {{ html()->select($name = 'type_id', $value = $ptypes->where('attribute', 'type')->pluck('name', 'id'), old('type_id'))->class('form-control select2')->placeholder('Select') }}
                                    @error('type_id')
                                    <small class="text-danger">{{ $errors->first('type_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Shape of Product</label>
                                    {{ html()->select($name = 'shape_id', $value = $ptypes->where('attribute', 'shape')->pluck('name', 'id'), old('shape_id'))->class('form-control select2')->placeholder('Select') }}
                                    @error('shape_id')
                                    <small class="text-danger">{{ $errors->first('shape_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Product Material</label>
                                    {{ html()->select($name = 'material', $value = $ptypes->where('attribute', 'material')->pluck('name', 'id'), '')->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Product Code</label>
                                    {{ html()->text($name = 'code', $value = productcode($ptypes->first()->category))->class('form-control')->placeholder('Product Code') }}
                                    @error('code')
                                    <small class="text-danger">{{ $errors->first('code') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Manufacturer</label>
                                    {{ html()->select($name = 'manufacturer_id', $value = $manufacturers, old('manufacturer_id'))->class('form-control select2')->placeholder('Select') }}
                                    @error('manufacturer_id')
                                    <small class="text-danger">{{ $errors->first('manufacturer_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">MRP</label>
                                    {{ html()->number($name = 'mrp', $value = old('mrp'), $min="", $max="", $step="any")->class('form-control')->placeholder('0.00') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Selling Price</label>
                                    {{ html()->number($name = 'selling_price', $value = old('selling_price'), $min="1", $max="", $step="any")->class('form-control')->placeholder('0.00') }}
                                    @error('selling_price')
                                    <small class="text-danger">{{ $errors->first('selling_price') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tax %</label>
                                    {{ html()->number($name = 'tax_percentage', $value = old('tax_percentage'), $min="", $max="100", $step="any")->class('form-control')->placeholder('0%') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Reorder Level</label>
                                    {{ html()->number($name = 'reorder_level', $value = old('reorder_level'), $min="1", $max="", $step="any")->class('form-control')->placeholder('0') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Eye Size</label>
                                    {{ html()->text($name = 'eye_size', old('eye_size'))->class('form-control')->placeholder('Eye Size') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Bridge Size</label>
                                    {{ html()->text($name = 'bridge_size', old('bridge_size'))->class('form-control')->placeholder('Bridge Size') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Temple Size</label>
                                    {{ html()->text($name = 'temple_size', old('temple_size'))->class('form-control')->placeholder('Temple Size') }}
                                </div>
                                <!--<div class="col-md-4">
                                    <label class="form-label">Collection</label>
                                    {{ html()->select($name = 'collection_id', $collection->where('category', 'frame')->where('attribute', 'collection')->pluck('name', 'id'), old('collection_id'))->class('form-control select2')->placeholder('Select') }}
                                </div>-->
                                <div class="col-md-3">
                                    <label class="form-label">Color</label>
                                    {{ html()->select($name = 'color', $collection->where('category', 'frame')->where('attribute', 'colour')->pluck('name', 'id'), old('color'))->class('form-control select2')->placeholder('Select') }}
                                    @error('color')
                                    <small class="text-danger">{{ $errors->first('color') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label req">Collection <small>(Multiple selection enabled)</small></label>
                                    {{ html()->select($name = 'collection_id[]', $collection->where('category', 'frame')->where('attribute', 'collection')->pluck('name', 'id'), NULL)->class('form-control select2')->multiple() }}
                                    @error('collection_id')
                                    <small class="text-danger">{{ $errors->first('collection_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Product Description</label>
                                    {{ html()->text($name = 'description', $value = old('description') )->class('form-control')->placeholder('Product Description if any') }}
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