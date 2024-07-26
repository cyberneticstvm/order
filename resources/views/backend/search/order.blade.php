@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Search</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Search</li>
                        <li class="breadcrumb-item active">Order</li>
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
                            <form class="row g-3" method="post" action="{{ route('search.order.fetch') }}">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label req">Search Term</label>
                                    {{ html()->text($name = 'search_term', $value = ($inputs[0]) ?? old('search_term'))->class('form-control')->placeholder('Search by Order Id, Mobile Number, Customer Name') }}
                                    @error('search_term')
                                    <small class="text-danger">{{ $errors->first('search_term') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Search</button>
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
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Order Number</th>
                                        <th>Customer Name</th>
                                        <th>Customer ID</th>
                                        <th>Mobile</th>
                                        <th>Alt. Mobile</th>
                                        <th>Bill Total</th>
                                        <th>Advance</th>
                                        <th>Balance</th>
                                        <th>Presc.</th>
                                        <th>Order</th>
                                        <th>Invoice</th>
                                        <th>Order Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="javascript:void(0)" class="dayBook" data-from-date="" data-to-date="" data-type="order" data-mode="{{ $item->id }}" data-branch="" data-drawer="orderDrawer">{{ $item->ono() }}</a></td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->customer_id }}</td>
                                        <td>{{ $item->mobile }}</td>
                                        <td>{{ $item->alt_mobile }}</td>
                                        <td>{{ $item->invoice_total }}</td>
                                        <td>{{ number_format($item->payments->sum('amount') + $item->credit_used, 2) }}</td>
                                        <td>{{ number_format($item->invoice_total - ($item->payments->sum('amount') + $item->credit_used + $item->discount), 2) }}</td>
                                        <td class="text-center"><a href="{{ route('store.order.prescription', encrypt($item->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td class="text-center"><a href="{{ route('store.order.receipt', encrypt($item->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td class="text-center"><a href="{{ route('store.order.invoice', encrypt($item->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        @if($item->deleted_at)
                                        <td>{!! $item->status() !!}</td>
                                        @else
                                        <td><a href="{{ route('order.status', encrypt($item->id)) }}">{{ $item->order_status }}</a></td>
                                        @endif
                                        <td class="text-center"><a href="{{ route(($item->category == 'store') ? 'store.order.edit' : 'solution.order.edit', encrypt($item->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route(($item->category == 'store') ? 'store.order.delete' : 'solution.order.delete', encrypt($item->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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
<div class="drawer drawer-left slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="orderDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document"></div>
</div>
@endsection