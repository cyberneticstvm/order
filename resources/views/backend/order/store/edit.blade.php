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
                                <h5>Available Credit: <span class="text-success avCr">{{ number_format(getAvailableCredit($order?->customer_id ?? 0), 2) }}</span>&nbsp;&nbsp;<a href="javascript:void(0)" class="refreshAvailableCr"><i class="fa fa-refresh text-info"></i></a></h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('store.order.update', $order->id) }}" name="orderForm" id="orderForm">
                                @csrf
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ $order?->customer_id ?? 0 }}" />
                                <input type="hidden" name="disc_per" value="{{ branch()->discount_limit_percentage }}" />
                                <div class="col-md-2">
                                    <label class="form-label req">Order Date</label>
                                    {{ html()->date($name = 'order_date', $value = $order->order_date?->format('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('order_date')
                                    <small class="text-danger">{{ $errors->first('order_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">MRN / Customer ID</label>
                                    {{ html()->text($name = 'mrn', $value = $order->consultation_id ? $order->consultation_id : $order->customer_id)->class('form-control')->placeholder('MRN') }}
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
                                    {{ html()->text($name = 'mobile', $value = $order->mobile)->class('form-control custmob')->maxlength('10')->placeholder('Mobile')->required() }}
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
                                <div class="col-md-3">
                                    <label class="form-label">Camp Name</label>
                                    {{ html()->select('camp', $camps, $order->camp)->class('form-control select2')->if(Session::get('branch') != 17, function($el){
                                        return $el->attribute('disabled', 'true');
                                    })->placeholder('Select') }}
                                </div>
                                <div class="row g-4 table-responsive">
                                    <div class="col-md-3">
                                        <label class="form-label">Store Prescription</label>
                                        {{ html()->select('spectacle_id', $store_prescriptions->pluck('cid', 'id'), $order->spectacle_id)->class('form-control select2 changePresc')->attribute('data-source', 'store')->placeholder('Select') }}
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add New Row</button>
                                            <ul class="dropdown-menu dropdown-block">
                                                <li><a class="dropdown-item txt-dark" href="javascript:void(0)" onclick="addStoreOrderRow('lens', 'order', 0)"><i class="fa fa-eye txt-danger fw-bold"></i> Lens</a></li>
                                                <li><a class="dropdown-item txt-dark" href="javascript:void(0)" onclick="addStoreOrderRow('frame', 'order', 0)"><i class="fa fa-square-o txt-success fw-bold"></i> Frame</a></li>
                                                <li><a class="dropdown-item txt-dark" href="javascript:void(0)" onclick="addStoreOrderRow('service', 'order', 0)"><i class="icofont icofont-businesswoman txt-muted fw-bold"></i> Service</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h5 class="text-center text-secondary">Order Details</h5>
                                        <table class="table table-bordered table-stripped mt-3">
                                            <thead class="text-center">
                                                <tr>
                                                    <th width="5%">Del</th>
                                                    <th width="5%">Eye</th>
                                                    <th width="5%">SPH</th>
                                                    <th width="5%">CYL</th>
                                                    <th width="5%">AXIS</th>
                                                    <th width="5%">ADD</th>
                                                    <th width="5%">VA</th>
                                                    <th width="5%">IPD</th>
                                                    <th width="35%">Product</th>
                                                    <th width="5%">Qty</th>
                                                    <th>Price</th>
                                                    <th width="20%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="powerbox">
                                                @forelse($order->details as $key => $item)
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                    <td>
                                                        <select class="border-0" name="eye[]">
                                                            @if(in_array($item->eye, ['re', 'le', 'both']))
                                                            <option value="re" {{ ($item->eye == 're') ? 'selected' : '' }}>RE</option>
                                                            <option value="le" {{ ($item->eye == 'le') ? 'selected' : '' }}>LE</option>
                                                            <option value="both" {{ ($item->eye == 'both') ? 'selected' : '' }}>Both</option>
                                                            @endif
                                                            @if(in_array($item->eye, ['frame']))
                                                            <option value="frame" {{ ($item->eye == 'frame') ? 'selected' : '' }}>Frame</option>
                                                            @endif
                                                            @if(in_array($item->eye, ['service']))
                                                            <option value="service" {{ ($item->eye == 'service') ? 'selected' : '' }}>Service</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    @if(in_array($item->eye, ['re', 'le', 'both']))

                                                    <td>
                                                        {{ html()->select('sph[]', $powers?->where('name', 'sph')->pluck('value', 'value'), $item->sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->if($key==0, function($el){
                                                            return $el->attribute('class', 'fSph');
                                                        })->if($key==1, function($el){
                                                            return $el->attribute('class', 'sSph');
                                                        })->attribute('id', 'sph_'.$key) }}
                                                        <input type='hidden' name='fitting[]' value="0">
                                                    </td>
                                                    <td>
                                                        {{ html()->select('cyl[]', $powers?->where('name', 'cyl')->pluck('value', 'value'), $item->cyl ??  $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->if($key==0, function($el){
                                                            return $el->attribute('class', 'fCyl');
                                                        })->if($key==1, function($el){
                                                            return $el->attribute('class', 'sCyl');
                                                        })->attribute('id', 'cyl_'.$key) }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('axis[]', $powers?->where('name', 'axis')->pluck('value', 'value'), $item->axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->if($key==0, function($el){
                                                            return $el->attribute('class', 'fAxis');
                                                        })->if($key==1, function($el){
                                                            return $el->attribute('class', 'sAxis');
                                                        })->attribute('id', 'axis_'.$key) }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('add[]', $powers?->where('name', 'add')->pluck('value', 'value'), $item->add ?? $powers?->where('name', 'add')->where('default', 'true')?->first()?->value)->class('border-0 select2')->if($key==0, function($el){
                                                            return $el->attribute('class', 'fAdd');
                                                        })->if($key==1, function($el){
                                                            return $el->attribute('class', 'sAdd');
                                                        })->attribute('id', 'add_'.$key) }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('va[]', $item->va ?? '')->class('w-100 border-0 text-center')->if($key==0, function($el){
                                                            return $el->attribute('class', 'fVa');
                                                        })->if($key==1, function($el){
                                                            return $el->attribute('class', 'sVa');
                                                        })->maxlength(6)->placeholder('VA') }}
                                                    </td>
                                                    <td width="15%">
                                                        {{ html()->text('ipd[]', $item->ipd ?? '')->class('w-100 border-0 text-center')->if($key==0, function($el){
                                                            return $el->attribute('class', 'fIpd');
                                                        })->if($key==1, function($el){
                                                            return $el->attribute('class', 'sIpd');
                                                        })->maxlength(6)->placeholder('VA') }}
                                                    </td>
                                                    @endif
                                                    @if(in_array($item->eye, ['frame', 'service']))
                                                    <td colspan="4">
                                                        <div class="d-none">
                                                            <input type="hidden" name="sph[]" />
                                                            <input type="hidden" name="cyl[]" />
                                                            <input type="hidden" name="axis[]" />
                                                            <input type="hidden" name="add[]" />
                                                            <input type="hidden" name="ipd[]" />
                                                            <input type="hidden" name="va[]" />
                                                        </div>
                                                    </td>
                                                    <td colspan="2">
                                                        <select class="border-0" name="fitting[]">
                                                            <option value="0">Select</option>
                                                            <option value="1" {{ ($item->fitting) ? 'selected' : '' }}>Fitting</option>
                                                        </select>
                                                    </td>
                                                    @endif
                                                    <td>
                                                        {{ html()->select('product_id[]', $products->when(in_array($item->eye, ['re', 'le', 'both']), function($q){
                                                            return $q->where('category', 'lens');
                                                        })->when(in_array($item->eye, ['frame']), function($q){
                                                            return $q->where('category', 'frame');
                                                        })->when(in_array($item->eye, ['service']), function($q){
                                                            return $q->where('category', 'service');
                                                        })->pluck('name', 'id'), $item->product_id)->class('border-0 select2 selPdct offerPdct')->attribute('id', $item->id)->placeholder('Select')->required() }}
                                                    </td>
                                                    <td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" value="{{ $item->qty }}" required /></td>
                                                    <td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' value="{{ $item->unit_price }}" step="any" required readonly /></td>
                                                    <td><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" value="{{ $item->total }}" required readonly /></td>
                                                </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2">Int. Add</th>
                                                    <th>A Size</th>
                                                    <th>B Size</th>
                                                    <th>DBL</th>
                                                    <th>FH</th>
                                                    <th>ED</th>
                                                    <th>VD</th>
                                                    <th colspan="4"></th>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        {{ html()->text('int_add', $order->int_add)->class('w-100 border-0 text-center')->maxlength(6)->placeholder('Int.Add') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('a_size', $order->a_size)->class('w-100 border-0 text-center')->maxlength(2)->placeholder('A Size') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('b_size', $order->b_size)->class('w-100 border-0 text-center')->maxlength(2)->placeholder('B Size') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('dbl', $order->dbl)->class('w-100 border-0 text-center')->maxlength(2)->placeholder('DBL') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('fh', $order->fh)->class('w-100 border-0 text-center')->maxlength(2)->placeholder('FH') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('ed', $order->ed)->class('w-100 border-0 text-center')->maxlength(2)->placeholder('ED') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('vd', $order->vd)->class('w-100 border-0 text-center')->maxlength(2)->placeholder('VD') }}
                                                    </td>
                                                    <td colspan="3" class="text-end fw-bold border-0">
                                                        Order Total
                                                        @error('order_total')
                                                        <br /><small class="text-danger">{{ $errors->first('order_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="text" name="order_total" class="text-end border-0 fw-bold w-100 subtotal readOnly" placeholder="0.00" value="{{ $order->order_total }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        {{ html()->text('w_angle', $order->w_angle)->class('w-100 border-0 text-center')->maxlength(6)->placeholder('W Angle') }}
                                                    </td>
                                                    <td colspan="6">
                                                        {{ html()->text('special_lab_note', $order->special_lab_note)->class('w-100 border-0 text-center')->placeholder('Special Lab Note') }}
                                                    </td>
                                                    <td colspan="3" class="text-end fw-bold border-0">Discount</td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='discount' class="w-100 border-0 text-end discount" placeholder="0.00" step="any" value="{{ $order->discount }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td>RPD</td>
                                                    <td>{{ html()->text('rpd', $order->rpd)->class('w-100 border-0 text-center')->maxlength(3)->placeholder('RPD') }}</td>
                                                    <td>LPD</td>
                                                    <td>{{ html()->text('lpd', $order->lpd)->class('w-100 border-0 text-center')->maxlength(3)->placeholder('LPD') }}</td>
                                                    <td colspan="7" class="text-end fw-bold border-0">
                                                        Total After Discount
                                                        @error('invoice_total')
                                                        <br /><small class="text-danger">{{ $errors->first('invoice_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="number" name="invoice_total" class="text-end border-0 fw-bold w-100 nettotal readOnly" min="1" step="any" placeholder="0.00" value="{{ $order->invoice_total }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold border-0">Royalty Card</td>
                                                    <td colspan="1">
                                                        {{ html()->select('card_type', $cards, $order->card_type)->class('border-0')->attribute('id', 'ctype')->placeholder('Card Type') }}
                                                    </td>
                                                    <td class="fw-bold border-0" colspan="2"><input type="text" name='card_number' class="w-100 border-0" placeholder="Card Number" value="{{ $order->card_number }}" /></td>
                                                    <td><button class="btn btn-primary" onclick="return validateRoyaltyCard();">Validate</button></td>
                                                    <td colspan="5" class="text-end">Royalty Card Discount</td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='royalty_discount' class="w-100 border-0 text-end royalty_discount" placeholder="0.00" step="any" value="{{ $order->royalty_discount }}" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" class="text-end fw-bold border-0">Advance</td>
                                                    <td colspan="2">
                                                        {{ html()->select('payment_mode', $pmodes->pluck('name', 'id'), $order->payments?->first()?->payment_mode)->class('border-0')->attribute('id', 'pmode')->placeholder('Payment Mode') }}
                                                    </td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='advance' class="w-100 border-0 text-end advance" placeholder="0.00" step="any" value="{{ $order->advance }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="fw-bold border-0"></td>
                                                    <td colspan="6" class="border-0"><input type="hidden" class="border-0 text-success fw-bold avCr" name="available_credit" value="{{ ($order->credit_used) ?? getAvailableCredit($order?->customer_id ?? 0) }}" readonly /></td>
                                                    <td colspan="2" class="text-end fw-bold border-0">Credit Used</td>
                                                    <td class="border-0">
                                                        {{ html()->number('credit_used', $order->credit_used, '', '', $step = 'any')->class('text-end border-0 fw-bold w-100 credit_used')->if((getAvailableCredit($order?->customer_id ?? 0) + $order->credit_used) <= 0, function($el){
                                                            return $el->attribute('readonly', 'true');
                                                        })->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="fw-bold border-0 form-check_1">
                                                        <label class="form-check-label">Generate Invoice</label>
                                                        {{ html()->checkbox('generate_invoice',  ($order->invoice_generated_at) ? true : false, 1)->class('form-check-input')->attribute('id', 'form-check_1') }}
                                                    </td>
                                                    <td colspan="6" class="text-end fw-bold border-0">Balance As On Order Created Date</td>
                                                    <td class="border-0"><input type="text" name="balance" class="text-end border-0 fw-bold w-100 balance readOnly" placeholder="0.00" value="{{ number_format($order->balance + $order->credit_used, 2, '.', '') }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="11" class="text-end fw-bold border-0">Paid</td>
                                                    <td class="border-0 text-end">{{ number_format($order->payments->sum('amount') + $order->credit_used, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="11" class="text-end fw-bold border-0">Due Today</td>
                                                    <td class="border-0 text-end">{{ number_format($order->invoice_total - ($order->payments->sum('amount') + $order->credit_used), 2) }}</td>
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