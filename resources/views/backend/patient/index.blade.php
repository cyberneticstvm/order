@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Patient Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Patients</li>
                        <li class="breadcrumb-item active">Patient Register</li>
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
                            <div class="col">
                                <h5>Patient Register</h5><span>Patient Management</span>
                            </div>
                            <div class="col text-end"><a href="{{ route('patient.create', ['type' => 'Direct', 'type_id' => '0']) }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Patient Name</th>
                                        <th>Patient ID</th>
                                        <th>Mobile</th>
                                        <th>Place</th>
                                        <th>Review</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patients as $key => $patient)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $patient->name }}</td>
                                        <td>{{ $patient->patient_id }}</td>
                                        <td>{{ $patient->mobile }}</td>
                                        <td>{{ $patient->place }}</td>
                                        <td class="text-center"><a href="/backend/consultation/create/{{ encrypt($patient->id) }}">Review</a></td>
                                        <td>{!! $patient->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('patient.edit', encrypt($patient->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('patient.delete', encrypt($patient->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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