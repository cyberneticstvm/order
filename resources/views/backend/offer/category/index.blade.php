@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Offer Category Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Offer</li>
                        <li class="breadcrumb-item active">Category Register</li>
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
                                <h5>Offer Category Register</h5><span>Offer Category Management</span>
                            </div>
                            <div class="col text-end"><a href="{{ route('offer.category.create') }}" class="btn btn-primary" type="button">Add New</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive theme-scrollbar">
                            <table class="display" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Category Name</th>
                                        <th>Discount %</th>
                                        <th>Buy</th>
                                        <th>Get</th>
                                        <th>Valid From</th>
                                        <th>Valid To</th>
                                        <th>Branch</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $key => $category)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="javascript:void(0)" class="offer" data-type="offer" data-oid="{{ $category->id }}" data-oname="{{ $category->name }}" data-branch="{{ $category->branch_id }}" data-drawer="offerDrawer">{{ $category->name }}</a></td>
                                        <td>{{ $category->discount_percentage }}</td>
                                        <td>{{ $category->buy_number }}</td>
                                        <td>{{ $category->get_number }}</td>
                                        <td>{{ $category->valid_from->format('d.M.Y') }}</td>
                                        <td>{{ $category->valid_to->format('d.M.Y') }}</td>
                                        <td>{{ $category->branch->name }}</td>
                                        <td>{{ $category->description }}</td>
                                        <td>{!! $category->status() !!}</td>
                                        <td class="text-center"><a href="{{ route('offer.category.edit', encrypt($category->id)) }}"><i class="fa fa-edit text-muted fa-lg"></i></a></td>
                                        @if($category->deleted_at)
                                        <td class="text-center"><a href="{{ route('offer.category.restore', encrypt($category->id)) }}" class="proceed"><i class="fa fa-recycle text-success fa-lg"></i></a></td>
                                        @else
                                        <td class="text-center"><a href="{{ route('offer.category.delete', encrypt($category->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
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
@include("backend.misc.offer-drawer")
@endsection