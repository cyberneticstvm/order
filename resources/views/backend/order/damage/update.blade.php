@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Pending Transfer Update for Damaged Product</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Transfer</li>
                        <li class="breadcrumb-item active">Pending</li>
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
                        <h5>Pending Transfer for <span class="text-primary"> {{ $transfer->id }}</span></h5><span>Pending Transfer Update</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('pending.damage.transfer.update', $transfer->id) }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Status</label>
                                    {{ html()->select('approved_status', array('0' => 'Pending', '1' => 'Approved'), $transfer->approved_status)->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('approved_status')
                                    <small class="text-danger">{{ $errors->first('approved_status') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label req">Remarks</label>
                                    {{ html()->textarea($name = 'remarks', $value = $transfer->remarks)->class('form-control')->rows('3')->placeholder('Remarks / Notes') }}
                                    @error('remarks')
                                    <small class="text-danger">{{ $errors->first('remarks') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" onclick="return validateTransferForm()" type="submit">Update</button>
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