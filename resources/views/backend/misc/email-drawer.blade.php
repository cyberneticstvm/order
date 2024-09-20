<div class="drawer drawer-right slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="emailDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document">
        <div class="drawer-header">
            <h6 class="drawer-title" id="drawer-3-title">Email Documents - <span class="ono"></span></h6>
        </div>
        <div class="drawer-body table-responsive">
            <div class="row">
                <form class="g-3" method="post" action="{{ route('email.docs') }}">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id" value="" />
                    <div class="col-md-12">
                        <label class="form-label req">E-mail</label>
                        {{ html()->email($name = 'email', $value = old('email'))->class('form-control')->placeholder('E-mail')->required() }}
                        @error('email')
                        <small class="text-danger">{{ $errors->first('email') }}</small>
                        @enderror
                    </div>
                    <div class="col-md-12 mt-3">
                        <label class="form-label">Body Text</label>
                        {{ html()->textarea($name = 'body', $value = 'Please see the attached document(s) for your reference')->class('form-control')->rows('5')->placeholder('Body Text') }}
                        @error('body')
                        <small class="text-danger">{{ $errors->first('body') }}</small>
                        @enderror
                    </div>
                    <div class="col-md-12 mt-3">Attachments</div>
                    <div class="col-md-12 mt-3">
                        {{ html()->checkbox($name = 'invoice', $checked=false, '1')->class('form-check-input')->attribute('id', 'invoice') }}
                        <label class="form-check-label">Bill / Invoice</label>
                    </div>
                    <div class="col-md-12">
                        {{ html()->checkbox($name = 'receipt', $checked=false, '1')->class('form-check-input')->attribute('id', 'receipt') }}
                        <label class="form-check-label">Order</label>
                    </div>
                    <div class="col-md-12">
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
        <div class="drawer-footer">Email Documents</div>
    </div>
</div>