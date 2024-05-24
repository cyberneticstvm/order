@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Transfer</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Product Transfer</li>
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
                            <form class="row g-3" method="post" action="{{ route('report.product.transfer.fetch') }}">
                                @csrf
                                <div class="col-md-2">
                                    <label class="form-label req">From Date</label>
                                    {{ html()->date($name = 'from_date', $value = ($inputs[0]) ?? date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('from_date')
                                    <small class="text-danger">{{ $errors->first('from_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label req">To Date</label>
                                    {{ html()->date($name = 'to_date', $value = ($inputs[1]) ?? date('Y-m-d'))->class('form-control')->placeholder('Order Date')->required() }}
                                    @error('to_date')
                                    <small class="text-danger">{{ $errors->first('to_date') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Product Type</label>
                                    {{ html()->select($name = 'product_type', array('all' => 'All', 'frame' => 'Frame', 'lens' => 'Lens'), ($inputs[6]) ?? old('product_type'))->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Product</label>
                                    {{ html()->select($name = 'product', $products, ($inputs[2]) ?? old('product'))->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Transfer Status</label>
                                    {{ html()->select($name = 'status', array('all' => 'All', '0' => 'Pending', '1' => 'Approved'), ($inputs[3]) ?? old('status'))->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Approved By</label>
                                    {{ html()->select($name = 'approved_by', $users, ($inputs[5]) ?? old('approved_by'))->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Branch</label>
                                    {{ html()->select($name = 'branch', $branches, ($inputs[4]) ?? old('branch'))->class('form-control select2')->placeholder('Select') }}
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Fetch</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                        <th>Date</th>
                                        <th>Transfer ID</th>
                                        <th>From Branch</th>
                                        <th>To Branch</th>
                                        <th>Transfer Note</th>
                                        <th>Transfer Status</th>
                                        <th>Approved By</th>
                                        <th>Approved At</th>
                                        <th>Count</th>
                                        <th>Pdf</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($sum=0)
                                    @forelse($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->created_at?->format('d.M.Y') }}</td>
                                        <td>{{ $item->transfer_number }}</td>
                                        <td>{{ $item->frombranch?->name ?? 'Main Branch' }}</td>
                                        <td>{{ $item->tobranch?->name ?? 'Main Branch' }}</td>
                                        <td>{{ $item->transfer_note }}</td>
                                        <td>{{ ($item->transfer_status == 1) ? 'Approved' : 'Pending' }}</td>
                                        <td>{{ $item->approvedBy?->name }}</td>
                                        <td>{{ $item->accepted_at?->format('d.M.Y h:i a') }}</td>
                                        <td class="text-end">{{ $item->details?->sum('qty') }}</td>
                                        <td class="text-center"><a href="{{ route('pdf.product.transfer', encrypt($item->id)) }}" target="_blank"><i class="fa fa-file-pdf-o text-success fa-lg"></i></td>
                                    </tr>
                                    @php($sum += $item->details?->sum('qty'))
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold">{{ $sum }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection