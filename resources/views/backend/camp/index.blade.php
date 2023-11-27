@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h3>Camp Register</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">                                       
                    <svg class="stroke-icon">
                        <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Camps</li>
                <li class="breadcrumb-item active">Camp Register</li>
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
                            <div class="col"><h5>Camp Register</h5><span>Camp Management</span></div>
                            <div class="col text-end"><a href="{{ route('camp.create') }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Camp Name</th>
                                        <th>Camp ID</th>
                                        <th>Venue</th> 
                                        <th>Address</th>                           
                                        <th>Cordinator</th>                           
                                        <th>Type</th>                           
                                        <th>Status</th>                           
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($camps as $key => $camp)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $camp->from_date->format('d, M Y') }}</td>
                                            <td>{{ $camp->to_date->format('d, M Y') }}</td>
                                            <td><a href="{{ route('camp.patients', encrypt($camp->id)) }}">{{ $camp->name }}</a></td>
                                            <td>{{ $camp->camp_id }}</td>
                                            <td>{{ $camp->venue }}</td>
                                            <td>{{ $camp->address }}</td>
                                            <td>{{ $camp->getCordinator?->name }}</td>
                                            <td>{{ $camp->ctype?->name }}</td>
                                            <td>{!! $camp->status() !!}</td>
                                            <td class="text-center"><a href="{{ route('camp.edit', encrypt($camp->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                            <td class="text-center"><a href="{{ route('camp.delete', encrypt($camp->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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