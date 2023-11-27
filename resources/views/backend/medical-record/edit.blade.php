@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Medical Record</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Medical Record</li>
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
                        <h5>Medical Record</h5><span>Update Medical Record</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('mrecord.update', $mrecord->id) }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label">Patient Name</label>
                                    {{ html()->text($name = 'pname', $value = $consultation->patient->name)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Patient ID</label>
                                    {{ html()->text($name = 'pid', $value = $consultation->patient->patient_id)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">MRN</label>
                                    {{ html()->text($name = 'mrn', $value = $consultation->mrn)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Doctor</label>
                                    {{ html()->text($name = 'pmobile', $value = $consultation->doctor->name)->class('form-control')->attribute('disabled') }}
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label req">Symptoms</label><small>(Allowed Multiple)</small>
                                    {{ html()->text($name = 'symptoms', $value = $mrecord->symptoms()->pluck('name')->implode(','))->class('form-control')->attribute('data-role', 'tagsinput')->placeholder('Symptoms') }}
                                    @error('symptoms')
                                    <small class="text-danger">{{ $errors->first('symptoms') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Patient History</label>
                                    {{ html()->textarea($name = 'patient_history', $value = $mrecord->patient_history)->class('form-control')->attribute('rows', 5)->placeholder('Patient History if any') }}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Is allergic to any drugs?</label>
                                    {{ html()->textarea($name = 'allergic_drugs', $value = $mrecord->allergic_drugs)->class('form-control')->attribute('rows', 5)->placeholder('Details') }}
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label req">Diagnoses</label><small>(Allowed Multiple)</small>
                                    {{ html()->text($name = 'diagnosis', $value = $mrecord->diagnoses()->pluck('name')->implode(','))->class('form-control')->attribute('data-role', 'tagsinput')->placeholder('Diagnosis') }}
                                    @error('diagnosis')
                                    <small class="text-danger">{{ $errors->first('diagnosis') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label req">Doctor Recommondations / Advice</label>
                                    {{ html()->textarea($name = 'doctor_recommondation', $value = $mrecord->doctor_recommondation)->class('form-control')->attribute('rows', 5)->placeholder('Doctor Recommondations / Advice') }}
                                    @error('doctor_recommondation')
                                    <small class="text-danger">{{ $errors->first('doctor_recommondation') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <label class="form-label fw-bold">VISION</label>
                                    <table class="table table-bordered table-sm mt-3">
                                        <thead class="text-center">
                                            <tr>
                                                <th>EYE</th>
                                                <th>SPH</th>
                                                <th>CYL</th>
                                                <th>AXIS</th>
                                                <th>ADD</th>
                                                <th>VA</th>
                                                <th>NV</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>RE</td>
                                                <td><input type="text" name="re_sph" class="border-0 text-center" maxlength="6" placeholder="SPH" value="{{ $mrecord->vision?->re_sph }}"></td>
                                                <td><input type="text" name="re_cyl" class="border-0 text-center" maxlength="6" placeholder="CYL" value="{{ $mrecord->vision?->re_cyl }}"></td>
                                                <td><input type="text" name="re_axis" class="border-0 text-center" maxlength="6" placeholder="AXIS" value="{{ $mrecord->vision?->re_axis }}"></td>
                                                <td><input type="text" name="re_add" class="border-0 text-center" maxlength="6" placeholder="ADD" value="{{ $mrecord->vision?->re_add }}"></td>
                                                <td><input type="text" name="re_va" class="border-0 text-center" maxlength="6" placeholder="VA" value="{{ $mrecord->vision?->re_va }}"></td>
                                                <td><input type="text" name="re_nv" class="border-0 text-center" maxlength="6" placeholder="NV" value="{{ $mrecord->vision?->re_nv }}"></td>
                                            </tr>
                                            <tr>
                                                <td>LE</td>
                                                <td><input type="text" name="le_sph" class="border-0 text-center" maxlength="6" placeholder="SPH" value="{{ $mrecord->vision?->le_sph }}"></td>
                                                <td><input type="text" name="le_cyl" class="border-0 text-center" maxlength="6" placeholder="CYL" value="{{ $mrecord->vision?->le_cyl }}"></td>
                                                <td><input type="text" name="le_axis" class="border-0 text-center" maxlength="6" placeholder="AXIS" value="{{ $mrecord->vision?->le_axis }}"></td>
                                                <td><input type="text" name="le_add" class="border-0 text-center" maxlength="6" placeholder="ADD" value="{{ $mrecord->vision?->le_add }}"></td>
                                                <td><input type="text" name="le_va" class="border-0 text-center" maxlength="6" placeholder="VA" value="{{ $mrecord->vision?->le_va }}"></td>
                                                <td><input type="text" name="le_nv" class="border-0 text-center" maxlength="6" placeholder="NV" value="{{ $mrecord->vision?->le_nv }}"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-6 mt-5"><label class="form-label fw-bold">MEDICINE</label></div>
                                <div class="col-6 text-end mt-5">
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm" type="button" onclick="addMedicineRow('pharmacy', 'type')">Add New Medicine</button>
                                    </div>
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-bordered table-stripped table-sm">
                                        <thead class="text-center">
                                            <tr>
                                                <th>Remove</th>
                                                <th>Type</th>
                                                <th width="25%">Medicine</th>
                                                <th>Dosage</th>
                                                <th>Duration</th>
                                                <th>Qty</th>
                                                <th>Eye</th>
                                                <th width="15%">Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody class="medicineRow">
                                            @forelse($mrecord->medicines as $key => $item)
                                            <tr>
                                                <td class="text-center">
                                                    <a href="javascript:void(0)" class="dltRow"><i class="fa fa-trash text-danger"></i></a>
                                                </td>
                                                <td>
                                                    {{ html()->select('product_type[]', $types, $item->product_type)->class('select2 selPdctType')->attribute('id', '')->placeholder('Select') }}
                                                </td>
                                                <td>
                                                    {{ html()->select('product_id[]', $products, $item->product_id)->class('select2 selPdct')->attribute('id', '')->placeholder('Select') }}
                                                </td>
                                                <td>
                                                    {{ html()->text('dosage[]', $item->dosage)->class('border-0 w-100')->attribute('id', '')->placeholder('Dosage')}}
                                                </td>
                                                <td>
                                                    {{ html()->text('duration[]', $item->duration)->class('border-0 w-100')->attribute('id', '')->placeholder('Duration')}}
                                                </td>
                                                <td>
                                                    {{ html()->number('qty[]', $item->qty, '', '', 'any')->class('border-0 w-100 text-end')->attribute('id', '')->placeholder('0')}}
                                                </td>
                                                <td>
                                                    {{ html()->select('eye[]', array('left' => 'Left', 'right' => 'Right', 'both' =>'Both'), $item->eye)->class('select2 selEye')->attribute('id', '')->placeholder('Select') }}
                                                </td>
                                                <td>
                                                    {{ html()->text('notes[]', $item->notes)->class('border-0 w-100')->attribute('id', '')->placeholder('Notes')}}
                                                </td>
                                            </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-2 mt-5">
                                    <label class="form-label">Surgery Advised?</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="flexSwitchCheckDefault" type="checkbox" role="switch" name="surgery_advised" value="1" {{ ($mrecord->surgery_advised == 1) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-5">
                                    <label class="form-label">Next Review Date</label>
                                    {{ html()->date($name = 'review_date', $value = $mrecord->review_date?->format('Y-m-d'))->class('form-control')->placeholder(date('Y-m-d')) }}
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