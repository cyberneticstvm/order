@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h3>Branch Register</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                    <svg class="stroke-icon">
                        <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Branches</li>
                <li class="breadcrumb-item active">Branch Register</li>
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
                            <div class="col"><h5>Branch Register</h5><span>Branch Management</span></div>
                            <div class="col text-end"><a href="{{ route('branch.create') }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Branch Name</th>                           
                                        <th>Branch Code</th>                           
                                        <th>Email</th>                           
                                        <th>Contact</th>                           
                                        <th>GSTIN</th>                           
                                        <th>Reg. Fee</th>                           
                                        <th>Status</th>                           
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($branches as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->phone }}</td>
                                            <td>{{ $item->gstin }}</td>
                                            <td>{{ $item->registration_fee }}</td>
                                            <td class="text-center">{!! $item->status() !!}</td>
                                            <td class="text-center"><a href="{{ route('branch.edit', encrypt($item->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                            <td class="text-center"><a href="{{ route('branch.delete', encrypt($item->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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