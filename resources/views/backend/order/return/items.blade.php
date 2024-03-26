@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product List for Order Number {{ $order->branch->code }}/{{ $order->id }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Product List</li>
                        <li class="breadcrumb-item active">Product List</li>
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
                    <div class="card-body">
                        <form method="post" action="{{ route('sales.return.list.save', $order->id) }}">
                            @csrf
                            <div class="table-responsive theme-scrollbar">
                                <table class="display table table-bordered" id="" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>Product Name</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>Discount</th>
                                            <th>Return Value</th>
                                            <th width="10%">Qty Returned</th>
                                            <th width="10%">Customer Account</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sreturn">
                                        @forelse($data as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->product->name }}</td>
                                            <td class="text-center qty">{{ $item->qty }}</td>
                                            <td class="text-end price">{{ $item->unit_price }}</td>
                                            <td class="text-end total">{{ number_format(($item->unit_price*getPercentage($item->order->discount, $item->order->order_total))/100, 2) }}</td>
                                            <td class="text-end retval">{{ number_format(($item->unit_price - ($item->unit_price*getPercentage($item->order->discount, $item->order->order_total)/100))* $item->qty, 2) }} </td>
                                            <td>
                                                <input name="qty[]" type="number" min="" max="{{ $item->qty }}" step="1" class="form-control text-end retqty" placeholder="0" />
                                                @error('qty')
                                                <small class="text-danger">{{ $errors->first('qty') }}</small>
                                                @enderror
                                            </td>
                                            <td><input type="text" class="form-control text-end custacc" placeholder="0.00" readonly /></td>
                                        </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group mt-5">
                                <label class="form-label req">Comment / Reason</label>
                                <textarea class="form-control" placeholder="Comment / Reason" value="{{ old('comment') }}" name="comment" required></textarea>
                            </div>
                            <div class="col-12 text-end mt-5">
                                <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                <button class="btn btn-submit btn-success" type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
@endsection