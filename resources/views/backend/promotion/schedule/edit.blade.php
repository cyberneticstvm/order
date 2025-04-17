@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Update Promotion Schedule</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Promotion Schedule</li>
                        <li class="breadcrumb-item active">Update</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Update Promotion Schedule</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('promotion.schedule.update', $schedule->id) }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Schedule Name</label>
                                    {{ html()->text($name = 'name', $schedule->name)->class('form-control')->placeholder('Schedule Name') }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Schedule Date</label>
                                    {{ html()->date($name = 'scheduled_date', $schedule->scheduled_date->format('Y-m-d'))->class('form-control') }}
                                    @error('scheduled_date')
                                    <small class="text-danger">{{ $errors->first('scheduled_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Template Id</label>
                                    {{ html()->text($name = 'template_id', $schedule->template_id)->class('form-control')->placeholder('Template Id') }}
                                    @error('template_id')
                                    <small class="text-danger">{{ $errors->first('template_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Template Language</label>
                                    {{ html()->select($name = 'template_language', array('en' => 'English', 'en_GB' => 'English UK', 'en_US' => 'English US', 'en_IN' => 'English India', 'ml' => 'Malayalam'), $schedule->template_language)->class('form-control select2')->placeholder('Template Language') }}
                                    @error('template_language')
                                    <small class="text-danger">{{ $errors->first('template_language') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">SMS Limit per Hour</label>
                                    {{ html()->text($name = 'sms_limit_per_hour', $schedule->sms_limit_per_hour)->class('form-control')->placeholder('0') }}
                                    @error('sms_limit_per_hour')
                                    <small class="text-danger">{{ $errors->first('sms_limit_per_hour') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Entity</label>
                                    {{ html()->select('entity', array('hospital' => 'Hospital', 'store' => 'Store', 'lab' => 'Lab'), $schedule->entity)->class('form-control select2')->placeholder('Select') }}
                                    @error('entity')
                                    <small class="text-danger">{{ $errors->first('entity') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Status</label>
                                    {{ html()->select('status', array('publish' => 'Publish', 'draft' => 'Draft'), $schedule->status)->class('form-control select2')->placeholder('Select') }}
                                    @error('status')
                                    <small class="text-danger">{{ $errors->first('status') }}</small>
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
</div>
@endsection