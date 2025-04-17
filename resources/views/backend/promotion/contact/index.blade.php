@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Promotion Contact Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Promotion</li>
                        <li class="breadcrumb-item active">Contact Register</li>
                    </ol>
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
                                <h5>Promotion Contact Register</h5><span>Promotion Contact Management</span>
                            </div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add New</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a class="dropdown-item txt-dark" href="{{ route('promotion.contact.create') }}">Contact</a></li>
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
                                        <th>Date</th>
                                        <th>Entity</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contacts as $key => $contact)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $contact->created_at->format('d.M.Y') }}</td>
                                        <td>{{ ucfirst($contact->entity) }}</td>
                                        <td class="{{ ($contact->type == 'include') ? 'text-success' : 'text-danger' }}">{{ ucfirst($contact->type) }}</td>
                                        <td>{{ $contact->name }}</td>
                                        <td>{{ $contact->contact_number }}</td>
                                        <td>{!! $contact->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('promotion.contact.edit', encrypt($contact->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        @if($contact->deleted_at)
                                        <td class="text-center"><a href="{{ route('promotion.contact.restore', encrypt($contact->id)) }}" class="proceed"><i class="fa fa-recycle text-success fa-lg"></i></a></td>
                                        @else
                                        <td class="text-center"><a href="{{ route('promotion.contact.delete', encrypt($contact->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
                                        @endif
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