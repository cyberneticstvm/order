<div class="drawer drawer-right slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="paymentDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document">
        <div class="drawer-header">
            <h6 class="drawer-title" id="drawer-3-title">Generate Payment QR Code</h6>
        </div>
        <div class="drawer-body table-responsive">
            <div class="row">
                <form class="g-3" method="post" action="{{ route('email.docs') }}">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id" value="" />
                    <div class="col-md-12">
                        <label class="form-label req">Mobile Number</label>
                        {{ html()->text($name = 'mobile', null)->class('form-control')->maxlength(10)->placeholder('Mobile')->required() }}
                        @error('mobile')
                        <small class="text-danger">{{ $errors->first('mobile') }}</small>
                        @enderror
                    </div>
                    <div class="col-12 text-end mt-3">
                        <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                        <button class="btn btn-submit btn-success" type="submit">Generate</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="drawer-footer">Generate Payment QR Code</div>
    </div>
</div>