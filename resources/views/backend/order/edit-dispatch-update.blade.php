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
                                    <label class="form-label req">Discount</label>
                                    {{ html()->number($name = 'discount', $order->discount, '', '', 'any')->class('form-control')->placeholder('0') }}
                                    @error('discount')
                                    <small class="text-danger">{{ $errors->first('discount') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Credit Used</label>
                                    {{ html()->number($name = 'credit_used', $order->credit_used, '', '', 'any')->class('form-control')->placeholder('0') }}
                                    @error('credit_used')
                                    <small class="text-danger">{{ $errors->first('credit_used') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Payment ID</th>
                                                <th>Amount</th>
                                                <th>Type</th>
                                                <th>Mode</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($payments as $key => $item)
                                            <tr>
                                                <td>
                                                    {{ html()->text($name = 'payment_id[]', $item->id)->class('form-control')->attribute('readonly') }}
                                                </td>
                                                <td>
                                                    {{ html()->number($name = 'amount[]', $item->amount, '', '', 'any')->class('form-control')->placeholder('0') }}
                                                </td>
                                                <td>
                                                    {{ html()->select($name = 'payment_type[]', array('advance' => 'Order Advance') + paymentypes(), $item->payment_type)->class('form-control select2')->placeholder('Select') }}
                                                </td>
                                                <td>
                                                    {{ html()->select($name = 'payment_mode[]', $value = $pmodes, $item->payment_mode)->class('form-control select2')->placeholder('Select') }}
                                                </td>
                                                <td>
                                                    {{ html()->text($name = 'notes[]', $value = $item->notes)->class('form-control')->placeholder('Notes') }}
                                                </td>
                                            </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                    @error('payment_id')
                                    <small class="text-danger">{{ $errors->first('payment_id') }}</small>
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