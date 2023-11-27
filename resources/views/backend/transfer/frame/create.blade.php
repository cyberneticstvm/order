@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Transfer - Frames</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Frames</li>
                        <li class="breadcrumb-item active">Transfer</li>
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
                        <h5>Transfer Frames</h5><span>Transfer Frames</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('frame.transfer.save') }}" id="transferForm">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">Transfer Date</label>
                                    {{ html()->date($name = 'transfer_date', $value = date('Y-m-d'))->class('form-control')->placeholder('Transfer Date')->required() }}
                                    @error('transfer_date')
                                    <small class="text-danger">{{ $errors->first('transfer_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label req">From Branch</label>
                                    {{ html()->select('from_branch_id', array('0' => 'Main Stock') + $branches->pluck('name', 'id')->toArray(), old('from_branch_id'))->class('form-control select2 from_branch_id')->attribute('id', 'from_branch_id')->placeholder('Select')->required() }}
                                    @error('from_branch_id')
                                    <small class="text-danger">{{ $errors->first('from_branch_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label req">To Branch</label>
                                    {{ html()->select('to_branch_id', $branches->pluck('name', 'id'), old('to_branch_id'))->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('to_branch_id')
                                    <small class="text-danger">{{ $errors->first('to_branch_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Transfer Note</label>
                                    {{ html()->textarea($name = 'transfer_note', $value = old('transfer_note'))->class('form-control')->rows('3')->placeholder('Transfer Note') }}
                                </div>
                                <div class="row g-4 table-responsive">
                                    <div class="col text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-primary" type="button" onclick="addTransferRow('frame')">Add New Row</button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h5 class="text-center text-secondary">Transfer Details</h5>
                                        <table class="table table-bordered table-sm table-stripped mt-3">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Remove</th>
                                                    <th width="60%">Product</th>
                                                    <th width="20%">Qty Available</th>
                                                    <th width="10%">Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tblPharmacyTransferBody">
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                    <td>
                                                        {{ html()->select('product_id[]', $products, old('product_id'))->class('border-0 select2 selPdctForTransfer')->attribute('data-category', 'frame')->attribute('id', '')->placeholder('Select')->required() }}
                                                    </td>
                                                    <td class='qtyAvailable text-end'>0</td>
                                                    <td><input type="number" name='qty[]' class="w-100 border-0 qtyMax text-end" placeholder="0" min='1' step="1" required /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                        <button class="btn btn-submit btn-success" onclick="return validateTransferForm()" type="submit">Save</button>
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