@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Failed Uploads</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Upload</li>
                        <li class="breadcrumb-item active">Failed Uploads</li>
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
                        <div class="card-wrapper text-center">
                            <h5 class="text-info mb-5">Some products weren't uploaded. Please click the download link below for more info.</h5>
                            <a href="{{ route('upload.failed.export') }}">Download</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection