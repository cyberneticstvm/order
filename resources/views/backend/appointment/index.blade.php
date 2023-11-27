@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h3>Appointment Register</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                    <svg class="stroke-icon">
                        <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Appointments</li>
                <li class="breadcrumb-item active">Appointment Register</li>
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
                            <div class="col"><h5>Appointment Register</h5><span>Appointment Management</span></div>
                            <div class="col text-end"><a href="{{ route('appointment.create') }}" class="btn btn-primary" type="button">Add New</a></div>
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
                                            <td>{{ $appointment->name }}</td>
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