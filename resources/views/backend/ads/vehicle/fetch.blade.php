@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Fetch</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Fetch</li>
                        <li class="breadcrumb-item active">Vehicle</li>
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
                            <form class="row g-3" method="post" action="{{ route('vehicle.payment.fetch.update') }}">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label req">Vehicle Number / Mobile Number / Royalty Card No.</label>
                                    {{ html()->text($name = 'search_term', $value = ($inputs[0]) ?? old('search_term'))->class('form-control')->placeholder('Search Vehicle Number / Mobile Number / Royalty Card No.') }}
                                    @error('search_term')
                                    <small class="text-danger">{{ $errors->first('search_term') }}</small>
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
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Owner Name</th>
                                        <th>Reg Number</th>
                                        <th>Contact Number</th>
                                        <th>Royalty Card No.</th>
                                        <th>Status</th>
                                        <th>Days Left</th>
                                        <th>Branch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vehicles as $key => $vehicle)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $vehicle->owner_name }}</td>
                                        <td><a href="{{ route('vehicle.payment', encrypt($vehicle->id)) }}">{{ $vehicle->reg_number }}</a></td>
                                        <td>{{ $vehicle->contact_number }}</td>
                                        <td>{{ $vehicle->vcode }}</td>
                                        <td>{!! $vehicle->vstatus() !!}</td>
                                        <td class="text-success">{{ $vehicle->daysLeft() }} Days</td>
                                        <td>{{ $vehicle->branch?->name }}</td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection