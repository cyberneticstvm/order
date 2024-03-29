@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Import Lenses</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Product</li>
                        <li class="breadcrumb-item active">Import Lenses</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('import.lenses.update') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label req">File Upload</label>
                                    <div class="input-group">
                                        {{ html()->file($name = 'file')->class('form-control') }}
                                        <button class="btn btn-outline-warning btn-submit" id="button-addon2" type="submit">Import</button>
                                    </div>
                                    @error('file')
                                    <small class="text-danger">{{ $errors->first('file') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <a href="{{ asset('/backend/assets/docs/Lenses.xlsx') }}">Download Format</a>
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