@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h3>Doctor Register</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                    <svg class="stroke-icon">
                        <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Doctors</li>
                <li class="breadcrumb-item active">Doctor Register</li>
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
                            <div class="col"><h5>Doctor Register</h5><span>Doctor Management</span></div>
                            <div class="col text-end"><a href="{{ route('doctor.create') }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Doctor Name</th>
                                        <th>Code</th>
                                        <th>Email</th>
                                        <th>Mobile</th> 
                                        <th>Doctor Fee</th>                           
                                        <th>Status</th>                           
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($doctors as $key => $doc)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $doc->name }}</td>
                                            <td>{{ $doc->code }}</td>
                                            <td>{{ $doc->email }}</td>
                                            <td>{{ $doc->mobile }}</td>
                                            <td>{{ $doc->fee }}</td>
                                            <td>{!! $doc->status() !!}</td>
                                            <td class="text-center"><a href="{{ route('doctor.edit', encrypt($doc->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                            <td class="text-center"><a href="{{ route('doctor.delete', encrypt($doc->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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