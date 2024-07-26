@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Invoice Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Orders</li>
                        <li class="breadcrumb-item active">Invoice Register</li>
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
                                <h5>Invoice Register</h5><span>Invoice Register</span>
                            </div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Export</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a class="dropdown-item txt-dark" href="#" target="_blank"><i class="icofont icofont-file-pdf txt-danger fw-bold"></i> Pdf</a></li>
                                        <li><a class="dropdown-item" href="#" target="_blank"><i class="icofont icofont-file-excel txt-success fw-bold"></i> Excel</a></li>
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
                                        <th>Customer</th>
                                        <th>Mobile</th>
                                        <th>Order Number</th>
                                        <th>Invoice Number</th>
                                        <th>Invoice Total</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Order Status</th>
                                        <th>Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->mobile }}</td>
                                        <td>{{ $item->ono() }}</td>
                                        <td>{{ $item->ino() }}</td>
                                        <td>{{ $item->invoice_total }}</td>
                                        <td>{{ number_format($item->payments->sum('amount') + $item->credit_used, 2) }}</td>
                                        <td>{{ number_format($item->invoice_total-($item->payments->sum('amount') + $item->credit_used + $item->discount), 2) }}</td>
                                        <td>{{ ucfirst($item->order_status) }}</td>
                                        <td class="text-center"><a href="{{ route('store.order.invoice', encrypt($item->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
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