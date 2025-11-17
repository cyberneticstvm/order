@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Purchases</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Purchases</li>
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
                            <form class="row g-3" method="post" action="{{ route('report.purchase.fetch') }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">From Date</label>
                                    {{ html()->date($name = 'from_date', $value = ($inputs[0]) ?? date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('from_date')
                                    <small class="text-danger">{{ $errors->first('from_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">To Date</label>
                                    {{ html()->date($name = 'to_date', $value = ($inputs[1]) ?? date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('to_date')
                                    <small class="text-danger">{{ $errors->first('to_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Product</label>
                                    {{ html()->select($name = 'product', $products, ($inputs[2]) ?? old('product'))->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Supplier</label>
                                    {{ html()->select($name = 'supplier', $suppliers, ($inputs[3]) ?? old('supplier'))->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Fetch</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Date</th>
                                        <th>Invoice No</th>
                                        <th>Purchase ID</th>
                                        <th>Order Date</th>
                                        <th>Del. Date</th>
                                        <th>Supplier</th>
                                        <th>Branch</th>
                                        <th>Bill</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($tot = 0);
                                    @forelse($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->created_at->format('d.M.Y') }}</td>
                                        <td>{{ $item->purchase_invoice_number }}</td>
                                        <td>{{ $item->purchase_number }}</td>
                                        <td>{{ $item->order_date->format('d.M.Y') }}</td>
                                        <td>{{ $item->delivery_date->format('d.M.Y') }}</td>
                                        <td>{{ $item->supplier->name }}</td>
                                        <td>{{ $item->branch->name }}</td>
                                        <td class="text-center"><a href="{{ route('report.export.purchase.pdf', encrypt($item->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-danger"></i></a></td>
                                        <td class="text-end">{{ number_format($item->detail->sum('total'), 2) }}</td>
                                    </tr>
                                    @php($tot += $item->detail->sum('total'))
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold">{{ number_format($tot, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection