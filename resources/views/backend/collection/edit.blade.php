@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Collection / Type / Shape / Brand / Material</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Collection / Type / Shape / Brand / Material</li>
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
                        <h5>Update Collection / Type / Shape / Brand / Material</h5><span>Update Collection / Type / Shape / Brand / Material</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('collection.update', $collection->id) }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Name</label>
                                    {{ html()->text($name = 'name', $value = $collection->name)->class('form-control')->placeholder('Name') }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Category</label>
                                    {{ html()->select($name = 'category', $value = array('frame' => 'Frame', 'lens' => 'Lens', 'solution' => 'Solution', 'accessory' => 'Accessory'), $collection->category)->class('form-control')->placeholder('Category') }}
                                    @error('category')
                                    <small class="text-danger">{{ $errors->first('category') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Attribute</label>
                                    {{ html()->select($name = 'attribute', $value = array('type' => 'Type', 'shape' => 'Shape', 'collection' => 'Collection', 'colour' => 'Colour', 'material' => 'Material', 'brand' => 'Brand', 'coating' => 'Coating'), $collection->attribute)->class('form-control')->placeholder('Attribute') }}
                                    @error('attribute')
                                    <small class="text-danger">{{ $errors->first('attribute') }}</small>
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