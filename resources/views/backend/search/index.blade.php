@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Search</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Search</li>
                        <li class="breadcrumb-item active">All</li>
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
                            <form class="row g-3" method="post" action="{{ route('search.fetch') }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Search By</label>
                                    {{ html()->select($name = 'search_by', $value = array('mrn' => 'MRN', 'mobile' => 'Mobile', 'pid' => 'Patient ID', 'pname' => 'Patient Name'), ($inputs[0]) ?? old('search_by'))->class('form-control select2')->placeholder('Select') }}
                                    @error('search_by')
                                    <small class="text-danger">{{ $errors->first('search_by') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">Search Term</label> <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<strong>MRN and Patient ID number parts only.</strong>"><i class="fa fa-info txt-info"></i></a>
                                    {{ html()->text($name = 'search_term', $value = ($inputs[1]) ?? old('search_term'))->class('form-control')->placeholder('Search Term') }}
                                    @error('search_term')
                                    <small class="text-danger">{{ $errors->first('search_term') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    @if($data)
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
                                        <th>Patient Name</th>
                                        <th>Patient ID</th>
                                        <th>Mobile</th>
                                        <th>MRNs</th>
                                        <th>Orders</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->patient_id }}</td>
                                        <td>{{ $item->mobile }}</td>
                                        <td>
                                            @forelse($item->consultation as $key1 => $mrn)
                                            <a href="{{ route('consultation.edit', encrypt($mrn->id)) }}">{{ $mrn->mrn }}</a><br />
                                            @empty
                                            @endforelse
                                        </td>
                                        <td>
                                            @forelse($item->consultation as $key2 => $order)
                                            @foreach($order->orders as $key3 => $ord)
                                            <a href="{{ ($ord->category == 'store') ? route('store.order.edit', encrypt($ord->id)) : route('pharmacy.order.edit', encrypt($ord->id)) }}">{{ $ord->invoice_number }}</a><br />
                                            @endforeach
                                            @empty
                                            @endforelse
                                        </td>
                                        <td>{!! $item->status() !!}</td>
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
    @endif
    <!-- Container-fluid Ends-->
</div>

@endsection