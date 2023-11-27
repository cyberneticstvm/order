@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Patient Procedure</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Patient Procedure</li>
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
                        <h5>Update Patient Procedure</h5><span>Update Patient Procedure</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('patient.procedure.update', $procedure->id) }}">
                                @csrf
                                <div class="col-4">
                                    <label class="form-label">Patient Name</label>
                                    {{ html()->text($name = 'name', $value = $procedure->patient->name)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Patient ID</label>
                                    {{ html()->text($name = 'pid', $value = $procedure->patient->patient_id)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Patient Mobile</label>
                                    {{ html()->text($name = 'mobile', $value = $procedure->patient->mobile)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Findings</label>
                                    {{ html()->textarea($name = 'findings', $value = $procedure->findings)->class('form-control')->rows(5)->placeholder('Findings') }}
                                    @error('findings')
                                    <small class="text-danger">{{ $errors->first('findings') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label req">Procedures <small>(Multiple selection enabled)</small></label>
                                    {{ html()->select($name = 'procedures[]', $value = $procs, $procedure->patientprocedures->pluck('procedure_id'))->class('form-control select2')->multiple() }}
                                    @error('procedures')
                                    <small class="text-danger">{{ $errors->first('procedures') }}</small>
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