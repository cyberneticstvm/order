@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Frame Product Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item active">Frame Product Register</li>
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
                                <h5>Frame Product Register</h5><span>Frame Product Management</span>
                            </div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <a href="{{ route('product.frame.create') }}" class="btn btn-primary" type="button">Add New</a>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Export</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a class="dropdown-item txt-dark" href="{{ route('pdf.product.frame') }}" target="_blank"><i class="icofont icofont-file-pdf txt-danger fw-bold"></i> Pdf</a></li>
                                        <li><a class="dropdown-item" href="{{ route('export.product.frame') }}" target="_blank"><i class="icofont icofont-file-excel txt-success fw-bold"></i> Excel</a></li>
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
                                        <th>Product Name</th>
                                        <th>Product Code</th>
                                        <th>Manufacturer</th>
                                        <th>Type</th>
                                        <th>Shape</th>
                                        <th>Reorder Level</th>
                                        <th>Selling Price</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->manufacturer?->name }}</td>
                                        <td>{{ $item->type?->name }}</td>
                                        <td>{{ $item->shape?->name }}</td>
                                        <td>{{ $item->reorder_level }}</td>
                                        <td class="text-end">{{ number_format($item->selling_price, 2) }}</td>
                                        <td>{!! $item->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('product.frame.edit', encrypt($item->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('product.frame.delete', encrypt($item->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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