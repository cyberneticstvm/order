@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Stock Update Confirmation</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Stock update</li>
                        <li class="breadcrumb-item active">Confirmation</li>
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
                            <form class="row g-3" method="post" action="{{ route('stock.update.proceed') }}">
                                @csrf
                                <input type="hidden" name="category" value="{{ $category }}" />
                                <input type="hidden" name="branch" value="{{ $branch }}" />
                                <input type="hidden" name="confirmation_text" value="I'm aware of the impacts of being proceed with the current action." />
                                <div class="col-md-6">
                                    <label class="form-label">I'm aware of the impacts of being proceed with the current action. </label>
                                    {{ html()->text($name = 'confirm_text', old('confirm_text'))->class('form-control')->placeholder('please enter the text exactly as it is displayed above to proceed.') }}
                                    @error('confirm_text')
                                    <small class="text-danger">{{ $errors->first('confirm_text') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Proceed with Stock Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection