@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Sales Return Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Sales Returns</li>
                        <li class="breadcrumb-item active">Sales Return Register</li>
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
                            <form class="row g-3" method="post" action="{{ route('sales.return.fetch') }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Order ID / Invoice Number / Mobile Number</label>&nbsp;&nbsp;<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<strong>Number Parts Only.</strong>"><i class="fa fa-info txt-info"></i></a>
                                    <div class="input-group">
                                        {{ html()->text($name = 'query_string', $value = old('query_string'))->class('form-control')->maxlength('10')->placeholder('Order ID / Invoice Number / Mobile Number') }}
                                        <button class="btn btn-outline-warning btn-submit" id="button-addon2" type="submit">Fetch</button>
                                    </div>
                                    @error('query_string')
                                    <small class="text-danger">{{ $errors->first('query_string') }}</small>
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
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Return ID</th>
                                        <th>Order Number</th>
                                        <th>Order Branch</th>
                                        <th>Returned Branch</th>
                                        <th>Customer Name</th>
                                        <th>Order Date</th>
                                        <th>Return Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="{{ route('sales.return.list.detail', encrypt($item->id)) }}">{{ $item->id }}</a></td>
                                        <td>{{ $item->order->branch->code }}/{{ $item->order->id }}</td>
                                        <td>{{ $item->orderBranch->name }}</td>
                                        <td>{{ $item->returnBranch->name }}</td>
                                        <td>{{ $item->order->name }}</td>
                                        <td>{{ $item->order->created_at->format('d, M Y') }}</td>
                                        <td>{{ $item->created_at->format('d, M Y') }}</td>
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