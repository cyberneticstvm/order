@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Customer Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Customer</li>
                        <li class="breadcrumb-item active">Customer Register</li>
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
                            <form class="row g-3" method="post" action="{{ route('customer.fetch') }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Source</label>
                                    <div class="input-group">
                                        {{ html()->select('source', array('hospital' => 'Medical Record Number', 'store' => 'Customer ID'), 'hospital')->class('form-control select2') }}
                                    </div>
                                    @error('type')
                                    <small class="text-danger">{{ $errors->first('type') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Search Term</label>
                                    <div class="input-group">
                                        {{ html()->number($name = 'search_term', $value = old('search_term'))->class('form-control')->placeholder('Medical Record Number / Customer ID') }}
                                        <button class="btn btn-outline-warning btn-submit" id="button-addon2" type="submit">Fetch</button>
                                    </div>
                                    @error('search_term')
                                    <small class="text-danger">{{ $errors->first('search_term') }}</small>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
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
                        <div class="row">
                            <div class="col">
                                <h5>Customer Register</h5><span>Customer Management</span>
                            </div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Create New</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a class="dropdown-item txt-dark" href="{{ route('customer.create', ['id' => encrypt(0), 'source' => 'direct']) }}">Customer</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Contact Number</th>
                                        <th>MRN</th>
                                        <th>Created at</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($registrations as $key => $reg)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $reg->customer->id }}</td>
                                        <td>{{ $reg->customer->name }}</td>
                                        <td>{{ $reg->customer->mobile }}</td>
                                        <td>{{ ($reg->customer->mrn == '0') ? 'Direct' : $reg->customer->mrn }}</td>
                                        <td>{{ $reg->created_at->format('d, M Y h:i a') }}</td>
                                        <td>{!! $reg->status() !!}</td>
                                        <td>
                                            <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Create</button>
                                            <ul class="dropdown-menu dropdown-block">
                                                <li><a class="dropdown-item txt-dark" href="{{ route('store.order.create', ['id' => encrypt($reg->id), 'type' => '1']) }}">Spectacles</a></li>
                                                <li><a class="dropdown-item txt-dark" href="{{ route('solution.order.create', ['id' => encrypt($reg->id), 'type' => '2']) }}">Solutions</a></li>
                                            </ul>
                                        </td>
                                        <td class="text-center"><a href="{{ route('customer.registration.delete', encrypt($reg->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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
    <!-- Container-fluid Ends-->
</div>
@endsection