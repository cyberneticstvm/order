@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Order</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Order</li>
                        <li class="breadcrumb-item active">Create</li>
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
                                <h5>Create Order</h5><span>Create New Order</span>
                            </div>
                            <div class="col text-end">
                                <h5>Available Credit: <span class="text-success avCr">{{ getAvailableCredit($patient?->id ?? 0) }}</span>&nbsp;&nbsp;<a href="javascript:void(0)" class="refreshAvailableCr"><i class="fa fa-refresh text-info"></i></a></h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('store.order.save') }}" name="orderForm" id="orderForm">
                                @csrf
                                <input type="hidden" name="consultation_id" value="{{ $patient?->mrn ?? 0 }}" />
                                <input type="hidden" name="registration_id" value="{{ $registration?->id ?? 0 }}" />
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ $patient?->id ?? 0 }}" />
                                <input type="hidden" name="disc_per" value="{{ branch()->discount_limit_percentage }}" />
                                <div class="col-md-2">
                                    <label class="form-label req">Order Date</label>
                                    {{ html()->date($name = 'order_date', $value = date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('order_date')
                                    <small class="text-danger">{{ $errors->first('order_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">MRN / Cutomer ID</label>
                                    {{ html()->text($name = 'mrn', $value = $patient?->mrn ? $patient?->mrn : $patient?->id)->class('form-control')->placeholder('MRN') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Name</label>
                                    {{ html()->text($name = 'name', $value = $patient?->name ?? old('name'))->class('form-control')->placeholder('Customer Name')->required() }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Age</label>
                                    {{ html()->number($name = 'age', $value = $patient?->age ?? old('age'))->class('form-control')->placeholder('0') }}
                                    @error('age')
                                    <small class="text-danger">{{ $errors->first('age') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Address</label>
                                    {{ html()->text($name = 'place', $value = $patient?->address ?? old('place'))->class('form-control')->placeholder('Place')->required() }}
                                    @error('place')
                                    <small class="text-danger">{{ $errors->first('place') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Mobile</label>
                                    {{ html()->text($name = 'mobile', $value = $patient?->mobile ?? old('mobile'))->class('form-control custmob')->maxlength('10')->placeholder('Mobile')->required() }}
                                    @error('mobile')
                                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Alt. Mobile</label>
                                    {{ html()->text($name = 'alt_mobile', $patient?->alt_mobile ?? old('alt_mobile'))->class('form-control')->maxlength('10')->placeholder('Alt Mobile') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Product Adviser</label>
                                    {{ html()->select('product_adviser', $padvisers->pluck('name', 'id'), old('product_adviser'))->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('product_adviser')
                                    <small class="text-danger">{{ $errors->first('product_adviser') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Expected Delivery Date</label>
                                    {{ html()->date($name = 'expected_delivery_date', $value = date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('expected_delivery_date')
                                    <small class="text-danger">{{ $errors->first('expected_delivery_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Order Note</label>
                                    {{ html()->text($name = 'order_note', $value = old('order_note'))->class('form-control')->placeholder('Order Note') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Lab Note</label>
                                    {{ html()->text($name = 'lab_note', $value = old('lab_note'))->class('form-control')->placeholder('Lab Note') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Invoice Note</label>
                                    {{ html()->text($name = 'invoice_note', $value = old('invoice_note'))->class('form-control')->placeholder('Invoice Note') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Case Type</label>
                                    {{ html()->select('case_type', casetypes(), 'rexine')->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('case_type')
                                    <small class="text-danger">{{ $errors->first('case_type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Order Status</label>
                                    {{ html()->select('order_status', orderStatuses(), 'booked')->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('order_status')
                                    <small class="text-danger">{{ $errors->first('order_status') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Order Type</label>
                                    {{ html()->select('type', array('btob' => 'B2B', 'btoc' => 'B2C'), 'btoc')->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('type')
                                    <small class="text-danger">{{ $errors->first('type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">State</label>
                                    {{ html()->select('state', $states->pluck('name', 'id'), $states->where('default', 'true')->first()->id)->class('form-control select2')->placeholder('Select')->required() }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">GSTIN</label>
                                    {{ html()->text($name = 'gstin', $value = $patient?->gstin ?? old('gstin'))->class('form-control')->placeholder('GSTIN') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Company Name</label>
                                    {{ html()->text($name = 'company_name', $value = $patient?->company_name ?? old('company_name'))->class('form-control')->placeholder('Company Name') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Camp Name</label>
                                    {{ html()->select('camp', $camps, old('camp'))->class('form-control select2')->if(Session::get('branch') != 17, function($el){
                                        return $el->attribute('disabled', 'true');
                                    })->placeholder('Select') }}
                                </div>
                                <div class="row g-4 table-responsive">
                                    <div class="col-md-3">
                                        <label class="form-label">Store Prescription</label>
                                        {{ html()->select('spectacle_id', $store_prescriptions->pluck('cid', 'id'), $spectacle?->id)->class('form-control select2 changePresc')->attribute('data-source', 'store')->placeholder('Select') }}
                                    </div>
                                    <div class="col-md-9 text-end">
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
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                        <input type='hidden' name='fitting[]' value="0">
                                                    </td>
                                                    <td>
                                                        <select class="border-0" name="eye[]">
                                                            <option value="re">RE</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('sph[]', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->re_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2 fSph') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('cyl[]', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->re_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2 fCyl') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('axis[]', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->re_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2 fAxis') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('add[]', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->re_add ?? $powers?->where('name', 'add')->where('default', 'true')?->first()?->value)->class('border-0 select2 fAdd') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('va[]', $spectacle?->re_va ?? '')->class('w-100 border-0 text-center fVa')->maxlength(6)->attribute('readonly', 'true')->placeholder('VA') }}
                                                    </td>
                                                    <td><input type="text" name='ipd[]' class="w-100 border-0 text-center fIpd" placeholder="IPD" value="{{ $spectacle?->rpd ?? '' }}" maxlength="6" readonly /></td>
                                                    <td>
                                                        {{ html()->select('product_id[]', $products->where('category', 'lens')->pluck('name', 'id'), old('product_id'))->class('border-0 select2 selPdct pdctFirst')->attribute('data-batch', 'NA')->attribute('data-category', 'lens')->attribute('id', 'cat-lens1')->placeholder('Select')->required() }}
                                                    </td>
                                                    <td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td>
                                                    <td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td>
                                                    <td width="15%"><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                        <input type='hidden' name='fitting[]' value="0">
                                                    </td>
                                                    <td>
                                                        <select class="border-0" name="eye[]">
                                                            <option value="le">LE</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('sph[]', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->le_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2 sSph')->attribute('id', 'sph1') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('cyl[]', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->le_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2 sCyl')->attribute('id', 'cyl1') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('axis[]', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->le_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2 sAxis')->attribute('id', 'axis1') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('add[]', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->le_add ?? $spectacle?->le_add ?? $powers?->where('name', 'add')->where('default', 'true')?->first()?->value)->class('border-0 select2 sAdd')->attribute('id', 'add1') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('va[]', $spectacle?->le_va ?? '')->class('w-100 border-0 text-center sVa')->maxlength(6)->attribute('readonly', 'true')->placeholder('VA') }}
                                                    </td>
                                                    <td><input type="text" name='ipd[]' class="w-100 border-0 text-center sIpd" placeholder="IPD" value="{{ $spectacle?->lpd ?? '' }}" maxlength="6" readonly /></td>
                                                    <td>
                                                        {{ html()->select('product_id[]', $products->where('category', 'lens')->pluck('name', 'id'), old('product_id'))->class('border-0 select2 selPdct pdctSecond')->attribute('data-batch', 'NA')->attribute('data-category', 'lens')->attribute('id', 'cat-lens2')->placeholder('Select')->required() }}
                                                    </td>
                                                    <td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td>
                                                    <td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td>
                                                    <td width="15%"><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                    <td colspan="5">
                                                        <select class="border-0" name="eye[]">
                                                            <option value="frame">Frame</option>
                                                        </select>
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
                                                            <option value="1">Fitting</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('product_id[]', $frames->pluck('name', 'id'), old('product_id'))->class('border-0 select2 selPdct offerPdct')->attribute('data-batch', 'NA')->attribute('data-category', 'frame')->attribute('id', 'frame')->placeholder('Select')->required() }}
                                                    </td>
                                                    <td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td>
                                                    <td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td>
                                                    <td width="15%"><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td>
                                                </tr>
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
                                                        {{ html()->text('int_add', '')->class('w-100 border-0 text-center int_add')->maxlength(6)->placeholder('Int.Add') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('a_size', '')->class('w-100 border-0 text-center a_size')->maxlength(2)->placeholder('A Size') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('b_size', '')->class('w-100 border-0 text-center b_size')->maxlength(2)->placeholder('B Size') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('dbl', '')->class('w-100 border-0 text-center dbl')->maxlength(2)->placeholder('DBL') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('fh', '')->class('w-100 border-0 text-center fh')->maxlength(2)->placeholder('FH') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('ed', '')->class('w-100 border-0 text-center ed')->maxlength(2)->placeholder('ED') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('vd', '')->class('w-100 border-0 text-center vd')->maxlength(2)->placeholder('VD') }}
                                                    </td>
                                                    <td colspan="3" class="text-end fw-bold border-0">
                                                        Order Total
                                                        @error('order_total')
                                                        <br /><small class="text-danger">{{ $errors->first('order_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="text" name="order_total" class="text-end border-0 fw-bold w-100 subtotal readOnly" placeholder="0.00" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        {{ html()->text('w_angle', '')->class('w-100 border-0 text-center w_angle')->maxlength(6)->placeholder('W Angle') }}
                                                    </td>
                                                    <td colspan="6">
                                                        {{ html()->text('special_lab_note', '')->class('w-100 border-0 text-center')->placeholder('Special Lab Note') }}
                                                    </td>
                                                    <td colspan="3" class="text-end fw-bold border-0">Discount</td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='discount' class="w-100 border-0 text-end discount" placeholder="0.00" step="any" /></td>
                                                </tr>
                                                <tr>
                                                    <td>RPD</td>
                                                    <td>{{ html()->text('rpd', $spectacle?->rpd ?? '')->class('w-100 border-0 text-center fIpd')->maxlength(3)->placeholder('RPD') }}</td>
                                                    <td>LPD</td>
                                                    <td>{{ html()->text('lpd', $spectacle?->lpd ?? '')->class('w-100 border-0 text-center sIpd')->maxlength(3)->placeholder('LPD') }}</td>
                                                    <td colspan="7" class="text-end fw-bold border-0">
                                                        Total After Discount
                                                        @error('invoice_total')
                                                        <br /><small class="text-danger">{{ $errors->first('invoice_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="number" name="invoice_total" class="text-end border-0 fw-bold w-100 nettotal readOnly" min="1" step="any" placeholder="0.00" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold border-0">Royalty Card</td>
                                                    <td colspan="1">
                                                        {{ html()->select('card_type', $cards, old('card_type'))->class('border-0')->attribute('id', 'ctype')->placeholder('Card Type') }}
                                                    </td>
                                                    <td class="fw-bold border-0" colspan="2"><input type="text" name='card_number' class="w-100 border-0" placeholder="Card Number" value="{{ old('card_number') }}" /></td>
                                                    <td><button class="btn btn-primary" onclick="return validateRoyaltyCard();">Validate</button></td>
                                                    <td colspan="5" class="text-end">Royalty Card Discount</td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='royalty_discount' class="w-100 border-0 text-end royalty_discount" placeholder="0.00" step="any" value="0.00" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" class="text-end fw-bold border-0">Advance</td>
                                                    <td colspan="2">
                                                        {{ html()->select('payment_mode', $pmodes->pluck('name', 'id'), old('payment_mode'))->class('border-0')->attribute('id', 'pmode')->placeholder('Payment Mode') }}
                                                    </td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='advance' class="w-100 border-0 text-end advance" placeholder="0.00" step="any" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="fw-bold border-0"></td>
                                                    <td colspan="6" class="border-0"><input type="hidden" class="border-0 text-success fw-bold avCr" name="available_credit" value="{{ getAvailableCredit($patient?->id ?? 0) }}" readonly /></td>
                                                    <td colspan="2" class="text-end fw-bold border-0">Credit Used</td>
                                                    <td class="border-0">
                                                        {{ html()->number('credit_used', '', '', '', $step = 'any')->class('text-end border-0 fw-bold w-100 credit_used')->if(getAvailableCredit($patient?->id ?? 0) <= 0, function($el){
                                                            return $el->attribute('readonly', 'true');
                                                        })->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="10" class="fw-bold border-0 form-check_1">
                                                        <label class="form-check-label">Generate Invoice</label>
                                                        {{ html()->checkbox('generate_invoice', false, 1)->class('form-check-input')->attribute('id', 'form-check_1') }}
                                                    </td>
                                                    <td class="text-end fw-bold border-0">Balance</td>
                                                    <td class="border-0"><input type="text" name="balance" class="text-end border-0 fw-bold w-100 balance readOnly" placeholder="0.00" /></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                        <button class="btn btn-submit btn-success" onclick="return validateOrderForm()" type="submit">Save</button>
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