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
                        <h5>Create Patient Procedure</h5><span>Create Patient Procedure</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('patient.procedure.save') }}">
                                @csrf
                                <input type="hidden" name="patient_id" value="{{ $consultation->patient_id }}" />
                                <input type="hidden" name="branch_id" value="{{ $consultation->branch_id }}" />
                                <input type="hidden" name="consultation_id" value="{{ $consultation->id }}" />
                                <div class="col-4">
                                    <label class="form-label">Patient Name</label>
                                    {{ html()->text($name = 'name', $value = $consultation->patient->name)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Patient ID</label>
                                    {{ html()->text($name = 'pid', $value = $consultation->patient->patient_id)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Patient Mobile</label>
                                    {{ html()->text($name = 'mobile', $value = $consultation->patient->mobile)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Findings</label>
                                    {{ html()->textarea($name = 'findings', $value = old('findings'))->class('form-control')->rows(5)->placeholder('Findings') }}
                                    @error('findings')
                                    <small class="text-danger">{{ $errors->first('findings') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label req">Procedures <small>(Multiple selection enabled)</small></label>
                                    {{ html()->select($name = 'procedures[]', $value = $procs, NULL)->class('form-control select2')->multiple() }}
                                    @error('procedures')
                                    <small class="text-danger">{{ $errors->first('procedures') }}</small>
                                    @enderror
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