@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Assign Order to Lab</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Orders</li>
                        <li class="breadcrumb-item active">Assign Order to Lab</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    {{ html()->form('POST', route('lab.assign.orders.save'))->attribute('name', 'labForm')->attribute('id', 'labForm')->open() }}
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-3">
                            <label class="form-label req">Lab</label>
                            {{ html()->select('lab_id', $labs->pluck('name', 'id'), old('lab_id'))->class('form-control select2')->placeholder('Select') }}
                            @error('lab_id')
                            <small class="text-danger">{{ $errors->first('lab_id') }}</small>
                            @enderror
                        </div>
                        <div class="table-responsive theme-scrollbar mt-5">
                            <table class="table table-sm" style="width:100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Select</th>
                                        <th>Date</th>
                                        <th>Order ID</th>
                                        <th>Eye</th>
                                        <th>Sph</th>
                                        <th>Cyl</th>
                                        <th>Axis</th>
                                        <th>Add</th>
                                        <th>Product</th>
                                        <th>A</th>
                                        <th>B</th>
                                        <th>DBL</th>
                                        <th>FH</th>
                                        <th>ED</th>
                                        <th>F.Type</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td class="text-center">
                                            {{ html()->checkbox('chkItem[]', '', (old('chkItem')[$key]) ?? $item->id)->class('chkItem chk_'.$item->id.' '.$item->eye) }}
                                        </td>
                                        <td>{{ $item->created_at->format('d.M.Y') }}</td>
                                        <td><a href="javascript:void(0)" class="dayBook" data-from-date="" data-to-date="" data-type="order" data-mode="{{ $item->order->id }}" data-branch="" data-drawer="orderDrawer">{{ $item->order?->ono() }}</a></td>
                                        <td class="fw-bold">{{ strtoupper($item->eye) }}</td>
                                        <td>{{ $item->sph }}</td>
                                        <td>{{ $item->cyl }}</td>
                                        <td>{{ $item->axis }}</td>
                                        <td>{{ $item->add }}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->order?->a_size }}</td>
                                        <td>{{ $item->order?->b_size }}</td>
                                        <td>{{ $item->order?->dbl }}</td>
                                        <td>{{ $item->order?->fh }}</td>
                                        <td>{{ $item->order?->ed }}</td>
                                        <td>{{ getFrameType($item->order?->id ?? 0) }}</td>
                                        <td>{{ $item->order?->name }}</td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 text-end mt-5">
                            <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                            <button class="btn btn-submit btn-success" onclick="return validateLabOrderForm()" type="submit">Assign</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ html()->form()->close() }}
    <!-- Container-fluid Ends-->
</div>
<div class="drawer drawer-left slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="orderDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document"></div>
</div>
@endsection