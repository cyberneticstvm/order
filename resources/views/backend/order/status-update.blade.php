@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Order Status Update</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Order Status</li>
                        <li class="breadcrumb-item active">Update</li>
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
                            <form class="row g-3" method="post" action="{{ route('order.status.update', $order->id) }}">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label">Order Number</label>
                                    {{ html()->text('order_number', $order->branch->code.'/'.$order->id )->class('form-control')->attribute('readonly', 'true') }}
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Order Status</label>
                                    {{ html()->select('order_status', orderStatuses(), $order->order_status)->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('order_status')
                                    <small class="text-danger">{{ $errors->first('order_status') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Status Note / Customer Feedback</label>
                                    {{ html()->textarea('status_note', old('status_note'))->class('form-control')->rows(5)->placeholder('Status Note / Customer Feedback') }}
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    @if(Session::has('geninv'))
                                    <button class="btn btn-submit btn-success" type="submit">Update</button>
                                    @else
                                    <button class="btn btn-submit btn-warning" type="submit">Proceed</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
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
                            @forelse($notes as $key => $note)
                            <div class="info-block mb-3">
                                <h6>{{ $note->status_note }}</h6>
                                <p>By {{ $note->user->name }}, at {{ $note->created_at->format('d, m Y h:i a') }}</p>
                            </div>
                            <hr />
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection