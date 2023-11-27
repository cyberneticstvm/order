@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Order Register (Pharmacy)</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Orders</li>
                        <li class="breadcrumb-item active">Order Register</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('pharmacy.order.fetch') }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Medical Record Number</label>&nbsp;&nbsp;<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<strong>Number Parts Only.</strong>"><i class="fa fa-info txt-info"></i></a>
                                    <div class="input-group">
                                        {{ html()->number($name = 'medical_record_number', $value = old('medical_record_number'))->class('form-control')->placeholder('Medical Record Number') }}
                                        <button class="btn btn-outline-warning btn-submit" id="button-addon2" type="submit">Fetch</button>
                                    </div>
                                    @error('medical_record_number')
                                    <small class="text-danger">{{ $errors->first('medical_record_number') }}</small>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
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
                                <h5>Order Register</h5><span>Order Management</span>
                            </div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <a href="{{ route('pharmacy.order.create', ['id' => encrypt(0)]) }}" class="btn btn-primary" type="button">Add New</a>
                                </div>
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
                                        <th>Invoice Number</th>
                                        <th>Branch</th>
                                        <th>Customer Name</th>
                                        <th>MRN</th>
                                        <th>Invoice Total</th>
                                        <th>Receipt</th>
                                        <th>Invoice</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->invoice_number }}</td>
                                        <td>{{ $item->branch?->name }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->consultation?->mrn }}</td>
                                        <td class="text-end">{{ number_format($item->invoice_total, 2) }}</td>
                                        <td class="text-center"><a href="{{ route('pdf.order.receipt', encrypt($item->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td class="text-center"><a href="#" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td>{!! $item->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('pharmacy.order.edit', encrypt($item->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('pharmacy.order.delete', encrypt($item->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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