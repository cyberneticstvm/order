@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Admin Dashboard</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item active">Admin Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row widget-grid">
            <div class="col-xl-12">
                <div class="card visitor-card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5 class="m-0">Sales Chart - <span class="occbname">{{ branches()->where('id', Session::get('branch'))->first()->name ?? '' }}</span></h5>
                            <div class="card-header-right-icon">
                                <div class="dropdown icon-dropdown">
                                    <button class="btn dropdown-toggle" id="visitorButton" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="visitorButton">
                                        @forelse(userBranches()->where('type', 'branch') as $key => $item)
                                        <a class="dropdown-item" href="{{ route('switch.branch', encrypt($item->id)) }}">{{ $item->name }}</a>
                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="visitors-container">
                            <div id="sales-comparison-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection