@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Lab</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Lab</li>
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
                            <form class="row g-3" method="post" action="{{ route('report.lab.fetch') }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">From Date</label>
                                    {{ html()->date($name = 'from_date', $value = ($inputs[0]) ?? date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('from_date')
                                    <small class="text-danger">{{ $errors->first('from_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">To Date</label>
                                    {{ html()->date($name = 'to_date', $value = ($inputs[1]) ?? date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('to_date')
                                    <small class="text-danger">{{ $errors->first('to_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">Order Status</label>
                                    {{ html()->select('order_status', array('all' => 'All Status') + orderStatuses(), ($inputs[2]) ?? old('order_status'))->class('form-control select2')->placeholder('Select')->required() }}
                                    @error('order_status')
                                    <small class="text-danger">{{ $errors->first('order_status') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Branch</label>
                                    {{ html()->select($name = 'branch', $value = $branches, ($inputs[3]) ?? old('branch'))->class('form-control select2')->placeholder('Select') }}
                                    @error('branch')
                                    <small class="text-danger">{{ $errors->first('branch') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Fetch</button>
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
            @forelse($labs as $key => $item)
            <div class="col-md-6">
                <div class="card widget-hover">
                    <div class="card-body radial-progress-card">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="mb-0">{{ $item->consultation?->mrn ?? $item->name }} <span class="text-primary">({{ ucfirst($item->order_status)}})</span></h6>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-0">Booked: {{ $item->created_at?->format('d/M/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-0">Due: {{ $item->expected_delivery_date?->format('d/M/Y') }}</p>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td>RE</td>
                                                <td>{{ $item->details?->where('eye', 're')?->first()?->sph }}</td>
                                                <td>{{ $item->details?->where('eye', 're')?->first()?->cyl }}</td>
                                                <td>{{ $item->details?->where('eye', 're')?->first()?->axis }}</td>
                                                <td>{{ $item->details?->where('eye', 're')?->first()?->add }}</td>
                                                <td>{{ $item->details?->where('eye', 're')?->first()?->dia }}</td>
                                                <td>{{ $item->details?->where('eye', 're')?->first()?->thickness }}</td>
                                                <td>{{ $item->details?->where('eye', 're')?->first()?->product->name }}</td>
                                            </tr>
                                            <tr>
                                                <td>LE</td>
                                                <td>{{ $item->details?->where('eye', 'le')?->first()?->sph }}</td>
                                                <td>{{ $item->details?->where('eye', 'le')?->first()?->cyl }}</td>
                                                <td>{{ $item->details?->where('eye', 'le')?->first()?->axis }}</td>
                                                <td>{{ $item->details?->where('eye', 'le')?->first()?->add }}</td>
                                                <td>{{ $item->details?->where('eye', 'le')?->first()?->dia }}</td>
                                                <td>{{ $item->details?->where('eye', 'le')?->first()?->thickness }}</td>
                                                <td>{{ $item->details?->where('eye', 're')?->first()?->product->name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Frame</td>
                                                <td colspan="7">{{ $item->details?->where('eye', 'frame')?->first()?->product->name }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            @endforelse
        </div>
    </div>
</div>
@endsection