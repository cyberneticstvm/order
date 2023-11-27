@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Transfer Register (Frames)</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Frames</li>
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
                                <h5>Transfer Register</h5><span>Frames Transfer Management</span>
                            </div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <a href="{{ route('frame.transfer.create') }}" class="btn btn-primary" type="button">Add New</a>
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
                                        <th>Transfer ID</th>
                                        <th>Transfer Date</th>
                                        <th>From Branch</th>
                                        <th>To Branch</th>
                                        <th>Transfer Note</th>
                                        <th>Export</th>
                                        <th>Status</th>
                                        <th>Transfer</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transfers as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->transfer_number }}</td>
                                        <td>{{ $item->transfer_date?->format('d/M/Y') }}</td>
                                        <td>{{ $item->frombranch?->name ?? 'Main Stock' }}</td>
                                        <td>{{ $item->tobranch?->name }}</td>
                                        <td>{{ $item->transfer_note }}</td>
                                        <td class="text-center"><a href="{{ route('pdf.product.transfer', encrypt($item->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td>{!! $item->status() !!}</td>
                                        <td>{!! $item->isTransferAccepted() !!}</td>
                                        <td class="text-center"><a href="{{ route('frame.transfer.edit', encrypt($item->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('frame.transfer.delete', encrypt($item->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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