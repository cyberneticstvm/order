@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Received from Lab Orders</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Orders</li>
                        <li class="breadcrumb-item active">Received from Lab</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    {{ html()->form('POST', route('received.from.lab.orders.update'))->attribute('name', 'labStatusForm')->attribute('id', 'labStatusForm')->open() }}
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label req">Status</label>
                                {{ html()->select('status', $status, old('status'))->class('form-control select2')->placeholder('Select') }}
                                @error('status')
                                <small class="text-danger">{{ $errors->first('status') }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lab</label>
                                {{ html()->select('lab_id', $labs->pluck('name', 'id'), old('lab_id'))->class('form-control select2')->placeholder('Select') }}
                                @error('lab_id')
                                <small class="text-danger">{{ $errors->first('lab_id') }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-submit btn-lg btn-success mt-4" onclick="return validateLabStatusOrderForm()" type="submit">Update</button>
                            </div>
                        </div>
                        <div class="table-responsive theme-scrollbar mt-5">
                            <table class="table table-sm" style="width:100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Select</th>
                                        <th>Notes</th>
                                        <th>Order ID</th>
                                        <th>Eye</th>
                                        <th>Sph</th>
                                        <th>Cyl</th>
                                        <th>Axis</th>
                                        <th>Add</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                        <th>Customer Name</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $key => $item)
                                    <tr>
                                        <td class="">{{ $key + 1 }}</td>
                                        <td class=" text-center">
                                            {{ html()->checkbox('chkItem[]', '', (old('chkItem')[$key]) ?? $item->id)->class('chkItem') }}
                                        </td>
                                        <td class="text-center"><a href="javascript:void(0)" class="labNote" data-drawer="labNoteDrawer" data-oid="{{ $item->order_id }}"><i class="fa fa-edit {{ isOrderNote($item->order?->id) }} fa-lg"></i></a></td>
                                        <td><a href="javascript:void(0)" class="dayBook {{ $item->order?->isEdited() }}" data-from-date="" data-to-date="" data-type="order" data-mode="{{ $item->order?->id }}" data-branch="" data-drawer="orderDrawer">{{ $item->order?->ono() }}</a></td>
                                        <td class="{{ isOrderNote($item->order?->id) }}">{{ strtoupper($item->item?->eye) }}</td>
                                        <td>{{ $item->item?->sph }}</td>
                                        <td>{{ $item->item?->cyl }}</td>
                                        <td>{{ $item->item?->axis }}</td>
                                        <td>{{ $item->item?->add }}</td>
                                        <td class="{{ $item->order?->isEdited() }}">{{ $item->item?->product?->name ?? '' }}</td>
                                        <td>{{ $item->item?->qty }}</td>
                                        <td>{{ $item->status }}</td>
                                        <td>{{ $item->order?->name ?? '' }}</td>
                                        <td class="text-center"><a href="{{ route('lab.order.delete', encrypt($item->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 text-end mt-5">
                            <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                            <button class="btn btn-submit btn-success" onclick="return validateLabStatusOrderForm()" type="submit">Update</button>
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
<div class="drawer drawer-left slide" tabindex="-1" role="dialog" aria-labelledby="drawer-3-title" aria-hidden="true" id="labNoteDrawer">
    <div class="drawer-content drawer-content-scrollable" role="document">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12">
                    <h5>Order Notes for <span class="labOrderId"></span></h5>
                </div>
                <div class="col-12 oldNotes mt-3"></div>
                {{ html()->form('POST', route('lab.note.update'))->open() }}
                <input type="hidden" name="order_id" id="order_id" value="" />
                <div class="col-12 mt-3">
                    {{ html()->textarea('notes')->class('form-control')->attribute('rows', 5)->placeholder('Notes / Remarks') }}
                </div>
                <div class="col mt-3 text-end">
                    {{ html()->submit('Update')->class('btn btn-submit btn-primary') }}
                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
</div>
@endsection