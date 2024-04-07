@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Voucher Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Vouchers</li>
                        <li class="breadcrumb-item active">Voucher Register</li>
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
                                <h5>Voucher Register</h5><span>Voucher Management</span>
                            </div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add New</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a class="dropdown-item txt-dark" href="{{ route('voucher.create', 'receipt') }}">Receipt</a></li>
                                        <li><a class="dropdown-item" href="{{ route('voucher.create', 'payment') }}">Payment</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Voucher ID</th>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Payment Mode</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vouchers as $key => $voucher)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $voucher->id }}</td>
                                        <td>{{ $voucher->customer_id }}</td>
                                        <td>{{ $voucher->customer->name }}</td>
                                        <td>{{ $voucher->category }}</td>
                                        <td>{{ $voucher->description }}</td>
                                        <td>{{ $voucher->amount }}</td>
                                        <td>{{ $voucher->paymentmode->name }}</td>
                                        <td>{!! $voucher->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('voucher.edit', encrypt($voucher->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('voucher.delete', encrypt($voucher->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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