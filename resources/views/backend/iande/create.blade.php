@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Income & Expense</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Income & Expense</li>
                        <li class="breadcrumb-item active">Create</li>
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
                        <h5>Create Income & Expense</h5><span>Create New Income & Expense</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('iande.save') }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">Date</label>
                                    {{ html()->date($name = 'date', $value = date('Y-m-d'))->class('form-control')->placeholder('Date')->disabled() }}
                                    @error('date')
                                    <small class="text-danger">{{ $errors->first('date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Head Name</label>
                                    {{ html()->select($name = 'head_id', $heads, $value = old('head_id'))->class('form-control')->placeholder('Select') }}
                                    @error('head_id')
                                    <small class="text-danger">{{ $errors->first('head_id') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label req">Description</label>
                                    {{ html()->text($name = 'description', $value = old('description'))->class('form-control')->placeholder('Description') }}
                                    @error('description')
                                    <small class="text-danger">{{ $errors->first('description') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Amount</label>
                                    {{ html()->number($name = 'amount', $value = old('amount'), $min='1', $max='', $step='any')->class('form-control')->placeholder('0.00') }}
                                    @error('amount')
                                    <small class="text-danger">{{ $errors->first('amount') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Save</button>
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