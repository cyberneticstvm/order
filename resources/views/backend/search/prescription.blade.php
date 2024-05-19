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
                        <li class="breadcrumb-item active">Prescription</li>
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
                            <form class="row g-3" method="post" action="{{ route('search.prescription.fetch') }}">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label req">Search Term</label>
                                    {{ html()->text($name = 'search_term', $value = ($inputs[0]) ?? old('search_term'))->class('form-control')->placeholder('Search by Customer Id, Mobile Number, Customer Name, MRN') }}
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
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Contact Number</th>
                                        <th>Doctor</th>
                                        <th>Optometrist</th>
                                        <th>Branch</th>
                                        <th>Time</th>
                                        <th>Print</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $key => $spec)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $spec->customer->id }}</td>
                                        <td>{{ $spec->customer->name }}</td>
                                        <td>{{ $spec->customer->mobile }}</td>
                                        <td>{{ $spec->doctors?->name }}</td>
                                        <td>{{ $spec->optometrists?->name }}</td>
                                        <td>{{ $spec->branch->name }}</td>
                                        <td>{{ $spec->created_at->format('d.M.Y h:i a') }}</td>
                                        <td class="text-center"><a href="{{ route('customer.order.prescription', encrypt($spec->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                        <td class="text-center"><a href="{{ route('spectacle.edit', encrypt($spec->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
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