@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Purchase Return Register</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Purchase</li>
                        <li class="breadcrumb-item active">Purchase Return Register</li>
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
                                <h5>Purchase Return Register</h5><span>Purchase Return</span>
                            </div>
                            <div class="col text-end">
                                <div class="btn-group">
                                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Create</button>
                                    <ul class="dropdown-menu dropdown-block">
                                        <li><a href="javascript:void(0)" class="dropdown-item preturn" data-type="damage" data-drawer="prDrawer"><i class="fa fa-bars text-success"></i> Damage</a></li>
                                        <li><a href="javascript:void(0)" class="dropdown-item preturn" data-type="purchase" data-drawer="prDrawer"><i class="fa fa-clone text-success"></i> Purchase</a></li>
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
                                        <th>Return Id</th>
                                        <th>Supplier</th>
                                        <th>Ret. Date</th>
                                        <th>Ret. Note</th>
                                        <th>Ret. Type</th>
                                        <th>Courier Charges</th>
                                        <th>Other Charges</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($returns as $key => $return)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $return->id }}</td>
                                        <td>{{ $return->supplier->name }}</td>
                                        <td>{{ $return->created_at->format("d.m.Y") }}</td>
                                        <td>{{ $return->notes }}</td>
                                        <td>{{ $return->rtype }}</td>
                                        <td class="text-end">{{ return->courier_charges }}</td>
                                        <td class="text-end">{{ return->other_charges }}</td>
                                        <td class="text-end">{{ number_format(return->courier_charges + return->courier_charges + $return->details->sum('amount'), 2) }}</td>
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
@include("backend.misc.purchase-return-drawer")
@endsection