@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Update Order</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Order</li>
                        <li class="breadcrumb-item active">Update</li>
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
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <h5>Update Order</h5><span>Update Order</span>
                            </div>
                            <div class="col text-end">
                                <h5>Available Credit: <span class="text-success avCr">{{ number_format(getAvailableCredit($order?->customer_id ?? 0) + $order->credit_used, 2) }}</span>&nbsp;&nbsp;<a href="javascript:void(0)" class="refreshAvailableCr"><i class="fa fa-refresh text-info"></i></a></h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('solution.order.update', $order->id) }}" name="orderForm" id="orderForm">
                                @csrf
                                <input type="hidden" name="consultation_id" value="{{ $order->consultation_id }}" />
                                <input type="hidden" name="disc_per" value="{{ branch()->discount_limit_percentage }}" />
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ $order?->customer_id ?? 0 }}" />
                                <div class="col-md-2">
                                    <label class="form-label req">Order Date</label>
                                    {{ html()->date($name = 'order_date', $value = $order->order_date?->format('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('order_date')
                                    <small class="text-danger">{{ $errors->first('order_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">MRN</label>
                                    {{ html()->text($name = 'mrn', $value = $order->consultation_id)->class('form-control')->placeholder('MRN') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Name</label>
                                    {{ html()->text($name = 'name', $value = $order->name)->class('form-control')->placeholder('Customer Name')->required() }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Age</label>
                                    {{ html()->number($name = 'age', $value = $order->age)->class('form-control')->placeholder('0') }}
                                    @error('age')
                                    <small class="text-danger">{{ $errors->first('age') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Address</label>
                                    {{ html()->text($name = 'place', $value = $order->place)->class('form-control')->placeholder('Place')->required() }}
                                    @error('place')
                                    <small class="text-danger">{{ $errors->first('place') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Mobile</label>
                                    {{ html()->text($name = 'mobile', $value = $order->mobile)->class('form-control')->maxlength('10')->placeholder('Mobile')->required() }}
                                    @error('mobile')
                                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Alt. Mobile</label>
                                    {{ html()->text($name = 'alt_mobile', $order->alt_mobile)->class('form-control')->maxlength('10')->placeholder('Alt Mobile') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Product Adviser</label>
                                    {{ html()->select('product_adviser', $padvisers->pluck('name', 'id'), $order->product_adviser)->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('product_adviser')
                                    <small class="text-danger">{{ $errors->first('product_adviser') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Expected Delivery Date</label>
                                    {{ html()->date($name = 'expected_delivery_date', $value = $order->expected_delivery_date?->format('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('expected_delivery_date')
                                    <small class="text-danger">{{ $errors->first('expected_delivery_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Order Note</label>
                                    {{ html()->text($name = 'order_note', $value = $order->order_note)->class('form-control')->placeholder('Order Note') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Lab Note</label>
                                    {{ html()->text($name = 'lab_note', $value = $order->lab_note)->class('form-control')->placeholder('Lab Note') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Invoice Note</label>
                                    {{ html()->text($name = 'invoice_note', $value = $order->invoice_note)->class('form-control')->placeholder('Invoice Note') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Case Type</label>
                                    {{ html()->select('case_type', casetypes(), $order->case_type)->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('case_type')
                                    <small class="text-danger">{{ $errors->first('case_type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Order Status</label>
                                    {{ html()->select('order_status', orderStatuses(), $order->order_status)->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('order_status')
                                    <small class="text-danger">{{ $errors->first('order_status') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Order Type</label>
                                    {{ html()->select('type', array('btob' => 'B2B', 'btoc' => 'B2C'), $order->type)->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('type')
                                    <small class="text-danger">{{ $errors->first('type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">State</label>
                                    {{ html()->select('state', $states->pluck('name', 'id'), $order->state)->class('form-control select2')->placeholder('Select')->required() }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">GSTIN</label>
                                    {{ html()->text($name = 'gstin', $value = $order->gstin)->class('form-control')->placeholder('GSTIN') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Company Name</label>
                                    {{ html()->text($name = 'company_name', $value = $order->company_name)->class('form-control')->placeholder('Company Name') }}
                                </div>
                                <div class="row g-4 table-responsive">
                                    <div class="col text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add New Row</button>
                                            <ul class="dropdown-menu dropdown-block">
                                                <li><a class="dropdown-item txt-dark" href="javascript:void(0)" onclick="addStoreOrderRow('solution', 'order')"><i class="fa fa-eye txt-danger fw-bold"></i> Solution</a></li>
                                                <li><a class="dropdown-item txt-dark" href="javascript:void(0)" onclick="addStoreOrderRow('accessory', 'order')"><i class="fa fa-square-o txt-success fw-bold"></i> Accessory</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h5 class="text-center text-secondary">Order Details</h5>
                                        <table class="table table-bordered table-stripped mt-3">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Remove</th>
                                                    <th width="40%">Product</th>
                                                    <th>Batch</th>
                                                    <th>Expiry</th>
                                                    <th width="5%">Qty</th>
                                                    <th>Price</th>
                                                    <th width="10%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="powerbox">
                                                @forelse($order->details as $key => $item)
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('product_id[]', $products->pluck('name', 'id'), (old('product_id')) ? old('product_id')[0] : $item->product_id)->class('border-0 select2 selPdct')->attribute('data-batch', 'NA')->attribute('data-category', 'solution')->attribute('id', 'cat-solution'.$key)->placeholder('Select')->required() }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('batch_number[]', (old('batch_number')) ? old('batch_number')[0] : $item->batch_number)->class('form-control')->placeholder('Batch Number') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->date('expiry_date[]', old('expiry_date')[0] ?? $item->expiry_date?->format('Y-m-d'))->class('form-control')->placeholder(date('Y-m-d')) }}
                                                    </td>
                                                    <td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" value="{{ $item->qty }}" required /></td>
                                                    <td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" value="{{ $item->unit_price }}" required readonly /></td>
                                                    <td width="15%"><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" value="{{ $item->total }}" required readonly /></td>
                                                </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="6" class="text-end fw-bold border-0">
                                                        Order Total
                                                        @error('order_total')
                                                        <br /><small class="text-danger">{{ $errors->first('order_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="text" name="order_total" class="text-end border-0 fw-bold w-100 subtotal readOnly" placeholder="0.00" value="{{ $order->order_total }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="text-end fw-bold border-0">Discount</td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='discount' class="w-100 border-0 text-end discount" placeholder="0.00" step="any" value="{{ $order->discount }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="text-end fw-bold border-0">
                                                        Total After Discount
                                                        @error('invoice_total')
                                                        <br /><small class="text-danger">{{ $errors->first('invoice_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="number" name="invoice_total" class="text-end border-0 fw-bold w-100 nettotal readOnly" min="1" step="any" placeholder="0.00" value="{{ $order->invoice_total }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="text-end fw-bold border-0">Advance</td>
                                                    <td>
                                                        {{ html()->select('payment_mode', $pmodes->pluck('name', 'id'), $order->payments?->first()?->payment_mode)->class('border-0')->attribute('id', 'pmode')->placeholder('Payment Mode') }}
                                                    </td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='advance' class="w-100 border-0 text-end advance" placeholder="0.00" step="any" value="{{ $order->advance }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="fw-bold border-0"></td>
                                                    <td colspan="2" class="border-0"><input type="hidden" class="border-0 text-success fw-bold avCr" name="available_credit" value="{{ ($order->credit_used) ?? getAvailableCredit($order?->customer_id ?? 0) }}" readonly /></td>
                                                    <td colspan="2" class="text-end fw-bold border-0">Credit Used</td>
                                                    <td class="border-0">
                                                        {{ html()->number('credit_used', $order->credit_used, '', '', $step = 'any')->class('text-end border-0 fw-bold w-100')->if((getAvailableCredit($order?->customer_id ?? 0) + $order->credit_used) <= 0, function($el){
                                                            return $el->attribute('readonly', 'true');
                                                        })->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="text-end fw-bold border-0">Balance</td>
                                                    <td class="border-0"><input type="text" name="balance" class="text-end border-0 fw-bold w-100 balance readOnly" placeholder="0.00" value="{{ number_format($order->balance + $order->credit_used, 2, '.', '') }}" /></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                        <button class="btn btn-submit btn-success" onclick="return validateOrderForm()" type="submit">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
@endsection