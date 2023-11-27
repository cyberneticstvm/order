@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h3>Camp Patient Register</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                    <svg class="stroke-icon">
                        <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Camp Patients</li>
                <li class="breadcrumb-item active">Camp Patient Register</li>
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
                        <div class="row">
                            <div class="col"><h5>Camp Patient Register</h5><span>Camp Patient Management</span></div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <a href="{{ route('camp.patient.create', ['id' => encrypt($camp->id)]) }}" class="btn btn-primary" type="button">Add New</a>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Export</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a class="dropdown-item txt-dark" href="{{ route('pdf.camp.patient', ['id' => encrypt($camp->id)]) }}" target="_blank"><i class="icofont icofont-file-pdf txt-danger fw-bold"></i> Pdf</a></li>
                                        <li><a class="dropdown-item" href="{{ route('export.camp.patient', ['id' => encrypt($camp->id)]) }}" target="_blank"><i class="icofont icofont-file-excel txt-success fw-bold"></i> Excel</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center"><h5 class="txt-primary">{{ $camp->name }} - {{ $camp->camp_id }}</h5></div>
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Patient Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Mobile</th> 
                                        <th>Place</th>
                                        <th>Record</th>                                                      
                                        <th>Status</th>                           
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patients as $key => $patient)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><a href="{{ route('patient.create', ['type' => 'Camp', 'type_id' => $patient->id]) }}">{{ $patient->name }}</a></td>
                                            <td>{{ $patient->age }}</td>
                                            <td>{{ $patient->gender }}</td>
                                            <td>{{ $patient->mobile }}</td>
                                            <td>{{ $patient->place }}</td>
                                            <td class="text-center"><a href="{{ route('pdf.camp.patient.mrecord', encrypt($patient->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                            <td>{!! $patient->status() !!}</td>
                                            <td class="text-center"><a href="{{ route('camp.patient.edit', encrypt($patient->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                            <td class="text-center"><a href="{{ route('camp.patient.delete', encrypt($patient->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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