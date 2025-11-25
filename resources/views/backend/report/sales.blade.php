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
                                <input type="hidden" name="redir" value="sales" />
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
                                    {{ html()->select('order_status', array('all' => 'All') + orderStatuses(), ($inputs[2]) ?? old('order_status'))->class('form-control select2')->placeholder('Select')->required() }}
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
                    <div class="card-header">
                        <div class="row">
                            <div class="col text-end">
                                <div class="btn-group">
                                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Export</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a class="dropdown-item" href="{{ route('report.export.sales.excel', ['from_date' => $inputs[0], 'to_date' => $inputs[1], 'status' => $inputs[2], 'branch' => $inputs[3]]) }}" target="_blank"><i class="icofont icofont-file-excel txt-success fw-bold"></i> Excel</a></li>
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
                                        <th>Order No.</th>
                                        <th>Frame</th>
                                        <th>Branch</th>
                                        <th>Customer Name</th>
                                        <th>MRN</th>
                                        <th>Order Date</th>
                                        <th>Order Status</th>
                                        <th>Return</th>
                                        <th>Order Total</th>
                                        <th>Discount</th>
                                        <th>Credit Used</th>
                                        <th>Advance</th>
                                        <th>Balance</th>
                                        <th>Bill Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($tot=0)
                                    @forelse($sales as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->ono() }}</td>
                                        <td class="text-center"><a class="fw-bold" title="{{ ($item->details->where('eye', 'frame')->sum('qty')) ? $item->details?->where('eye', 'frame')->first()?->product?->name : '' }}">{{ $item->details->where('eye', 'frame')->sum('qty') }}</a></td>
                                        <td>{{ $item->branch?->name }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->consultation_id }}</td>
                                        <td>{{ $item->created_at->format('d/M/Y h:i a') }}</td>
                                        <td>{{ ucfirst($item->order_status) }}</td>
                                        <td>{{ $item->sreturn()->details()->sum('returned_amount') }}</td>
                                        <td class="text-end">{{ number_format($item->order_total, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->discount, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->credit_used, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->payments->sum('amount'), 2) }}</td>
                                        <td class="text-end">{{ number_format($item->invoice_total - ($item->payments->sum('amount') + $item->credit_used), 2) }}</td>
                                        <td class="text-end">{{ number_format($item->invoice_total, 2) }}</td>
                                    </tr>
                                    @php($tot += $item->details->where('eye', 'frame')->sum('qty'))
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Total</th>
                                        <th class="text-center"><a href="javascript:void(0)" class="frameCount" data-from-date="{{ $inputs[0] }}" data-to-date="{{ $inputs[1] }}" data-status="{{ $inputs[2] }}" data-branch="{{ $inputs[3] }}" data-drawer="frameDrawer">{{ $tot }}</a></th>
                                        <th colspan="10"></th>
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
<div class="drawer drawer-left slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="frameDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document"></div>
</div>
@endsection