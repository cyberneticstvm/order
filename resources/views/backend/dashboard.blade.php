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
                        <li class="breadcrumb-item">Pages</li>
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
                        <h5>Dashboard</h5><span>Hello <span class="text-primary"> {{ Auth::user()->name }}</span>, You are now logged into <span class="text-primary">{{ branches()->where('id', Session::get('branch'))->first()->name}}</span> branch!</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <h5>Patient Register</h5><span>Patient Management</span>
                            </div>
                            <div class="col text-end"><a href="{{ route('patient.create', ['type' => 'Direct', 'type_id' => '0']) }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Patient Name</th>
                                        <th>Patient ID</th>
                                        <th>Mobile</th>
                                        <th>Place</th>
                                        <th>Review</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patients as $key => $patient)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $patient->name }}</td>
                                        <td>{{ $patient->patient_id }}</td>
                                        <td>{{ $patient->mobile }}</td>
                                        <td>{{ $patient->place }}</td>
                                        <td class="text-center"><a href="/backend/consultation/create/{{ encrypt($patient->id) }}">Review</a></td>
                                        <td>{!! $patient->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('patient.edit', encrypt($patient->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        <td class="text-center"><a href="{{ route('patient.delete', encrypt($patient->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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