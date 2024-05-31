@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Import Purchase</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Purchase</li>
                        <li class="breadcrumb-item active">Import Purchase</li>
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
                            <form class="row g-3" method="post" action="{{ route('stock.preview.update') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label">Branch</label>
                                    <div class="input-group">
                                        {{ html()->select('branch', $branches->pluck('name', 'id'), $inputs[0])->class('form-control select2') }}
                                    </div>
                                    @error('branch')
                                    <small class="text-danger">{{ $errors->first('branch') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Product Category</label>
                                    <div class="input-group">
                                        {{ html()->select('category', array('lens' => 'Lens', 'frame' => 'Frame', 'solution' => 'Solution', 'accessory' => 'Accessory'), $inputs[1])->class('form-control select2') }}
                                    </div>
                                    @error('category')
                                    <small class="text-danger">{{ $errors->first('category') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label req">File Upload</label>
                                    <div class="input-group">
                                        {{ html()->file($name = 'file')->class('form-control') }}
                                        <button class="btn btn-outline-warning btn-submit" id="button-addon2" type="submit">Preview</button>
                                    </div>
                                    @error('file')
                                    <small class="text-danger">{{ $errors->first('file') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <a href="{{ asset('/backend/assets/docs/Compare.xlsx') }}">Download Format</a>
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
                        <h5>Preview</h5>
                        <div class="table-responsive theme-scrollbar">
                            <table class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SL No</th>
                                        <th>Product Id</th>
                                        <th>Product Name</th>
                                        <th>Product Code</th>
                                        <th>Category</th>
                                        <th>Branch</th>
                                        <th>Qty</th>
                                        <!--<th>Delete</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $key => $item)
                                    <tr class="{{ (!$item->product_id) ? 'table-danger' : '' }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->product_id }}</td>
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->category }}</td>
                                        <td>{{ $item->branch->name }}</td>
                                        <td class=" text-end">{{ $item->qty }}</td>
                                        <!--<td class="text-center"><a href="{{ route('temp.item.delete', encrypt($item->id)) }}" class="dlt"><i class="fa fa-trash text-danger fa-lg"></i></a></td>-->
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold">{{ $products->sum('qty') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col text-end mt-3">
                            <div class="btn-group">
                                <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                <ul class="dropdown-menu dropdown-block">
                                    <li><a class="dropdown-item txt-dark fw-bold proceed" href="{{ route('stock.compare', ['category' => $inputs[1], 'branch' => $inputs[0]]) }}">Compare Stock</a></li>
                                    <li><a class="dropdown-item txt-dark fw-bold proceed" href="{{ route('stock.update', ['category' => $inputs[1], 'branch' => $inputs[0]]) }}">Update Stock</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection