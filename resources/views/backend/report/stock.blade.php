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
                                    <label class="form-label req">Product Category</label>
                                    <div class="input-group">
                                        {{ html()->select('branch', array('0' => 'Main Branch') + $branches, $inputs[0])->class('form-control select2') }}
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
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Product Name</th>
                                        <th>Sold Qty</th>
                                        <th>Purchased Qty</th>
                                        <th>Transferred Qty</th>
                                        <th>Returned Qty</th>
                                        <th>Damaged Qty</th>
                                        <th>Balance Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->soldQty }}</td>
                                        <td>{{ $item->purchasedQty }}</td>
                                        <td>{{ $item->transferredQty }}</td>
                                        <td>{{ $item->returnedQty }} </td>
                                        <td>{{ $item->damagedQty }}</td>
                                        <td>{{ $item->balanceQty }} </td>
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
</div>
@endsection