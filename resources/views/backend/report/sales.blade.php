@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Sales</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Sales</li>
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
                            <form class="row g-3" method="post" action="{{ route('report.sales.fetch') }}">
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
                                <div class="col-md-2">
                                    <label class="form-label req">Order Status</label>
                                    {{ html()->select('order_status', array('all' => 'All Status') + orderStatuses(), ($inputs[2]) ?? old('order_status'))->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('order_status')
                                    <small class="text-danger">{{ $errors->first('order_status') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch</label>
                                    {{ html()->select($name = 'branch', $value = $branches, ($inputs[3]) ?? old('branch'))->class('form-control select2')->placeholder('Select') }}
                                    @error('branch')
                                    <small class="text-danger">{{ $errors->first('branch') }}</small>
                                    @enderror
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
                                        <th>Order No.</th>
                                        <th>Invoice Number</th>
                                        <th>Branch</th>
                                        <th>Customer Name</th>
                                        <th>MRN</th>
                                        <th>Order Date</th>
                                        <th>Order Status</th>
                                        <th>Order Total</th>
                                        <th>Discount</th>
                                        <th>Credit Used</th>
                                        <th>Advance</th>
                                        <th>Balance</th>
                                        <th>Bill Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->ono() }}</td>
                                        <td>{{ $item->invoice_number }}</td>
                                        <td>{{ $item->branch?->name }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->consultation?->mrn }}</td>
                                        <td>{{ $item->order_date->format('d/M/Y') }}</td>
                                        <td>{{ ucfirst($item->order_status) }}</td>
                                        <td class="text-end">{{ number_format($item->order_total, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->discount, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->credit_used, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->payments->sum('amount'), 2) }}</td>
                                        <td class="text-end">{{ number_format($item->invoice_total - $item->payments->sum('amount'), 2) }}</td>
                                        <td class="text-end">{{ number_format($item->invoice_total, 2) }}</td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="13" class="text-end">Total</th>
                                        <th class="text-end">{{ ($sales) ? number_format($sales->sum('invoice_total'), 2) : '0.00' }}</th>
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