<div class="drawer drawer-left slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="waDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document">
        <div class="drawer-header">
            <h6 class="drawer-title" id="drawer-3-title">WhatsApp Documents - <span class="ono"></span></h6>
        </div>
        <div class="drawer-body table-responsive">
            <div class="row">
                <form class="g-3" method="post" action="{{ route('wa.docs') }}">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id" value="" />
                    <div class="col-md-12">
                        <label class="form-label req">Mobile Number</label>
                        {{ html()->text($name = 'mobile', $value = old('mobile'))->class('form-control waMobile')->placeholder('Mobile Number')->maxlength(10)->required() }}
                        @error('mobile')
                        <small class="text-danger">{{ $errors->first('mobile') }}</small>
                        @enderror
                    </div>
                    <div class="col-md-12 mt-3">Documents</div>
                    <div class="col-md-12 mt-3 ord">
                        {{ html()->checkbox($name = 'invoice', $checked=false, '1')->class('form-check-input')->attribute('id', 'invoice') }}
                        <label class="form-check-label">Bill / Invoice</label>
                    </div>
                    <div class="col-md-12 ord">
                        {{ html()->checkbox($name = 'receipt', $checked=false, '1')->class('form-check-input')->attribute('id', 'receipt') }}
                        <label class="form-check-label">Order</label>
                    </div>
                    <div class="col-md-12 presc">
                        {{ html()->checkbox($name = 'prescription', $checked=false, '1')->class('form-check-input')->attribute('id', 'prescription') }}
                        <label class="form-check-label">Spectacle Prescription</label>
                    </div>
                    <div class="col-12 text-end mt-3">
                        <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                        <button class="btn btn-submit btn-success" type="submit">Send</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="drawer-footer">WhatsApp Documents</div>
    </div>
</div>