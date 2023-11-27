@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Consultation Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Consultation</li>
                        <li class="breadcrumb-item active">Consultation Register</li>
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
                                <h5>Consultation Register</h5><span>Consultation Management</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>MRN</th>
                                        <th>Patient Name</th>
                                        <th>Patient ID</th>
                                        <th>Doctor</th>
                                        <th>OPT</th>
                                        <th>Prescription</th>
                                        <th>Receipt</th>
                                        <th>Order</th>
                                        <th>Pharmacy</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($consultations as $key => $con)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="{{ route('mrecord.create', encrypt($con->id)) }}">{{ $con->mrn }}</a></td>
                                        <td>{{ $con->patient->name }}</td>
                                        <td>{{ $con->patient->patient_id }}</td>
                                        <td>{{ $con->doctor->name }}</td>
                                        <td class="text-center"><a href="{{ route('pdf.opt', encrypt($con->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td class="text-center"><a href="{{ route('pdf.prescription', encrypt($con->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td class="text-center"><a href="{{ route('pdf.consultation.receipt', encrypt($con->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td class="text-center"><a href="{{ route('store.order.create', encrypt($con->id)) }}">Create</a></td>
                                        <td class="text-center"><a href="{{ route('pharmacy.order.create', encrypt($con->id)) }}">Create</a></td>
                                        <td>{!! $con->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('consultation.edit', encrypt($con->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('consultation.delete', encrypt($con->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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