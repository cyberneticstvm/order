@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Specatcle Pescription Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Register</li>
                        <li class="breadcrumb-item active">Spectacle Prescription</li>
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
                                <h5>Spectacle Prescription</h5><span>Register</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Contact Number</th>
                                        <th>Doctor</th>
                                        <th>Optometrist</th>
                                        <th>Branch</th>
                                        <th>Time</th>
                                        <th>Print</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($spectacles as $key => $spec)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $spec->customer->id }}</td>
                                        <td>{{ $spec->customer->name }}</td>
                                        <td>{{ $spec->customer->mobile }}</td>
                                        <td>{{ $spec->doctors?->name }}</td>
                                        <td>{{ $spec->optometrists?->name }}</td>
                                        <td>{{ $spec->branch->name }}</td>
                                        <td>{{ $spec->created_at->format('d.M.Y h:i a') }}</td>
                                        <td class="text-center"><a href="{{ route('customer.order.prescription', encrypt($spec->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td>{!! $spec->status() !!}</td>
                                        @if($spec->hasOrder($spec->registration_id))
                                        <td></td>
                                        @else
                                        <td class="text-center">
                                            <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Create</button>
                                            <ul class="dropdown-menu dropdown-block">
                                                <li><a class="dropdown-item txt-dark" href="{{ route('store.order.create', ['id' => encrypt($spec->registration_id), 'type' => '1']) }}">Spectacles</a></li>
                                                <li><a class="dropdown-item txt-dark" href="{{ route('solution.order.create', ['id' => encrypt($spec->registration_id), 'type' => '2']) }}">Solutions</a></li>
                                            </ul>
                                        </td>
                                        @endif
                                        <td class="text-center"><a href="{{ route('spectacle.edit', encrypt($spec->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('spectacle.delete', encrypt($spec->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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