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
                                        <th>MRN</th>
                                        <th>Prescription</th>
                                        <th>Created at</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($registrations as $key => $reg)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $reg->customer->id }}</td>
                                        <td>{{ $reg->customer->name }}</td>
                                        <td>{{ $reg->customer->mobile }}</td>
                                        <td>{{ ($reg->customer->mrn == '0') ? 'Direct' : $reg->customer->mrn }}</td>
                                        @if($reg->spectacle):
                                        <td></td>
                                        @else
                                        <td>
                                            <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Create</button>
                                            <ul class="dropdown-menu dropdown-block">
                                                <li><a class="dropdown-item txt-dark" href="{{ route('customer.spectacle.edit', encrypt($reg->id)) }}">Prescription</a></li>
                                            </ul>
                                        </td>
                                        @endif
                                        <td>{{ $reg->created_at->format('d, M Y h:i a') }}</td>
                                        <td>{!! $reg->status() !!}</td>
                                        <td>
                                            <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Create</button>
                                            <ul class="dropdown-menu dropdown-block">
                                                <li><a class="dropdown-item txt-dark" href="{{ route('store.order.create', ['id' => encrypt($reg->id), 'type' => '1']) }}">Spectacles</a></li>
                                                <li><a class="dropdown-item txt-dark" href="{{ route('solution.order.create', ['id' => encrypt($reg->id), 'type' => '2']) }}">Solutions</a></li>
                                            </ul>
                                        </td>
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