@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Update Order</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Update</li>
                        <li class="breadcrumb-item active">Order</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 file-content">
                <div class="card">
                    <div class="card-header">
                        <h5>Update Order</h5><span>Confirm Details</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper row g-4">
                            <div class="col-md-3">
                                <label class="form-label">Order Number</label>
                                {{ html()->text($name = 'ino', $value = $order->ono())->class('form-control')->attribute('disabled') }}
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Invoice Number</label>
                                {{ html()->text($name = 'pname', $value = $order->ino())->class('form-control')->attribute('disabled') }}
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Customer Name</label>
                                {{ html()->text($name = 'mobile', $value = $order->name)->class('form-control')->attribute('disabled') }}
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mobile Number</label>
                                {{ html()->text($name = 'place', $value = $order->mobile)->class('form-control')->attribute('disabled') }}
                            </div>
                            <div class="col-12 text-center">
                                <a class="btn btn-success" type="submit" href="{{ route('edit.dispatched.order.get', encrypt($order->id)) }}">Proceed</a>
                                <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
@endsection