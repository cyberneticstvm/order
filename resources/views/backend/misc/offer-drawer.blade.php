<div class="drawer drawer-right slide" role="dialog" tabindex="-1" aria-labelledby="drawer-3-title" aria-hidden="true" id="offerDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document">
        <div class="drawer-header">
            <h6 class="drawer-title" id="drawer-3-title">Offer - <span class="offerid"></span></h6>
        </div>
        <div class="drawer-body table-responsive">
            <div class="row">
                <form class="g-3" method="post" action="">
                    @csrf
                    <input type="hidden" name="offer_id" id="offer_id" value="" />
                    <div class="col-md-12">
                        <label class="form-label req">Select Product</label>
                        {{ html()->select($name = 'product', array('0' => 'Select Product'), $value = old('product'))->class('form-control selOfferPdct') }}
                        @error('product')
                        <small class="text-danger">{{ $errors->first('product') }}</small>
                        @enderror
                    </div>
                    <div class="col-12 text-end mt-3">
                        <button class="btn btn-secondary" onClick="$('#offerDrawer').drawer('toggle');" type="button">Cancel</button>
                        <button class="btn btn-submit btn-success btnAddOfferProduct" type="button">Add</button>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-12 mt-3 mb-3">
                    <h5>Existing Products</h5>
                </div>
                <div class="col-12 table-responsive tblContent">

                </div>
            </div>
        </div>
        <div class="drawer-footer">Offerred Products</div>
    </div>
</div>