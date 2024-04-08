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
                                        <th>Order</th>
                                        <th>Prescription</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $key => $customer)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $customer->id }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->mobile }}</td>
                                        <td>{{ ($customer->mrn == '0') ? 'Direct' : $customer->mrn }}</td>
                                        @if($customer->hasOrder($customer->specid))
                                        <td class="text-success text-center">Done!</td>
                                        @else
                                        <td>
                                            <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Create</button>
                                            <ul class="dropdown-menu dropdown-block">
                                                <li><a class="dropdown-item txt-dark" href="{{ route('store.order.create', ['cid' => encrypt($customer->id), 'sid' => encrypt($customer->specid), 'type' => '1']) }}">Spectacles</a></li>
                                                <li><a class="dropdown-item txt-dark" href="{{ route('solution.order.create', ['id' => encrypt($customer->id), 'type' => '2']) }}">Solutions</a></li>
                                            </ul>
                                        </td>
                                        @endif
                                        <td class="text-center"><a href="{{ route('customer.spectacle.edit', encrypt($customer->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></a></td>
                                        <td>{!! $customer->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('customer.edit', encrypt($customer->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('customer.delete', encrypt($customer->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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