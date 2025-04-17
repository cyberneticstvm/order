<div class="drawer drawer-left slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="waPromotionDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document">
        <div class="drawer-header">
            <h6 class="drawer-title" id="drawer-3-title">WhatsApp Promotion <span class="ono"></span></h6>
        </div>
        <div class="drawer-body table-responsive">
            <div class="row">
                <form class="g-3" method="post" action="{{ route('wa.send.promotion') }}">
                    @csrf
                    <input type="hidden" name="schedule_id" id="schedule_id" class="schedId" value="" />
                    <div class="col-md-12">
                        <label class="form-label req">Customer Name</label>
                        {{ html()->text($name = 'name', $value = old('name'))->class('form-control')->placeholder('Customer Name')->required() }}
                        @error('name')
                        <small class="text-danger">{{ $errors->first('name') }}</small>
                        @enderror
                    </div>
                    <div class="col-md-12 mt-3">
                        <label class="form-label req">Mobile Number</label>
                        {{ html()->text($name = 'mobile', $value = old('mobile'))->class('form-control')->placeholder('Mobile Number')->maxlength(10)->required() }}
                        @error('mobile')
                        <small class="text-danger">{{ $errors->first('mobile') }}</small>
                        @enderror
                    </div>
                    <div class="col-12 text-end mt-3">
                        <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                        <button class="btn btn-submit btn-success" type="submit">Send</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="drawer-footer">WhatsApp Promotion</div>
    </div>
</div>