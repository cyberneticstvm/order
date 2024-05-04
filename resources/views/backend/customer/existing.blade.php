@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Customer Existing Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Customer</li>
                        <li class="breadcrumb-item active">Customer Existing Register</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    {{ html()->form('POST', route('customer.exists.proceed'))->open() }}
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <h5>Customer Existing Register</h5><span>Customer Existing</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Contact Number</th>
                                        <th>MRN</th>
                                        <th>Created at</th>
                                        <th>Status</th>
                                        <th class="text-center">Select</th>
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
                                        <td>{{ $customer->created_at->format('d, M Y h:i a') }}</td>
                                        <td>{!! $customer->status() !!}</td>
                                        <td class="text-center">
                                            {{ html()->radio('rad')->attribute('value', $customer->id) }}
                                        </td>
                                    </tr>
                                    @empty
                                    @endforelse
                                    <tr>
                                        <td colspan="7" class="text-end">
                                            Proceed as a new Customer
                                            @error('rad')
                                            <small class="text-danger">{{ $errors->first('rad') }}</small>
                                            @enderror
                                        </td>
                                        <td class="text-center">
                                            {{ html()->radio('rad')->attribute('value', '0') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 text-end mt-3">
                            <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                            <button class="btn btn-submit btn-success" type="submit">Proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ html()->form()->close() }}
</div>
@endsection