@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Purchase Order</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Purchase Order</li>
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
                                <h5>Create Purchase Order</h5><span>Create New Purchase Order</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('po.save') }}" name="poForm" id="poForm">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label req">To</label>
                                    {{ html()->textarea($name = 'to', old('to'))->class('form-control')->placeholder('To')->required() }}
                                    @error('to')
                                    <small class="text-danger">{{ $errors->first('to') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label req">For</label>
                                    {{ html()->textarea($name = 'for', old('for'))->class('form-control')->placeholder('For')->required() }}
                                    @error('for')
                                    <small class="text-danger">{{ $errors->first('for') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Category</label>
                                    {{ html()->select('customer', array('hospital' => 'Hospital', 'store' => 'Store', 'lab' => 'Lab', 'other' => 'Other'), old('customer'))->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('customer')
                                    <small class="text-danger">{{ $errors->first('customer') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Purchase Order Date</label>
                                    {{ html()->date($name = 'date', $value = date('Y-m-d'), old('date'))->class('form-control')->required() }}
                                    @error('date')
                                    <small class="text-danger">{{ $errors->first('date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Purchase Order Number</label>
                                    {{ html()->text($name = 'po_number', old('po_number'))->class('form-control')->placeholder('Purchase Order Number') }}
                                    @error('po_number')
                                    <small class="text-danger">{{ $errors->first('po_number') }}</small>
                                    @enderror
                                </div>

                                <div class="row g-4 table-responsive">
                                    <div class="col-12">
                                        <h5 class="text-center text-secondary">Purchase Order Details - <a href="javascript:void(0)" onclick="addPurchaseOrderRow();">Add New Item</a></h5>
                                        <table class="table table-bordered table-stripped mt-3">
                                            <thead class="text-center">
                                                <tr>
                                                    <th width="10%">Remove</th>
                                                    <th width="50%">Product</th>
                                                    <th width="5%">Qty</th>
                                                    <th width="10%">Rate</th>
                                                    <th width="5%">Tax%</th>
                                                    <th width="10%">Tax Amount</th>
                                                    <th width="10%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="poTbl">
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                    <td>
                                                        {{ html()->text('products[]')->class('w-100 border-0 text-start')->placeholder('Product')->required() }}
                                                        @error('products')
                                                        <small class="text-danger">{{ $errors->first('products') }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        {{ html()->number('qty[]', '1', '', '', '')->class('w-100 border-0 text-end')->placeholder('Qty')->required() }}
                                                    </td>
                                                    <td>
                                                        {{ html()->number('rate[]', '', '', '', '')->class('w-100 border-0 text-end')->placeholder('0.00') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->number('tax_percentage[]', '', '', '', '')->class('w-100 border-0 text-end')->placeholder('0%') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->number('tax_amount[]', '', '', '', '')->class('w-100 border-0 text-end')->placeholder('0.00') }}
                                                    </td>
                                                    <td>
                                                        {{ html()->number('total[]', '', '', '', '')->class('w-100 border-0 text-end')->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="6" class="text-end fw-bold border-0">
                                                        Sub Total
                                                    </td>
                                                    <td>
                                                        {{ html()->number('grand_total', old('sub_total'), '', '', '')->class('text-end border-0 w-100')->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="text-end fw-bold border-0">
                                                        Shipping & Delivery
                                                    </td>
                                                    <td>
                                                        {{ html()->number('shipping', old('shipping'), '', '', '')->class('text-end border-0 w-100')->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">
                                                        {{ html()->text('additional_expense_remarks', old('additional_expense_remarks'))->class('w-100 border-0 text-start')->placeholder('Addional Expenses Remarks') }}
                                                    </td>
                                                    <td colspan="2" class="text-end fw-bold border-0">Addional Expenses</td>
                                                    <td class="text-end fw-bold border-0">
                                                        {{ html()->number('additional_expense', old('additional_expense'), '', '', '')->class('text-end border-0 w-100')->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">
                                                        {{ html()->text('discount_remarks', old('discount_remarks'))->class('w-100 border-0 text-start')->placeholder('Discount Remarks') }}
                                                    </td>
                                                    <td colspan="2" class="text-end fw-bold border-0">Discount</td>
                                                    <td class="text-end fw-bold border-0">
                                                        {{ html()->number('discount', old('discount'), '', '', '')->class('text-end border-0 w-100')->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bold border-0">Advance</td>
                                                    <td colspan="2">
                                                        {{ html()->select('advance_pmode', $pmodes, old('advance_pmode'))->class('border-0')->attribute('id', 'pmode')->placeholder('Payment Mode') }}
                                                    </td>
                                                    <td class="text-end fw-bold border-0">
                                                        {{ html()->number('advance', old('advance'), '', '', '')->class('w-100 border-0 text-end')->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="text-end fw-bold border-0">Balance</td>
                                                    <td class="border-0">
                                                        {{ html()->number('balance', old('balance'), '', '', '')->class('text-end border-0 fw-bold w-100')->placeholder('0.00') }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label req">Terms & Conditions</label>
                                        {{ html()->textarea($name = 'terms', old('terms'))->rows(5)->class('form-control')->placeholder('Terms & Conditions')->required() }}
                                        @error('terms')
                                        <small class="text-danger">{{ $errors->first('terms') }}</small>
                                        @enderror
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