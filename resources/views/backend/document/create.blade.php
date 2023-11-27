@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Documents</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                            <svg class="stroke-icon">
                                <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item">Documents</li>
                        <li class="breadcrumb-item active">Register</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 file-content">
                <div class="card">
                    <div class="card-header">
                    <h5>All Documents</h5><span>All Documents</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('document.save') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="patient_id" value="{{ $patient->id }}" />
                                <div class="col-md-4">
                                    <label class="form-label req">Choose File</label> <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<strong>Multiple file uploads enabled.</strong>"><i class="fa fa-info txt-info"></i></a>
                                    {{ html()->file($name = 'documents[]')->class('form-control')->multiple() }}
                                    @error('documents')
                                        <small class="text-danger">{{ $errors->first('documents') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Medical Record Number</label> <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<strong>Select a MRN against the document.</strong>"><i class="fa fa-info txt-info"></i></a>
                                    {{ html()->select($name = 'mrn', $value = $patient->consultation()->pluck('mrn', 'id'), old('mrn'))->class('form-control select2')->placeholder('Select') }}
                                    @error('mrn')
                                        <small class="text-danger">{{ $errors->first('mrn') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Description</label>
                                    {{ html()->text($name = 'description', $value=NULL)->class('form-control')->placeholder('Description'); }}
                                    @error('description')
                                        <small class="text-danger">{{ $errors->first('description') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body file-manager">
                      <h4 class="mb-3">All Files</h4>
                        <ul class="files">
                            @forelse($docs as $key => $item)
                            <li class="file-box">
                                <div class="card-header-right-icon text-end">
                                    <div class="dropdown icon-dropdown">
                                        <button class="btn dropdown-toggle" id="recentdropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentdropdown"><a class="dropdown-item fw-bold" href="{{ url($item->document_url) }}" target="_blank"><i class="fa fa-download"></i>&nbsp;Download</a><a class="dropdown-item fw-bold dlt txt-danger" href="{{ route('document.delete', encrypt($item->id)) }}"><i class="fa fa-trash txt-danger"></i>&nbsp;Delete</a></div>
                                    </div>
                                </div>
                                <div class="file-top">  
                                    <i class="fa fa-file-text-o txt-primary"></i>                                    
                                </div>
                                <div class="file-bottom text-left">
                                    <p class="mb-1"><b>File Name:</b> {{ $item->original_file_name }}</p>
                                    <p class="mb-1"><b>MRN:</b> {{ $item->consultation->mrn }}</p>
                                    <p class="mb-1"><b>Description:</b> {{ $item->description }}</p>
                                    <p> <b>Created At : </b>{{ $item->created_at->format('d, M Y h:i a') }}</p>
                                </div>
                            </li>
                            @empty
                            <h5 class="txt-danger">No files found!</h5>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
@endsection