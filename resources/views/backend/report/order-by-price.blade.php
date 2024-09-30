@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Order by Price</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Order by Price</li>
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
                            <form class="row g-3" method="post" action="{{ route('order.by.price.fetch') }}">
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
                                    <label class="form-label">Branch</label>
                                    {{ html()->select($name = 'branch', $branches, ($inputs[2]) ?? old('branch'))->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Category</label>
                                    {{ html()->select($name = 'category', $categories, ($inputs[3]) ?? old('category'))->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Min. Price.</label>
                                    {{ html()->number($name = 'minimum', $value = ($inputs[4]) ?? old('minimum'), '', '', '')->class('form-control')->placeholder('0.00') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Max. Price.</label>
                                    {{ html()->number($name = 'maximum', $value = ($inputs[5]) ?? old('maximum'), '', '', '')->class('form-control')->placeholder('0.00') }}
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
                                        <th>Product</th>
                                        <th>Unit Price</th>
                                        <th>Order Count</th>
                                        <th>Category</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($records as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->product->selling_price }}</td>
                                        <td>{{ $item->ocount }}</td>
                                        <td>{{ $item->eye }}</td>
                                        <td class="text-end">{{ $item->amount }}</td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold">{{ number_format($records->sum('amount'), 2) }}</td>
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