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
                        <h5>Create Order</h5><span>Create New Order</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('store.order.save') }}" name="orderForm">
                                @csrf
                                <input type="hidden" name="consultation_id" value="{{ $mrecord?->id ?? 0 }}" />
                                <div class="col-md-2">
                                    <label class="form-label req">Order Date</label>
                                    {{ html()->date($name = 'order_date', $value = date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('order_date')
                                    <small class="text-danger">{{ $errors->first('order_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Name</label>
                                    {{ html()->text($name = 'name', $value = $patient?->patient_name ?? old('name'))->class('form-control')->placeholder('Customer Name')->required() }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Age</label>
                                    {{ html()->number($name = 'age', $value = $patient?->age ?? old('age'))->class('form-control')->placeholder('0')->required() }}
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
                                    {{ html()->text($name = 'mobile', $value = $patient?->mobile_number ?? old('mobile'))->class('form-control')->maxlength('10')->placeholder('Mobile')->required() }}
                                    @error('mobile')
                                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                                    @enderror
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
                                <div class="col-md-5">
                                    <label class="form-label">Order Note</label>
                                    {{ html()->text($name = 'order_note', $value = old('order_note'))->class('form-control')->placeholder('Order Note') }}
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
                                <div class="row g-4 table-responsive">
                                    <div class="col text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add New Row</button>
                                            <ul class="dropdown-menu dropdown-block">
                                                <li><a class="dropdown-item txt-dark" href="javascript:void(0)" onclick="addStoreOrderRow('lens')"><i class="fa fa-eye txt-danger fw-bold"></i> Lens</a></li>
                                                <li><a class="dropdown-item txt-dark" href="javascript:void(0)" onclick="addStoreOrderRow('frame')"><i class="fa fa-square-o txt-success fw-bold"></i> Frame</a></li>
                                                <li><a class="dropdown-item txt-dark" href="javascript:void(0)" onclick="addStoreOrderRow('service')"><i class="icofont icofont-businesswoman txt-muted fw-bold"></i> Service</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h5 class="text-center text-secondary">Order Details</h5>
                                        <table class="table table-bordered table-stripped mt-3">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Remove</th>
                                                    <th>Eye</th>
                                                    <th width="6%">SPH</th>
                                                    <th width="6%">CYL</th>
                                                    <th width="6%">AXIS</th>
                                                    <th width="6%">ADD</th>
                                                    <th width="6%">DIA</th>
                                                    <th>INT.ADD</th>
                                                    <th width="6%">IPD</th>
                                                    <th width="50%">Product</th>
                                                    <th width="5%">Qty</th>
                                                    <th>Price</th>
                                                    <th width="15%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="powerbox">
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                    <td>
                                                        <select class="border-0" name="eye[]">
                                                            <option value="re">RE</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('sph[]', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->re_dist_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('cyl[]', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->re_dist_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('axis[]', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->re_dist_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('add[]', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->re_dist_add ?? $powers?->where('name', 'add')->where('default', 'true')?->first()?->value)->class('border-0 select2') }}
                                                    </td>
                                                    <td><input type="text" name='dia[]' class="w-100 border-0 text-center" placeholder="DIA" maxlength="6" /></td>
                                                    <td>
                                                        <input type="text" name='int_add[]' class="w-100 border-0 text-center" placeholder="0.00" value="{{ $spectacle?->re_int_add ?? '' }}" maxlength="6" />
                                                    </td>
                                                    <td><input type="text" name='ipd[]' class="w-100 border-0 text-center" placeholder="IPD" maxlength="6" /></td>
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
                                                    </td>
                                                    <td>
                                                        <select class="border-0" name="eye[]">
                                                            <option value="le">LE</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('sph[]', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->le_dist_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'sph1') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('cyl[]', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->le_dist_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'cyl1') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('axis[]', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->le_dist_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'axis1') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('add[]', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->le_dist_add ?? $powers?->where('name', 'add')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'add1') }}
                                                    </td>
                                                    <td><input type="text" name='dia[]' class="w-100 border-0 text-center" placeholder="DIA" maxlength="6" /></td>
                                                    <td>
                                                        <input type="text" name='int_add[]' class="w-100 border-0 text-center" value="{{ $spectacle?->le_int_add ?? '' }}" placeholder="0.00" maxlength="6" />
                                                    </td>
                                                    <td><input type="text" name='ipd[]' class="w-100 border-0 text-center" placeholder="IPD" maxlength="6" /></td>
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
                                                    <td colspan="8">
                                                        <select class="border-0" name="eye[]">
                                                            <option value="frame">Frame</option>
                                                        </select>
                                                        <div class="d-none">
                                                            <input type="hidden" name="sph[]" />
                                                            <input type="hidden" name="cyl[]" />
                                                            <input type="hidden" name="axis[]" />
                                                            <input type="hidden" name="add[]" />
                                                            <input type="hidden" name="dia[]" />
                                                            <input type="hidden" name="ipd[]" />
                                                            <input type="hidden" name="int_add[]" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('product_id[]', $products->where('category', 'frame')->pluck('name', 'id'), old('product_id'))->class('border-0 select2 selPdct')->attribute('data-batch', 'NA')->attribute('data-category', 'frame')->attribute('id', 'frame')->placeholder('Select')->required() }}
                                                    </td>
                                                    <td><input type="number" name='qty[]' class="w-100 border-0 text-end qty" placeholder="0" min='1' step="1" required /></td>
                                                    <td><input type="number" name='unit_price[]' class="w-100 border-0 text-end price" placeholder="0.00" min='1' step="any" required readonly /></td>
                                                    <td width="15%"><input type="number" name='total[]' class="w-100 border-0 text-end total" placeholder="0.00" min='1' step="any" required readonly /></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="12" class="text-end fw-bold border-0">
                                                        Order Total
                                                        @error('order_total')
                                                        <br /><small class="text-danger">{{ $errors->first('order_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="text" name="order_total" class="text-end border-0 fw-bold w-100 subtotal readOnly" placeholder="0.00" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12" class="text-end fw-bold border-0">Discount</td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='discount' class="w-100 border-0 text-end discount" placeholder="0.00" step="any" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12" class="text-end fw-bold border-0">
                                                        Total After Discount
                                                        @error('invoice_total')
                                                        <br /><small class="text-danger">{{ $errors->first('invoice_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="number" name="invoice_total" class="text-end border-0 fw-bold w-100 nettotal readOnly" min="1" step="any" placeholder="0.00" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="11" class="text-end fw-bold border-0">Advance</td>
                                                    <td>
                                                        {{ html()->select('payment_mode', $pmodes->pluck('name', 'id'), old('payment_mode'))->class('border-0')->attribute('id', 'pmode')->placeholder('Payment Mode') }}
                                                    </td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='advance' class="w-100 border-0 text-end advance" placeholder="0.00" step="any" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12" class="text-end fw-bold border-0">Balance</td>
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