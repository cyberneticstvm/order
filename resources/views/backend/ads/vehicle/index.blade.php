@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Vehicle Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Advertisement</li>
                        <li class="breadcrumb-item active">Vehicle Register</li>
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
                                <h5>Vehicle Register</h5><span>Vehicle Management</span>
                            </div>
                            <div class="col text-end"><a href="{{ route('vehicle.create') }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Owner Name</th>
                                        <th>Contact Number</th>
                                        <th>Reg. number</th>
                                        <th>Reg. Date</th>
                                        <th>Reg. Branch</th>
                                        <th>Fee</th>
                                        <th>Pay. Terms</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vehicles as $key => $vehicle)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $vehicle->owner_name }}</td>
                                        <td>{{ $vehicle->contact_number }}</td>
                                        <td><a href="{{ route('vehicle.payment', encrypt($vehicle->id)) }}">{{ $vehicle->reg_number }}</a></td>
                                        <td>{{ $vehicle->created_at->format('d.M.Y') }}</td>
                                        <td>{{ $vehicle->branch?->name }}</td>
                                        <td>{{ $vehicle->fee }}</td>
                                        <td>{{ $vehicle->payment_terms }} Days</td>
                                        <td class="text-center">{!! $vehicle->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('vehicle.edit', encrypt($vehicle->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('vehicle.delete', encrypt($vehicle->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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