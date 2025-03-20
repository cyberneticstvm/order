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
                        <li class="breadcrumb-item active">Royalty Card</li>
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
                            <form class="row g-3" method="post" action="{{ route('settings.royalty.card.fetch') }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label req">Card Type</label>
                                    <div class="input-group">
                                        {{ html()->select($name = 'card_type', $value = $cards, old('card_type') ?? $card)->class('form-control')->placeholder('Select') }}
                                        <button class="btn btn-outline-warning btn-submit" id="button-addon2" type="submit">Fetch</button>
                                    </div>
                                    @error('card_type')
                                    <small class="text-danger">{{ $errors->first('card_type') }}</small>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper table-responsive">
                            <form class="row g-3" method="post" action="{{ route('settings.royalty.card.update') }}">
                                <input type="hidden" name="cardType" value="{{ $card }}" />
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>Category</th>
                                            <th>Discount%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @csrf
                                        @if($data->isNotEmpty())
                                        @forelse($data as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                {{ $item->category }}
                                                <input type="hidden" name="categories[]" value="{{ $item->category }}" />
                                            </td>
                                            <td><input type="number" name="discounts[]" class="form-control" value="{{ $item->discount_percentage }}" min="0" max="100" /></td>
                                        </tr>
                                        @empty
                                        @endforelse
                                        @else
                                        @forelse($cats as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                {{ $item }}
                                                <input type="hidden" name="categories[]" value="{{ $item }}" />
                                            </td>
                                            <td><input type="number" name="discounts[]" class="form-control" value="0" min="0" max="100" /></td>
                                        </tr>
                                        @empty
                                        @endforelse
                                        @endif
                                    </tbody>
                                </table>
                                <div class="col-md-12 text-end">
                                    <button class="btn btn-success btn-submit" id="button-addon2" type="submit">Update</button>
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