@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6"><h3>Purchase Update - Lens</h3></div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
                        <li class="breadcrumb-item">Lens</li>
                        <li class="breadcrumb-item active">Purchase Update</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header"><h5>Purchase Lens Update</h5><span>Purchase Lens Update</span></div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            @include('backend.purchase.lens._form', ['formAction' => route('lens.purchase.update', $purchase->id)])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
