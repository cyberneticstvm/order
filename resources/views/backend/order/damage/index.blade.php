@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Damage Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item active">Damage Register</li>
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
                                <h5>Product Damage Register</h5><span>Product Management</span>
                            </div>
                            <div class="col text-end">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add New</button>
                                <ul class="dropdown-menu dropdown-block">
                                    <li><a class="dropdown-item txt-dark" href="{{ route('product.damage.create', 'lens') }}">Lens</a></li>
                                    <li><a class="dropdown-item" href="{{ route('product.damage.create', 'frame') }}">Frame</a></li>
                                    <li><a class="dropdown-item txt-dark" href="{{ route('product.damage.create', 'accessory') }}">Accessory</a></li>
                                    <li><a class="dropdown-item" href="{{ route('product.damage.create', 'solution') }}">Solution</a></li>
                                </ul>
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
                                        <th>Qty</th>
                                        <th>From Branch</th>
                                        <th>To Branch</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $key => $product)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $product->product->name }}</td>
                                        <td>{{ $product->qty }}</td>
                                        <td>{{ $product->frombranch->name }}</td>
                                        <td>Main Branch</td>
                                        <td>{!! $product->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('product.damage.edit', encrypt($product->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('product.damage.delete', encrypt($product->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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