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
                        <h5>Update Order</h5><span>Update Order</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('store.order.update', $order->id) }}" name="orderForm">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">Order Date</label>
                                    {{ html()->date($name = 'order_date', $value = $order->order_date?->format('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('order_date')
                                    <small class="text-danger">{{ $errors->first('order_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Name</label>
                                    {{ html()->text($name = 'name', $value = $order->name)->class('form-control')->placeholder('Customer Name')->required() }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Age</label>
                                    {{ html()->number($name = 'age', $value = $order->age)->class('form-control')->placeholder('0')->required() }}
                                    @error('age')
                                    <small class="text-danger">{{ $errors->first('age') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Place</label>
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
                                <div class="col-md-5">
                                    <label class="form-label">Order Note</label>
                                    {{ html()->text($name = 'order_note', $value = $order->order_note)->class('form-control')->placeholder('Order Note') }}
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
                                                    <th>THICK</th>
                                                    <th width="6%">IPD</th>
                                                    <th width="30%">Product</th>
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
                                                    <td><input type="text" name='sph[]' class="w-100 border-0 text-center" placeholder="SPH" value="{{ $item->sph ?? '' }}" maxlength="6" /></td>
                                                    <td><input type="text" name='cyl[]' class="w-100 border-0 text-center" placeholder="CYL" value="{{ $item->cyl ?? '' }}" maxlength="6" /></td>
                                                    <td><input type="number" name='axis[]' class="w-100 border-0 text-center" placeholder="AXIS" value="{{ $item->axis ?? '' }}" step="any" max="360" /></td>
                                                    <td><input type="text" name='add[]' class="w-100 border-0 text-center" placeholder="ADD" value="{{ $item->add ?? '' }}" maxlength="6" /></td>
                                                    <td><input type="text" name='dia[]' class="w-100 border-0 text-center" placeholder="DIA" value="{{ $item->dia ?? '' }}" maxlength="6" /></td>
                                                    <td>
                                                        <select class="border-0" name="thickness[]">
                                                            <option value="not-applicable" {{ ($item->thickness == 'not-applicable') ? 'selected' : '' }}>Not applicable</option>
                                                            <option value="thin" {{ ($item->thickness == 'thin') ? 'selected' : '' }}>Thin</option>
                                                            <option value="maximum-thin" {{ ($item->thickness == 'maximum-thin') ? 'selected' : '' }}>Maximum Thin</option>
                                                            <option value="normal-thick" {{ ($item->thickness == 'normal-thick') ? 'selected' : '' }}>Normal Thick</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name='ipd[]' class="w-100 border-0 text-center" placeholder="IPD" value="{{ $item->ipd ?? '' }}" maxlength="6" /></td>
                                                    @endif
                                                    @if(in_array($item->eye, ['frame', 'service']))
                                                    <td colspan="7">
                                                        <div class="d-none">
                                                            <input type="hidden" name="sph[]" />
                                                            <input type="hidden" name="cyl[]" />
                                                            <input type="hidden" name="axis[]" />
                                                            <input type="hidden" name="add[]" />
                                                            <input type="hidden" name="dia[]" />
                                                            <input type="hidden" name="ipd[]" />
                                                            <input type="hidden" name="thickness[]" />
                                                        </div>
                                                    </td>
                                                    @endif
                                                    <td>
                                                        {{ html()->select('product_id[]', $products->when(in_array($item->eye, ['re', 'le', 'both']), function($q){
                                                            return $q->where('category', 'lens');
                                                        })->when(in_array($item->eye, ['frame']), function($q){
                                                            return $q->where('category', 'frame');
                                                        })->when(in_array($item->eye, ['service']), function($q){
                                                            return $q->where('category', 'service');
                                                        })->pluck('name', 'id'), $item->product_id)->class('border-0 select2 selPdct')->attribute('id', $item->id)->placeholder('Select')->required() }}
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
                                                    <td colspan="12" class="text-end fw-bold border-0">
                                                        Order Total
                                                        @error('order_total')
                                                        <br /><small class="text-danger">{{ $errors->first('order_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="text" name="order_total" class="text-end border-0 fw-bold w-100 subtotal readOnly" placeholder="0.00" value="{{ $order->order_total }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12" class="text-end fw-bold border-0">Discount</td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='discount' class="w-100 border-0 text-end discount" placeholder="0.00" step="any" value="{{ $order->discount }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12" class="text-end fw-bold border-0">
                                                        Total After Discount
                                                        @error('invoice_total')
                                                        <br /><small class="text-danger">{{ $errors->first('invoice_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="number" name="invoice_total" class="text-end border-0 fw-bold w-100 nettotal readOnly" min="1" step="any" placeholder="0.00" value="{{ $order->invoice_total }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="11" class="text-end fw-bold border-0">Advance</td>
                                                    <td>
                                                        {{ html()->select('payment_mode', $pmodes->pluck('name', 'id'), $order->payment?->payment_mode)->class('border-0')->attribute('id', 'pmode')->placeholder('Payment Mode') }}
                                                    </td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='advance' class="w-100 border-0 text-end advance" placeholder="0.00" step="any" value="{{ $order->advance }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12" class="text-end fw-bold border-0">Balance</td>
                                                    <td class="border-0"><input type="text" name="balance" class="text-end border-0 fw-bold w-100 balance readOnly" placeholder="0.00" value="{{ $order->balance }}" /></td>
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