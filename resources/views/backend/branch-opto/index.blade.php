@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Head Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Branch - Optometrist</li>
                        <li class="breadcrumb-item active">Branch - Optometrist Register</li>
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
                                <h5>Branch - Optometrist Register</h5><span>Branch - Optometrist Management</span>
                            </div>
                            <div class="col text-end"><a href="{{ route('bo.create') }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Branch Name</th>
                                        <th>User</th>
                                        <th>Designation</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>On / Off</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bos as $key => $bo)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $bo->branch->name }}</td>
                                        <td>{{ $bo->user->name }}</td>
                                        <td>{{ ucfirst($bo->designation) }}</td>
                                        <td>{!! $bo->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('bo.edit', encrypt($bo->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('bo.delete', encrypt($bo->id)) }}" class="proceed">{!! $bo->icon() !!}</a></td>
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