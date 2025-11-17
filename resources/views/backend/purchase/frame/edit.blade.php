@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Purchase Update - Frame</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Frame</li>
                        <li class="breadcrumb-item active">Purchase Update</li>
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
                        <h5>Purchase Frame Update</h5><span>Purchase Frame Update</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('frame.purchase.update', $purchase->id) }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">Order Date</label>
                                    {{ html()->date($name = 'order_date', $value = $purchase->order_date?->format('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('order_date')
                                    <small class="text-danger">{{ $errors->first('order_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Delivery Date</label>
                                    {{ html()->date($name = 'delivery_date', $value = $purchase->delivery_date?->format('Y-m-d'))->class('form-control')->placeholder('Delivery Date')->required() }}
                                    @error('delivery_date')
                                    <small class="text-danger">{{ $errors->first('delivery_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch</label>
                                    {{ html()->select('branch_id', $branches, $purchase->branch_id)->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('branch_id')
                                    <small class="text-danger">{{ $errors->first('branch_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Supplier</label>
                                    {{ html()->select('supplier_id', $suppliers, $purchase->supplier_id)->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('supplier_id')
                                    <small class="text-danger">{{ $errors->first('supplier_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Purchase Invoice Number</label>
                                    {{ html()->text($name = 'purchase_invoice_number', $value = $purchase->purchase_invoice_number)->class('form-control')->placeholder('Purchase Invoice Number')->required() }}
                                    @error('purchase_invoice_number')
                                    <small class="text-danger">{{ $errors->first('purchase_invoice_number') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Purchase Note</label>
                                    {{ html()->textarea($name = 'purchase_note', $value = $purchase->purchase_note)->class('form-control')->rows('3')->placeholder('Purchase Note') }}
                                </div>
                                <div class="row g-4 table-responsive">
                                    <div class="col text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-primary" type="button" onclick="addPurchaseRowFrame('frame', 'purchase')">Add New Row</button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h5 class="text-center text-secondary">Purchase Details</h5>
                                        <table class="table table-bordered table-stripped mt-3">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Remove</th>
                                                    <th width="40%">Product</th>
                                                    <th width="5%">Qty</th>
                                                    <th width="10%">Purchase Price</th>
                                                    <th width="10%">Item Tot</th>
                                                    <th width="10%">Discount</th>
                                                    <th width="10%">Tax Amount</th>
                                                    <th width="10%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tblPharmacyPurchaseBody">
                                                @forelse($purchase->detail as $key => $item)
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('product_id[]', $products, $item->product_id)->class('border-0 select2 selPdct')->attribute('id', '')->placeholder('Select')->required() }}
                                                    </td>
                                                    <td><input type="number" name='qty[]' class="w-100 border-0 text-end pQty" placeholder="0" value="{{ $item->qty }}" min='1' step="1" required /></td>
                                                    <td><input type="number" name='purchase_price[]' class="w-100 border-0 text-end pPPrice" value="{{ $item->unit_price_purchase }}" placeholder="0.00" step="any" required /></td>
                                                    <td><input type="number" name='item_tot[]' class="w-100 border-0 text-end itemTot" value="{{ $item->qty * $item->unit_price_purchase }}" placeholder="0.00" step="any" required /></td>
                                                    <td><input type="number" name='discount[]' class="w-100 border-0 text-end discount" placeholder="0.00" value="{{ $item->discount }}" step="any" required /></td>
                                                    <td><input type="number" name='tax_amount[]' value="{{ $item->tax_amount }}" class="w-100 border-0 text-end" placeholder="0.00" step="any" required /></td>
                                                    <td><input type="number" name='total[]' class="w-100 border-0 text-end readOnly pTotal" value="{{ $item->total }}" placeholder="0.00" step="any" /></td>
                                                </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <!--<tr>
                                                    <td colspan="2" class="text-end fw-bold border-0">Total</td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 qtyTot readOnly" value="{{ $purchase->detail?->sum('qty') }}" placeholder="0" /></td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 ppriceTot readOnly" value="{{ number_format($purchase->detail?->sum('unit_price_purchase'), 2) }}" placeholder="0.00" /></td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 spriceTot readOnly" value="{{ number_format($purchase->detail?->sum('unit_price_sales'), 2) }}" placeholder="0.00" /></td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 mrpTot readOnly" value="{{ number_format($purchase->detail?->sum('unit_price_mrp'), 2) }}" placeholder="0.00" /></td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 discountTot readOnly" value="{{ number_format($purchase->detail?->sum('discount'), 2) }}" placeholder="0.00" /></td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 tTot readOnly" value="{{ number_format($purchase->detail?->sum('total'), 2) }}" placeholder="0.00" /></td>
                                                </tr>-->
                                                <tr>
                                                    <td colspan="7" class="fw-bold text-end">Grand Total</td>
                                                    <td class="fw-bold text-end">{{ number_format($purchase->detail->sum('total'), 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="text-end fw-bold border-0">Other Charges</td>
                                                    <td colspan="2">
                                                        <input type="text" class="form-control" name="other_charges_desc" value="{{ $purchase->other_charges_desc }}" placeholder="Description" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="text-end border-0 fw-bold w-100 otherCharges" name="other_charges" value="{{ $purchase->other_charges }}" placeholder="0.00" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bold border-0">Adjustment</td>
                                                    <td colspan="2">
                                                        <input type="text" class="form-control" name="adjust_desc" value="{{ $purchase->adjust_desc }}" placeholder="Description" />
                                                    </td>
                                                    <td>
                                                        <select class="form-control" name="adjust_type">
                                                            <option value="">Select</option>
                                                            <option value="plus" {{ ($purchase->adjust_type == 'plus') ? 'selected' : '' }}>+</option>
                                                            <option value="minus" {{ ($purchase->adjust_type == 'minus') ? 'selected' : '' }}>-</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="text-end border-0 fw-bold w-100 adjustmentAmount" value="{{ $purchase->adjust_amount }}" name="adjust_amount" placeholder="0.00" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" class="fw-bold text-end">Total</td>
                                                    <td class="fw-bold text-end">{{ number_format($purchase->detail->sum('total') + $purchase->other_charges, 2) $sign $purchase->adjust_amount }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                        <button class="btn btn-submit btn-success" type="submit">Update</button>
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