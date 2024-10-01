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
            <div class="col-xl-6">
                <div class="card visitor-card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5 class="m-0">Sales Numbers - <span class="occbname">{{ branches()->where('id', Session::get('branch'))->first()->name ?? '' }}</span></h5>
                            <div class="dropdown">
                                <label class="form-label">Month</label>
                                {{ html()->select($name = 'month', allMonths()->pluck('name', 'id'), $value = date('m'))->class('form-control selChangeChart')->attribute('id', 'selChangeMonth')->placeholder('Select') }}
                            </div>
                            <div class="dropdown">
                                <label class="form-label">Year</label>
                                {{ html()->select($name = 'year', array('2024' => '2024', '2025' => '2025', '2026' => '2026'), $value = date('Y'))->attribute('id', 'selChangeYear')->class('form-control selChangeChart')->placeholder('Select') }}
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