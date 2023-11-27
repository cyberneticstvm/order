@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h3>Today's Appointment List</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                    <svg class="stroke-icon">
                        <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Appointments</li>
                <li class="breadcrumb-item active">Appointment List</li>
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
                            <div class="col"><h5>Appointment List</h5><span>Appointment Management</span></div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <a href="{{ route('appointment.create') }}" class="btn btn-primary" type="button">Add New</a>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Export</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a class="dropdown-item txt-dark" href="{{ route('pdf.appointment') }}" target="_blank"><i class="icofont icofont-file-pdf txt-danger fw-bold"></i> Pdf</a></li>
                                        <li><a class="dropdown-item" href="{{ route('export.today.appointments') }}" target="_blank"><i class="icofont icofont-file-excel txt-success fw-bold"></i> Excel</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Patient Name</th>
                                        <th>Place</th>
                                        <th>Mobile</th>
                                        <th>Branch</th>
                                        <th>Doctor</th>
                                        <th>Date</th>
                                        <th>Time</th>                                                         
                                        <th>Status</th>                           
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($appointments as $key => $appointment)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><a href="{{ route('patient.create', ['type' => 'Appointment', 'type_id' => $appointment->id]) }}">{{ $appointment->name }}</a></td>
                                            <td>{{ $appointment->place }}</td>
                                            <td>{{ $appointment->mobile }}</td>
                                            <td>{{ $appointment->branch->name }}</td>
                                            <td>{{ $appointment->doctor->name }}</td>
                                            <td>{{ $appointment->date->format('d, M Y') }}</td>
                                            <td>{{ $appointment->time->format('h:i A') }}</td>
                                            <td>{!! $appointment->status() !!}</td>
                                            <td class="text-center"><a href="{{ route('appointment.edit', encrypt($appointment->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                            <td class="text-center"><a href="{{ route('appointment.delete', encrypt($appointment->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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