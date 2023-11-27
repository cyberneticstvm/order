@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Patient Procedure Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Patient Procedures</li>
                        <li class="breadcrumb-item active">Patient Procedure Register</li>
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
                            <form class="row g-3" method="post" action="{{ route('patient.procedure.fetch') }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Medical Record Number</label>&nbsp;&nbsp;<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<strong>Number Parts Only.</strong>"><i class="fa fa-info txt-info"></i></a>
                                    <div class="input-group">
                                        {{ html()->number($name = 'medical_record_number', $value = old('medical_record_number'))->class('form-control')->placeholder('Medical Record Number') }}
                                        <button class="btn btn-outline-warning btn-submit" id="button-addon2" type="submit">Fetch</button>
                                    </div>
                                    @error('medical_record_number')
                                    <small class="text-danger">{{ $errors->first('medical_record_number') }}</small>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
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
                        <h5>Patient Procedure Register</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Patient Name</th>
                                        <th>Patient ID</th>
                                        <th>Medical Record No.</th>
                                        <th>Procedures</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($procedures as $key => $procedure)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $procedure->patient->name }}</td>
                                        <td>{{ $procedure->patient->patient_id }}</td>
                                        <td>{{ $procedure->consultation->mrn }}</td>
                                        <td>{{ procedures()->whereIn('id', $procedure->patientprocedures->pluck('procedure_id'))->pluck('name')->implode(',') }}</td>
                                        <td>{!! $procedure->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('patient.procedure.edit', encrypt($procedure->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('patient.procedure.delete', encrypt($procedure->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
@endsection