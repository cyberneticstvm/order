@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Stock In Hand</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Inventory</li>
                        <li class="breadcrumb-item active">Stock In Hand</li>
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
                            <form class="row g-3" method="post" action="{{ route('report.stock.status.fetch') }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Product Category</label>
                                    <div class="input-group">
                                        {{ html()->select('category', array('frame' => 'Frame', 'lens' => 'Lens', 'solution' => 'Solution', 'accessory' => 'Accessory'), $inputs[1])->class('form-control select2') }}
                                    </div>
                                    @error('category')
                                    <small class="text-danger">{{ $errors->first('category') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch</label>
                                    <div class="input-group">
                                        {{ html()->select('branch', $branches, $inputs[0])->class('form-control select2') }}
                                    </div>
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
                                        <li><a class="dropdown-item txt-dark" href="{{ route('export.stock.status.pdf', ['category' => $inputs[1], 'branch' => $inputs[0]]) }}" target="_blank"><i class="icofont icofont-file-pdf txt-danger fw-bold"></i> Pdf</a></li>
                                        <li><a class="dropdown-item" href="{{ route('export.stock.status.excel', ['category' => $inputs[1], 'branch' => $inputs[0]]) }}" target="_blank"><i class="icofont icofont-file-excel txt-success fw-bold"></i> Excel</a></li>
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
                                        <th>PID</th>
                                        <th>Code</th>
                                        <th>Item Total</th>
                                        <th>Booked</th>
                                        <th>Billed</th>
                                        <th>Transfer In</th>
                                        <th>Transfer Out</th>
                                        <th>Returned</th>
                                        <th>Damaged</th>
                                        <th>Balance</th>
                                        <th>Shelf</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item?->name ?? '' }}</td>
                                        <td>{{ $item?->id ?? '' }}</td>
                                        <td>{{ $item->pcode }}</td>
                                        <td>{{ $item->item_tot ?? 0 }}</td>
                                        <td>{{ $item->soldQty ?? 0}}</td>
                                        <td>{{ $item->billedQty ?? 0 }}</td>
                                        <td>{{ $item->purchasedQty ?? 0 }}</td>
                                        <td>{{ $item->transferredQty ?? 0 }}</td>
                                        <td>{{ $item->returnedQty ?? 0 }} </td>
                                        <td>{{ $item->damagedQty ?? 0 }}</td>
                                        <td>{{ $item->balanceQty ?? 0 }} </td>
                                        <td>{{ $item->shelfQty ?? 0 }} </td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="fw-bold text-end">Total</td>
                                        <td class="fw-bold">{{ $data->sum('item_tot') }}</td>
                                        <td class="fw-bold text-right"><a href="javascript:void(0)" class="bkdPdct" data-branch="{{ $inputs[0] }} " data-category="{{ $inputs[1] }}" data-drawer="bookedPdctDrawer">{{ $data->sum('soldQty') }}</a></td>
                                        <td class="fw-bold">{{ $data->sum('billedQty') }}</td>
                                        <td class="fw-bold text-right"><a href="javascript:void(0)" class="trnsInPdct" data-branch="{{ $inputs[0] }} " data-category="{{ $inputs[1] }}" data-drawer="transferInPdctDrawer">{{ $data->sum('purchasedQty') }}</a></td>
                                        <td class="fw-bold text-right"><a href="javascript:void(0)" class="trnsOutPdct" data-branch="{{ $inputs[0] }} " data-category="{{ $inputs[1] }}" data-drawer="transferInPdctDrawer">{{ $data->sum('transferredQty') }}</a></td>
                                        <td class="fw-bold text-right">{{ $data->sum('returnedQty') }}</td>
                                        <td class="fw-bold text-right">{{ $data->sum('damagedQty') }}</td>
                                        <td class="fw-bold text-right">{{ $data->sum('balanceQty') }}</td>
                                        <td class="fw-bold text-right">{{ $data->sum('shelfQty') }}</td>
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
<div class="drawer drawer-right slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="bookedPdctDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document"></div>
</div>
<div class="drawer drawer-right slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="transferInPdctDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document"></div>
</div>
<div class="drawer drawer-right slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="transferOutPdctDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document"></div>
</div>
@endsection