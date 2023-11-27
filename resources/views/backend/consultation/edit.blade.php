@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Consultation</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                            <svg class="stroke-icon">
                                <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item">Consultation</li>
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
                    <h5>Update Consultation</h5><span>Update Consultation</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('consultation.edit', $consultation->id) }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label">Patient Name</label>
                                    {{ html()->text($name = 'pname', $value = $consultation->patient->name)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Patient ID</label>
                                    {{ html()->text($name = 'pid', $value = $consultation->patient->patient_id)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Patient Mobile</label>
                                    {{ html()->text($name = 'pmobile', $value = $consultation->patient->mobile)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Purpose of Visit</label>
                                    {{ html()->select($name = 'consultation_type', $value = $ctypes, $consultation->consultation_type)->class('form-control select2')->placeholder('Select') }}
                                    @error('consultation_type')
                                        <small class="text-danger">{{ $errors->first('consultation_type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Department</label>
                                    {{ html()->select($name = 'department_id', $value = $depts, $consultation->department_id)->class('form-control select2')->placeholder('Select') }}
                                    @error('department_id')
                                        <small class="text-danger">{{ $errors->first('department_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Doctor</label>
                                    {{ html()->select($name = 'doctor_id', $value = $doctors, $consultation->doctor_id)->class('form-control select2')->placeholder('Select') }}
                                    @error('doctor_id')
                                        <small class="text-danger">{{ $errors->first('doctor_id') }}</small>
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