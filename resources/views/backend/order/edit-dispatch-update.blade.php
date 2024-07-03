@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Update</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Update</li>
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
                            <form class="row g-3" method="post" action="{{ route('edit.dispatched.order.update', $order->id) }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">Order Number</label>
                                    {{ html()->text($name = 'order_number', $order->ono())->class('form-control')->disabled() }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Invoice Number</label>
                                    {{ html()->number($name = 'invoice_number', $order->ino())->class('form-control')->disabled() }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Order Total</label>
                                    {{ html()->number($name = 'order_total', $order->order_total, '', '', 'any')->class('form-control')->placeholder('0') }}
                                    @error('order_total')
                                    <small class="text-danger">{{ $errors->first('order_total') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Discount</label>
                                    {{ html()->number($name = 'discount', $order->discount, '', '', 'any')->class('form-control')->placeholder('0') }}
                                    @error('discount')
                                    <small class="text-danger">{{ $errors->first('discount') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Invoice Total</label>
                                    {{ html()->number($name = 'invoice_total', $order->invoice_total, '', '', 'any')->class('form-control')->placeholder('0') }}
                                    @error('invoice_total')
                                    <small class="text-danger">{{ $errors->first('invoice_total') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Advance</label>
                                    {{ html()->number($name = 'advance', $order->advance, '', '', 'any')->class('form-control')->placeholder('0') }}
                                    @error('advance')
                                    <small class="text-danger">{{ $errors->first('advance') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Credit Used</label>
                                    {{ html()->number($name = 'credit_used', $order->credit_used, '', '', 'any')->class('form-control')->placeholder('0') }}
                                    @error('credit_used')
                                    <small class="text-danger">{{ $errors->first('credit_used') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Balance</label>
                                    {{ html()->number($name = 'balance', $order->balance, '', '', 'any')->class('form-control')->placeholder('0') }}
                                    @error('balance')
                                    <small class="text-danger">{{ $errors->first('balance') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection