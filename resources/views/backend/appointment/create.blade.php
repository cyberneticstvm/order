@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Appointment</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                            <svg class="stroke-icon">
                                <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item">Appointment</li>
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
                    <h5>Create Appointment</h5><span>Create New Appointment</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('appointment.save') }}" id="frmAppointment">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Patient Name</label>
                                    {{ html()->text($name = 'name', $value = old('name'))->class('form-control')->placeholder('Patient Name') }}
                                    @error('name')
                                        <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Age</label>
                                    {{ html()->number($name = 'age', $value = old('age'), $min="1", $max="100", $step="any")->class('form-control')->placeholder('Age') }}
                                    @error('age')
                                        <small class="text-danger">{{ $errors->first('age') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Gender</label>
                                    {{ html()->select($name = 'gender', $value = array('Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'), old('gender'))->class('form-control select2')->placeholder('Select') }}
                                    @error('gender')
                                        <small class="text-danger">{{ $errors->first('gender') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Place</label>
                                    {{ html()->text($name = 'place', $value = old('place'))->class('form-control')->placeholder('Place') }}
                                    @error('place')
                                        <small class="text-danger">{{ $errors->first('place') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Mobile Number</label>
                                    {{ html()->text($name = 'mobile', $value = old('mobile'))->class('form-control')->maxlength(10)->placeholder('Mobile Number') }}
                                    @error('mobile')
                                        <small class="text-danger">{{ $errors->first('mobile') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Doctor</label>
                                    {{ html()->select($name = 'doctor_id', $value = $doctors, old('doctor_id'))->class('form-control select2 appTime')->placeholder('Select') }}
                                    @error('doctor_id')
                                        <small class="text-danger">{{ $errors->first('doctor_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch</label>
                                    {{ html()->select($name = 'branch_id', $value = $branches, old('branch_id'))->class('form-control select2 appTime')->placeholder('Select') }}
                                    @error('branch_id')
                                        <small class="text-danger">{{ $errors->first('branch_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date</label>
                                    {{ html()->date($name = 'date', $value = old('date'))->class('form-control appTime')->placeholder(date('Y-m-d')) }}
                                    @error('date')
                                        <small class="text-danger">{{ $errors->first('date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Time</label>
                                    {{ html()->select($name = 'time', $value = NULL, NULL)->class('form-control select2 selAppTime')->placeholder('Select') }}
                                    @error('time')
                                        <small class="text-danger">{{ $errors->first('time') }}</small>
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