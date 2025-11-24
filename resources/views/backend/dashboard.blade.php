@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Dashboard</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card no-radius">
                    <div class="card-header">
                        @if(Session::has('branch'))
                        <h5>Dashboard</h5><span>Hello <span class="text-primary"> {{ Auth::user()->name }}</span>, You are now logged into <span class="text-primary">{{ branches()->where('id', Session::get('branch'))->first()->name }}</span> branch!</span>
                        <p>Your Default Cash Sale Customer ID is <span class="fw-bold">{{ branches()->where('id', Session::get('branch'))->first()->cash_sales_id }}</span></p>
                        <p><a href="{{ route('product.lens.price.list') }}">Product Lens Price List</a> | <a href="{{ route('check.product.availability') }}">Check Product Availability</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
    <div class="container-fluid">
        <div class="row widget-grid">
            <div class="col-xl-3">
                <div class="card course-box widget-course">
                    <div class="card-body">
                        <div class="course-widget">
                            <div class="course-icon">
                                <svg class="fill-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#course-1') }}"></use>
                                </svg>
                            </div>
                            <div>
                                <h4 class="mb-0">₹{{ number_format($dvals[0], 2) }} + ₹{{ number_format($dvals[2], 2) }}</h4><span class="f-light"></span>
                            </div>
                        </div>
                    </div>
                    <ul class="square-group">
                        <li class="square-1 warning"></li>
                        <li class="square-1 primary"></li>
                        <li class="square-2 warning1"></li>
                        <li class="square-3 danger"></li>
                        <li class="square-4 light"></li>
                        <li class="square-5 warning"></li>
                        <li class="square-6 success"></li>
                        <li class="square-7 success"></li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card course-box widget-course">
                    <div class="card-body">
                        <div class="course-widget">
                            <div class="course-icon warning">
                                <svg class="fill-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#course-1') }}"></use>
                                </svg>
                            </div>
                            <div>
                                <h4 class="mb-0 text-success">₹{{ number_format($dvals[1], 2) }} - ₹{{ number_format($dvals[3], 2) }}</h4><span class="f-light"></span>
                            </div>
                        </div>
                    </div>
                    <ul class="square-group">
                        <li class="square-1 warning"></li>
                        <li class="square-1 primary"></li>
                        <li class="square-2 warning1"></li>
                        <li class="square-3 danger"></li>
                        <li class="square-4 light"></li>
                        <li class="square-5 warning"></li>
                        <li class="square-6 success"></li>
                        <li class="square-7 success"></li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card course-box widget-course">
                    <div class="card-body">
                        <div class="course-widget">
                            <div class="course-icon">
                                <svg class="fill-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#tag') }}"></use>
                                </svg>
                            </div>
                            <div>
                                <h4 class="mb-0 text-primary">{{ getInventory(Session::get('branch'), 0, 'frame')->sum('balanceQty') }}</h4><span class="f-light"></span>
                            </div>
                        </div>
                    </div>
                    <ul class="square-group">
                        <li class="square-1 warning"></li>
                        <li class="square-1 primary"></li>
                        <li class="square-2 warning1"></li>
                        <li class="square-3 danger"></li>
                        <li class="square-4 light"></li>
                        <li class="square-5 warning"></li>
                        <li class="square-6 success"></li>
                        <li class="square-7 success"></li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card visitor-card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5 class="m-0">Order Comparison Chart - <span class="occbname">{{ branches()->where('id', Session::get('branch'))->first()->name ?? '' }}</span></h5>
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
                            <div id="order-comparison-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(!Session::has('branch'))
<div class="modal fade" id="branchSelector" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="branchSelector" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-toggle-wrapper">
                    <ul class="modal-img">
                        <li><i class="icon-hand-point-down text-muted txt-secondary" style="font-size: 3rem;"></i></li>
                    </ul>
                    <h4 class="text-center pb-2">Select Branch!</h4>
                    <form method="post" action="{{ route('user.branch.update') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mt-3">
                                {{ html()->select($name = 'branch', $value = $branches, NULL)->class('form-control')->placeholder('Select Branch')->required() }}
                                @error('branches')
                                <small class="text-danger">{{ $errors->first('branches') }}</small>
                                @enderror
                            </div>
                        </div>
                        <button class="btn btn-secondary d-flex m-auto mt-3 btn-submit" type="submit">Update Branch</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection