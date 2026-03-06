<div class="drawer drawer-left slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="prDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document">
        <div class="drawer-header">
            <h6 class="drawer-title" id="drawer-3-title">Purchase Return</h6>
        </div>
        <div class="drawer-body table-responsive">
            <div class="row">
                <form class="g-3" method="post" action="{{ route('purchase.return.save') }}">
                    @csrf
                    <div class="col-12 table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL No.</th>
                                    <th>Supplier</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Ret. Qty</th>
                                    <th>Price/Qty</th>
                                </tr>
                            </thead>
                            <tbody class="output">

                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 text-end mt-3">
                        <button class="btn btn-secondary" onClick="$('#prDrawer').drawer('toggle');" type="button">Cancel</button>
                        <button class="btn btn-submit btn-success btnAddOfferProduct" type="button">Save</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="drawer-footer">Purchase Return</div>
    </div>
</div>