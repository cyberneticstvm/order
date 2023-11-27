@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Purchase - Frame</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Frame</li>
                        <li class="breadcrumb-item active">Purchase</li>
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
                        <h5>Purchase Frame</h5><span>Purchase Frame</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('frame.purchase.save') }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">Order Date</label>
                                    {{ html()->date($name = 'order_date', $value = date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('order_date')
                                    <small class="text-danger">{{ $errors->first('order_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Delivery Date</label>
                                    {{ html()->date($name = 'delivery_date', $value = date('Y-m-d'))->class('form-control')->placeholder('Delivery Date')->required() }}
                                    @error('delivery_date')
                                    <small class="text-danger">{{ $errors->first('delivery_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label req">Supplier</label>
                                    {{ html()->select('supplier_id', $suppliers, old('supplier_id'))->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('supplier_id')
                                    <small class="text-danger">{{ $errors->first('supplier_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Purchase Invoice Number</label>
                                    {{ html()->text($name = 'purchase_invoice_number', $value = old('purchase_invoice_number'))->class('form-control')->placeholder('Purchase Invoice Number')->required() }}
                                    @error('purchase_invoice_number')
                                    <small class="text-danger">{{ $errors->first('purchase_invoice_number') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Purchase Note</label>
                                    {{ html()->textarea($name = 'purchase_note', $value = old('purchase_note'))->class('form-control')->rows('3')->placeholder('Purchase Note') }}
                                </div>
                                <div class="row g-4 table-responsive">
                                    <div class="col text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-primary" type="button" onclick="addPurchaseRowFrame('frame')">Add New Row</button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h5 class="text-center text-secondary">Purchase Details</h5>
                                        <table class="table table-bordered table-stripped mt-3">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Remove</th>
                                                    <th width="50%">Product</th>
                                                    <th width="5%">Qty</th>
                                                    <th width="10%">MRP</th>
                                                    <th width="10%">Purchase Price</th>
                                                    <th width="10%">Selling Price</th>
                                                    <th width="10%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tblPharmacyPurchaseBody">
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('product_id[]', $products, old('product_id'))->class('border-0 select2 selPdct')->attribute('id', '')->placeholder('Select')->required() }}
                                                    </td>
                                                    <td><input type="number" name='qty[]' class="w-100 border-0 text-end pQty" placeholder="0" min='1' step="1" required /></td>
                                                    <td><input type="number" name='mrp[]' class="w-100 border-0 text-end pMrp" placeholder="0.00" step="any" required /></td>
                                                    <td><input type="number" name='purchase_price[]' class="w-100 border-0 text-end pPPrice" placeholder="0.00" step="any" required /></td>
                                                    <td><input type="number" name='selling_price[]' class="w-100 border-0 text-end pSPrice" placeholder="0.00" step="any" required /></td>
                                                    <td><input type="number" name='total[]' class="w-100 border-0 text-end readOnly pTotal" placeholder="0.00" step="any" /></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold border-0">Total</td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 qtyTot readOnly" placeholder="0" /></td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 mrpTot readOnly" placeholder="0.00" /></td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 ppriceTot readOnly" placeholder="0.00" /></td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 spriceTot readOnly" placeholder="0.00" /></td>
                                                    <td class="border-0"><input type="text" class="text-end border-0 fw-bold w-100 tTot readOnly" placeholder="0.00" /></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                        <button class="btn btn-submit btn-success" type="submit">Save</button>
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