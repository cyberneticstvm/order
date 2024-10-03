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
                                <h5>Spectacle Prescription</h5><span>Create Spectacle Prescription</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('spectacle.update', $spectacle->id) }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Customer Name</label>
                                    {{ html()->text($name = 'name', $value = $spectacle->customer->name)->class('form-control')->placeholder('Customer Name')->disabled() }}
                                    @error('name')
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Optometrist</label>
                                    {{ html()->select('optometrist', $optometrists, $spectacle->optometrist ?? '')->class('form-control select2')->placeholder('Select') }}
                                    @error('optometrist')
                                    <small class="text-danger">{{ $errors->first('optometrist') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Doctor</label>
                                    {{ html()->select('doctor', $doctors, $spectacle->doctor ?? '')->class('form-control select2')->placeholder('Select') }}
                                    @error('doctor')
                                    <small class="text-danger">{{ $errors->first('doctor') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Optometrist (Hospital)</label>
                                    {{ html()->text($name = 'optometrist_hospital', $spectacle->optometrist_hospital ?? '')->class('form-control')->placeholder('Optometrist (Hospital)') }}
                                </div>
                                <div class="col-md-6 mt-5 table-responsive">
                                    <h5 class="text-center text-secondary">Prescription Details</h5>
                                    <table class="table table-stripped mt-3">
                                        <thead class="text-center">
                                            <tr>
                                                <th width="">Eye</th>
                                                <th width="">SPH</th>
                                                <th width="">CYL</th>
                                                <th width="">AXIS</th>
                                                <th width="">ADD</th>
                                                <th width="">VA</th>
                                                <th width="">UC</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('re_eye', 'RE')->class('form-control border-0 text-center')->placeholder('RE')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('re_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->re_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 're_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('re_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->re_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 're_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('re_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->re_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 're_axis') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('re_add', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->re_add ?? $powers?->where('name', 'add')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 're_add') }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('re_va', $spectacle->re_va ?? '')->class('form-control border-0 text-center')->maxlength(6)->placeholder('VA')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('re_uc', $spectacle?->re_uc ?? '')->class('form-control border-0 text-center')->placeholder('UC')
                                                    }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('le_eye', 'LE')->class('form-control border-0 text-center')->placeholder('RE')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('le_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->le_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'le_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('le_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->le_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'le_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('le_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->le_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'le_axis') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('le_add', $powers?->where('name', 'add')->pluck('value', 'value'), $spectacle?->le_add ?? $powers?->where('name', 'add')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'le_add') }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('le_va', $spectacle->le_va ?? '')->class('form-control border-0 text-center')->maxlength(6)->placeholder('VA')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('le_uc', $spectacle?->le_uc ?? '')->class('form-control border-0 text-center')->placeholder('UC')
                                                    }}
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="col-md-6 mt-5 table-responsive">
                                    <h5 class="text-center text-secondary">Contact Lens Prescription</h5>
                                    <table class="table table-stripped mt-3">
                                        <thead class="text-center">
                                            <tr>
                                                <th width="">Eye</th>
                                                <th width="">BASE CURVE</th>
                                                <th width="">DIA</th>
                                                <th width="">SPH</th>
                                                <th width="">CYL</th>
                                                <th width="">AXIS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('cl_od', 'OD')->class('form-control border-0 text-center')->placeholder('OD')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('cl_od_base_curve', $spectacle?->cl_od_base_curve ?? '')->class('form-control border-0 text-center')->placeholder('Base Curve')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('cl_od_dia', $spectacle?->cl_od_dia ?? '')->class('form-control border-0 text-center')->placeholder('DIA')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('cl_od_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->cl_od_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'cl_od_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('cl_od_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->cl_od_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'cl_od_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('cl_od_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->cl_od_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'cl_od_axis') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('cl_os', 'OS')->class('form-control border-0 text-center')->placeholder('OS')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('cl_os_base_curve', $spectacle?->cl_os_base_curve ?? '')->class('form-control border-0 text-center')->placeholder('Base Curve')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('cl_os_dia', $spectacle?->cl_os_dia ?? '')->class('form-control border-0 text-center')->placeholder('DIA')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('cl_os_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->cl_os_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'cl_os_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('cl_os_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->cl_os_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'cl_os_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('cl_os_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->cl_os_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'cl_os_axis') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-5 mt-5 table-responsive">
                                    <h5 class="text-center text-secondary">ARM Value</h5>
                                    <table class="table table-stripped">
                                        <thead class="text-center">
                                            <tr>
                                                <th width="">Eye</th>
                                                <th width="">SPH</th>
                                                <th width="">CYL</th>
                                                <th width="">AXIS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('arm_od', 'OD')->class('form-control border-0 text-center')->placeholder('OD')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('arm_od_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->arm_od_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'arm_od_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('arm_od_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->arm_od_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'arm_od_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('arm_od_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->arm_od_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'arm_od_axis') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('arm_os', 'OS')->class('form-control border-0 text-center')->placeholder('OS')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('arm_os_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->arm_os_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'arm_os_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('arm_os_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->arm_os_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'arm_os_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('arm_os_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->arm_os_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'arm_os_axis') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-7 mt-5 table-responsive">
                                    <h5 class="text-center text-secondary">PGP</h5>
                                    <table class="table table-stripped">
                                        <thead class="text-center">
                                            <tr>
                                                <th width="">Eye</th>
                                                <th width="">SPH</th>
                                                <th width="">CYL</th>
                                                <th width="">AXIS</th>
                                                <th width="">ADD</th>
                                                <th width="">VISION</th>
                                                <th width="">NV</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('pgp_od', 'OD')->class('form-control border-0 text-center')->placeholder('OD')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('pgp_od_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->pgp_od_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'pgp_od_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('pgp_od_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->pgp_od_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'pgp_od_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('pgp_od_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->pgp_od_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'pgp_od_axis') }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('pgp_od_add', $spectacle?->pgp_od_add ?? '')->class('form-control border-0 text-center')->placeholder('ADD')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('pgp_od_vision', $spectacle?->pgp_od_vision ?? '')->class('form-control border-0 text-center')->placeholder('VISION')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('pgp_od_nv', $spectacle?->pgp_od_nv ?? '')->class('form-control border-0 text-center')->placeholder('NV')
                                                    }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('pgp_os', 'OS')->class('form-control border-0 text-center')->placeholder('OS')->attribute('disabled', 'true')
                                                    }}
                                                </td>
                                                <td>
                                                    {{ html()->select('pgp_os_sph', $powers?->where('name', 'sph')->pluck('value', 'value'), $spectacle?->pgp_os_sph ?? $powers?->where('name', 'sph')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'pgp_os_sph') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('pgp_os_cyl', $powers?->where('name', 'cyl')->pluck('value', 'value'), $spectacle?->pgp_os_cyl ?? $powers?->where('name', 'cyl')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'pgp_os_cyl') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('pgp_os_axis', $powers?->where('name', 'axis')->pluck('value', 'value'), $spectacle?->pgp_os_axis ?? $powers?->where('name', 'axis')->where('default', 'true')?->first()?->value)->class('border-0 select2')->attribute('id', 'pgp_os_axis') }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('pgp_os_add', $spectacle?->pgp_os_add ?? '')->class('form-control border-0 text-center')->placeholder('ADD')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('pgp_os_vision', $spectacle?->pgp_os_vision ?? '')->class('form-control border-0 text-center')->placeholder('VISION')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('pgp_os_nv', $spectacle?->pgp_os_nv ?? '')->class('form-control border-0 text-center')->placeholder('NV')
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6 mt-5 table-responsive">
                                    <h5 class="text-center text-secondary">PD Values</h5>
                                    <table class="table table-stripped">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    {{
                                                        html()->text('vd', $spectacle?->vd ?? '')->class('form-control border-0 text-center')->placeholder('VD')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('ipd', $spectacle?->ipd ?? '')->class('form-control border-0 text-center')->placeholder('IPD')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('npd', $spectacle?->npd ?? '')->class('form-control border-0 text-center')->placeholder('NPD')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('rpd', $spectacle?->rpd ?? '')->class('form-control border-0 text-center')->placeholder('RPD')
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        html()->text('lpd', $spectacle?->lpd ?? '')->class('form-control border-0 text-center')->placeholder('LPD')
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6 mt-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label">Notes / Remarks</label>
                                            {{ html()->textarea('notes', $spectacle->notes)->class('form-control')->placeholder('Notes / Remarks') }}
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label class="form-label">Advice</label>
                                            {{ html()->textarea('advice', $spectacle->advice)->class('form-control')->placeholder('Advice') }}
                                        </div>
                                    </div>
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