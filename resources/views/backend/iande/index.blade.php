@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Income & Expense Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Income & Expenses</li>
                        <li class="breadcrumb-item active">Income & Expense Register</li>
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
                                <h5>Income & Expense Register</h5><span>Income & Expense Management</span>
                            </div>
                            <div class="col text-end"><a href="{{ route('iande.create') }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Date</th>
                                        <th>Head</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($iandes as $key => $ie)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $ie->date->format('d/M/Y') }}</td>
                                        <td>{{ $ie->head->name }}</td>
                                        <td>{{ ucfirst($ie->category) }}</td>
                                        <td>{{ $ie->description }}</td>
                                        <td>{{ $ie->amount }}</td>
                                        <td>{!! $ie->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('iande.edit', encrypt($ie->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('iande.delete', encrypt($ie->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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