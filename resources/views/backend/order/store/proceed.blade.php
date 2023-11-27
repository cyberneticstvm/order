@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Order</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Order</li>
                        <li class="breadcrumb-item active">Create</li>
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
                        <h5>Create Order</h5><span>Confirm Details</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper row g-4">
                            <div class="col-md-4">
                                <label class="form-label">Patient Name</label>
                                {{ html()->text($name = 'pname', $value = $consultation->patient->name)->class('form-control')->attribute('disabled') }}
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Patient ID</label>
                                {{ html()->text($name = 'pid', $value = $consultation->patient->patient_id)->class('form-control')->attribute('disabled') }}
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Place</label>
                                {{ html()->text($name = 'place', $value = $consultation->patient->place)->class('form-control')->attribute('disabled') }}
                            </div>
                            <div class="col-12 text-center">
                                <a class="btn btn-success" type="submit" href="{{ route('store.order.create', encrypt($consultation->id)) }}">Proceed</a>
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