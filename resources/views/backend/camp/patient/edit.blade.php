@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Camp Patient</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Camp Patient</li>
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
                        <h5>Update Camp Patient</h5><span>Update Camp Patient</span>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <h5 class="txt-primary">{{ $campatient->camp->name }} - {{ $campatient->camp->camp_id }}</h5>
                        </div>
                        <div class="card-wrapper mt-3">
                            <form class="row g-3" method="post" action="{{ route('camp.patient.update', $campatient->id) }}">
                                @csrf
                                <input type="hidden" name="camp_patient_vision_id" value="{{ $campatient->vision->id }}" />
                                <div class="col-md-3">
                                    <label class="form-label req">Patient Name</label>
                                    {{ html()->text($name = 'name', $value = $campatient->name ?? old('name'))->class('form-control')->placeholder('Patient Name') }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Age</label>
                                    {{ html()->number($name = 'age', $value = $campatient->age ?? old('age'), $min="1", $max="100", $step="any")->class('form-control')->placeholder('Age') }}
                                    @error('age')
                                    <small class="text-danger">{{ $errors->first('age') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Gender</label>
                                    {{ html()->select($name = 'gender', $value = array('Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'), $campatient->gender ?? old('gender'))->class('form-control select2')->placeholder('Select') }}
                                    @error('gender')
                                    <small class="text-danger">{{ $errors->first('gender') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Place</label>
                                    {{ html()->text($name = 'place', $value = $campatient->place ?? old('place'))->class('form-control')->placeholder('Place') }}
                                    @error('place')
                                    <small class="text-danger">{{ $errors->first('place') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Mobile Number</label>
                                    {{ html()->text($name = 'mobile', $value = $campatient->mobile ?? old('mobile'))->class('form-control')->maxlength(10)->placeholder('Mobile Number') }}
                                    @error('mobile')
                                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Review Date</label>
                                    {{ html()->date($name = 'review_date', $value = $campatient->review_date ?? old('review_date'))->class('form-control')->placeholder(date('Y-m-d')) }}
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <h5 class="text-center">VISION</h5>
                                    <table class="table table-bordered table-sm mt-3">
                                        <thead class="text-center">
                                            <tr>
                                                <th>EYE</th>
                                                <th>VB</th>
                                                <th>SPH</th>
                                                <th>CYL</th>
                                                <th>AXIS</th>
                                                <th>ADD</th>
                                                <th>VA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>RE</td>
                                                <td><input type="text" name="re_vb" class="border-0 text-center" maxlength="6" placeholder="VB" value="{{ $campatient->vision->re_vb ?? old('re_vb') }}"></td>
                                                <td><input type="text" name="re_sph" class="border-0 text-center" maxlength="6" placeholder="SPH" value="{{ $campatient->vision->re_sph ?? old('re_sph') }}"></td>
                                                <td><input type="text" name="re_cyl" class="border-0 text-center" maxlength="6" placeholder="CYL" value="{{ $campatient->vision->re_cyl ?? old('re_cyl') }}"></td>
                                                <td><input type="text" name="re_axis" class="border-0 text-center" maxlength="6" placeholder="AXIS" value="{{ $campatient->vision->re_axis ?? old('re_axis') }}"></td>
                                                <td><input type="text" name="re_add" class="border-0 text-center" maxlength="6" placeholder="ADD" value="{{ $campatient->vision->re_add ?? old('re_add') }}"></td>
                                                <td><input type="text" name="re_va" class="border-0 text-center" maxlength="6" placeholder="VA" value="{{ $campatient->vision->re_va ?? old('re_va') }}"></td>
                                            </tr>
                                            <tr>
                                                <td>LE</td>
                                                <td><input type="text" name="le_vb" class="border-0 text-center" maxlength="6" placeholder="VB" value="{{ $campatient->vision->le_vb ?? old('le_vb') }}"></td>
                                                <td><input type="text" name="le_sph" class="border-0 text-center" maxlength="6" placeholder="SPH" value="{{ $campatient->vision->le_sph ?? old('le_sph') }}"></td>
                                                <td><input type="text" name="le_cyl" class="border-0 text-center" maxlength="6" placeholder="CYL" value="{{ $campatient->vision->le_cyl ?? old('le_cyl') }}"></td>
                                                <td><input type="text" name="le_axis" class="border-0 text-center" maxlength="6" placeholder="AXIS" value="{{ $campatient->vision->le_axis ?? old('le_axis') }}"></td>
                                                <td><input type="text" name="le_add" class="border-0 text-center" maxlength="6" placeholder="ADD" value="{{ $campatient->vision->le_add ?? old('le_add') }}"></td>
                                                <td><input type="text" name="le_va" class="border-0 text-center" maxlength="6" placeholder="VA" value="{{ $campatient->vision->le_va ?? old('le_va') }}"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Notes / Remarks</label>
                                    {{ html()->textarea($name = 'notes', $campatient->notes ??  old('notes'))->class('form-control')->rows('3')->placeholder('Notes / Remarks') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Further Investigation Advised?</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="further_investigation_advised" type="checkbox" role="switch" name="further_investigation_advised" value="1" {{ $campatient->further_investigation_advised == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Glasses Advised?</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="galsses_advised" type="checkbox" role="switch" name="galsses_advised" value="1" {{ $campatient->galsses_advised == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Yearly Eye Test Advised?</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="yearly_eye_test_advised" type="checkbox" role="switch" name="yearly_eye_test_advised" value="1" {{ $campatient->yearly_eye_test_advised == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Surgery Advised?</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="surgery_advised" type="checkbox" role="switch" name="surgery_advised" value="1" {{ $campatient->surgery_advised == 1 ? 'checked' : '' }}>
                                    </div>
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