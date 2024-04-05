@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Settings</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item active">Account Adjustment</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="get" action="{{ route('account.setting.fetch') }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Date</label>
                                    {{ html()->date($name = 'date', $value = $inputs[0] ?? date('y-m-d'))->class('form-control')->placeholder('Date') }}
                                    @error('date')
                                    <small class="text-danger">{{ $errors->first('date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Branch</label>
                                    <div class="input-group">
                                        {{ html()->select('branch', $branches->pluck('name', 'id'), $inputs[1] ?? old('branch'))->class('form-control')->placeholder('Branch') }}
                                        <button class="btn btn-outline-warning btn-submit" id="button-addon2" type="submit">Fetch</button>
                                    </div>
                                    @error('branch')
                                    <small class="text-danger">{{ $errors->first('branch') }}</small>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($closing)
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Settings</h5><span>Update Closing Balance</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('account.setting.update') }}">
                                @csrf
                                <input type="hidden" name="date" value="{{ $inputs[0] }}" />
                                <input type="hidden" name="branch" value="{{ $inputs[1] }}" />
                                <div class="col-md-2">
                                    <label class="form-label req">Closing Balance</label>
                                    {{ html()->number($name = 'closing_balance', $value = $closing->closing_balance, $min='', $max='', $step='any')->class('form-control')->attribute('readonly', 'true') }}
                                    @error('closing_balance')
                                    <small class="text-danger">{{ $errors->first('closing_balance') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Operation</label>
                                    {{ html()->select($name = 'operand', $value = array('add' => '+', 'sub' => '-'), )->class('form-control')->attribute('readonly', 'true') }}
                                    @error('operand')
                                    <small class="text-danger">{{ $errors->first('operand') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Amount</label>
                                    {{ html()->number($name = 'amount', $value = '', $min='1', $max='', $step='any')->class('form-control')->placeholder('0') }}
                                    @error('amount')
                                    <small class="text-danger">{{ $errors->first('amount') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection