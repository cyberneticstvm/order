@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Update Spectacle Prescription</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Spectacle Prescription</li>
                        <li class="breadcrumb-item active">Update</li>
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
                                <h5>Spectacle Prescription</h5><span>Update Spectacle Prescription</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('customer.spectacle.update', $spectacle->id) }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Name</label>
                                    {{ html()->text($name = 'name', $value = $customer->name)->class('form-control')->placeholder('Customer Name')->disabled() }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Optometrist</label>
                                    {{ html()->select('optometrist', $optometrists, $spectacle->optometrist)->class('form-control select2')->placeholder('Select') }}
                                    @error('optometrist')
                                    <small class="text-danger">{{ $errors->first('optometrist') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Doctor</label>
                                    {{ html()->select('doctor', $doctors, $spectacle->doctor)->class('form-control select2')->placeholder('Select') }}
                                    @error('doctor')
                                    <small class="text-danger">{{ $errors->first('doctor') }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 mt-5 table-responsive">
                                    <h5 class="text-center text-secondary">Prescription Details</h5>
                                    <table class="table table-bordered table-stripped mt-3">
                                        <thead class="text-center">
                                            <tr>
                                                <th width="16%">Eye</th>
                                                <th width="12%">SPH</th>
                                                <th width="12%">CYL</th>
                                                <th width="12%">AXIS</th>
                                                <th width="12%">ADD</th>
                                                <th width="12%">VA</th>
                                                <th width="12%">PD</th>
                                                <th width="12%">INT.ADD</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('re_eye', 'RE')->class('form-control border-0 text-center')->placeholder('RE')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('re_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->re_dist_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 're_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('re_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->re_dist_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 're_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('re_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->re_dist_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 're_axis') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('re_add', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->re_dist_add ?? $powers?->where('name', 'add')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 're_add') }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('re_va', $spectacle->re_va)->class('form-control border-0 text-center')->maxlength(6)->placeholder('VA')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('re_pd', $spectacle?->rpd ?? '')->class('form-control border-0 text-center')->placeholder('PD')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('re_int_add', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->re_int_add ?? $powers?->where('name', 'intad')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 're_int_add') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('le_eye', 'LE')->class('form-control border-0 text-center')->placeholder('RE')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('le_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->le_dist_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'le_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('le_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->le_dist_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'le_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('le_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->le_dist_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'le_axis') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('le_add', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->le_dist_add ?? $powers?->where('name', 'add')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'le_add') }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('le_va', $spectacle->le_va)->class('form-control border-0 text-center')->maxlength(6)->placeholder('VA')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('le_pd', $spectacle?->lpd ?? '')->class('form-control border-0 text-center')->placeholder('PD')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('le_int_add', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->le_int_add ?? $powers?->where('name', 'intad')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'le_int_add') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    {{
                                                        html()->text('a_size', $spectacle->a_size)->class('form-control border-0 text-center')->maxlength(2)->placeholder('A Size')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('b_size', $spectacle->b_size)->class('form-control border-0 text-center')->maxlength(2)->placeholder('B Size')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('dbl', $spectacle->dbl)->class('form-control border-0 text-center')->maxlength(2)->placeholder('DBL')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('fh', $spectacle->fh)->class('form-control border-0 text-center')->maxlength(2)->placeholder('FH')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('ed', $spectacle->ed)->class('form-control border-0 text-center')->maxlength(2)->placeholder('ED')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('vd', $spectacle->vd)->class('form-control border-0 text-center')->maxlength(2)->placeholder('VD')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('w_angle', $spectacle->w_angle)->class('form-control border-0 text-center')->maxlength(2)->placeholder('W. Angle')
                                                    }}
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-secondary" onClick="window.history.back()" type="button">Cancel</button>
                                    <button class="btn btn-submit btn-success" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
@endsection