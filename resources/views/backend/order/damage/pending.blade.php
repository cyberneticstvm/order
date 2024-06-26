@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Pending Transfer Register for Damaged Products</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Pending</li>
                        <li class="breadcrumb-item active">Transfer Register</li>
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
                                <h5>Pending Transfer Register</h5><span>Pending Transfer Management</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Transfer ID</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Transfer Date</th>
                                        <th>From Branch</th>
                                        <th>To Branch</th>
                                        <th>Transfer Note</th>
                                        <th>Export</th>
                                        <th>Status</th>
                                        <th>Transfer</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ $item->created_at?->format('d/M/Y') }}</td>
                                        <td>{{ $item->frombranch?->name }}</td>
                                        <td>{{ $item->tobranch?->name ?? 'Main Stock' }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td class="text-center"></td>
                                        <td>{!! $item->status() !!}</td>
                                        <td>{!! $item->isTransferAccepted() !!}</td>
                                        <td class="text-center"><a href="{{ route('pending.damage.transfer.edit', encrypt($item->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
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