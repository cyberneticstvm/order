@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Update Pharmacy Order</h3>
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
                        <h5>Update Order</h5><span>Update Pharmacy Order</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('pharmacy.order.update', $order->id) }}" name="orderForm">
                                @csrf
                                <input type="hidden" id="from_branch_id" value="{{ branch()->id }}" />
                                <div class="col-md-2">
                                    <label class="form-label req">Order Date</label>
                                    {{ html()->date($name = 'order_date', $value = $order->order_date->format('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('order_date')
                                    <small class="text-danger">{{ $errors->first('order_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Name</label>
                                    {{ html()->text($name = 'name', $value = $order->name ?? old('name'))->class('form-control')->placeholder('Customer Name')->required() }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Age</label>
                                    {{ html()->number($name = 'age', $value = $order->age ?? old('age'))->class('form-control')->placeholder('0')->required() }}
                                    @error('age')
                                    <small class="text-danger">{{ $errors->first('age') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Place</label>
                                    {{ html()->text($name = 'place', $value = $order->place ?? old('place'))->class('form-control')->placeholder('Place')->required() }}
                                    @error('place')
                                    <small class="text-danger">{{ $errors->first('place') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Mobile</label>
                                    {{ html()->text($name = 'mobile', $value = $order->mobile ?? old('mobile'))->class('form-control')->maxlength('10')->placeholder('Mobile')->required() }}
                                    @error('mobile')
                                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                                    @enderror
                                </div>
                                <div class="row g-4 table-responsive">
                                    <div class="col text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" type="button" onclick="addMedicineRowForOrder('pharmacy', 'type')">Add New Medicine</button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h5 class="text-center text-secondary">Medicine Details</h5>
                                        <table class="table table-bordered table-stripped mt-3">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Remove</th>
                                                    <th>Type</th>
                                                    <th width="25%">Medicine</th>
                                                    <th>Batch</th>
                                                    <th width="5%">Qty</th>
                                                    <th>Dosage</th>
                                                    <th>Duration</th>
                                                    <th>Eye</th>
                                                    <th>Price</th>
                                                    <th width="10%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="medicineBox">
                                                @forelse($order->details as $key => $item)
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('product_type[]', $types, $item->product_type)->class('select2 selPdctType')->attribute('id', '')->placeholder('Select') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('product_id[]', $products, $item->product_id)->class('select2 selPdct pdctForMed')->attribute('data-batch', 'NA')->attribute('data-category', 'pharmacy')->attribute('id', '')->placeholder('Select') }}
                                                    </td>
                                                    <td>
                                                        <select class="select2 selBatch" name="batch_number[]" data-category="pharmacy" id="" required>
                                                            <option value="">Select</option>
                                                            @forelse(getInventory(branch()->id, $item->product_id, 'pharmacy') as $key1 => $batch)
                                                            <option value="{{ $batch->batch_number }}" {{ $batch->batch_number == $item->batch_numer ? 'selected' : '' }}>{{ $batch->batch_number }}&nbsp;({{ $batch->balanceQty }} Qty Available)</option>
                                                            @empty
                                                            <option value="">No Records</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                    <td>
                                                        {{ html()->number('qty[]', $item->qty, '', '', 'any')->class('border-0 w-100 text-end qty')->attribute('id', '')->placeholder('0')}}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('dosage[]', $item->dosage)->class('dosage border-0 w-100')->attribute('id', '')->placeholder('Dosage')}}
                                                    </td>
                                                    <td>
                                                        {{ html()->text('duration[]', $item->duration)->class('duration border-0 w-100')->attribute('id', '')->placeholder('Duration')}}
                                                    </td>
                                                    <td>
                                                        {{ html()->select('eye[]', array('left' => 'Left', 'right' => 'Right', 'both' =>'Both'), $item->eye)->class('select2 selEye')->attribute('id', '')->placeholder('Select') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->number('unit_price[]', $item->unit_price, '1', '', 'any')->class('w-100 border-0 text-end price')->attribute('id', '')->placeholder('0.00')->attribute('readonly', 'true')->required() }}
                                                    </td>
                                                    <td>
                                                        {{ html()->number('total[]', $item->total, '1', '', 'any')->class('w-100 border-0 text-end total')->attribute('id', '')->placeholder('0.00')->attribute('readonly', 'true')->required() }}
                                                    </td>
                                                </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="9" class="text-end fw-bold border-0">
                                                        Order Total
                                                        @error('order_total')
                                                        <br /><small class="text-danger">{{ $errors->first('order_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="text" name="order_total" class="text-end border-0 fw-bold w-100 subtotal readOnly" placeholder="0.00" value="{{ $order->order_total }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" class="text-end fw-bold border-0">Discount</td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='discount' class="w-100 border-0 text-end discount" placeholder="0.00" step="any" value="{{ $order->discount }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" class="text-end fw-bold border-0">
                                                        Total After Discount
                                                        @error('invoice_total')
                                                        <br /><small class="text-danger">{{ $errors->first('invoice_total') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td class="border-0"><input type="number" name="invoice_total" class="text-end border-0 fw-bold w-100 nettotal readOnly" min="1" step="any" placeholder="0.00" value="{{ $order->invoice_total }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8" class="text-end fw-bold border-0">Advance</td>
                                                    <td>
                                                        {{ html()->select('payment_mode', $pmodes->pluck('name', 'id'), $order->payment?->payment_mode)->class('border-0')->attribute('id', 'pmode')->placeholder('Payment Mode') }}
                                                    </td>
                                                    <td class="text-end fw-bold border-0"><input type="number" name='advance' class="w-100 border-0 text-end advance" placeholder="0.00" step="any" value="{{ $order->advance }}" /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" class="text-end fw-bold border-0">Balance</td>
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