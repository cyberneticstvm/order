@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>User</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                            <svg class="stroke-icon">
                                <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item">User</li>
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
                    <h5>Update User</h5><span>Update User</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('user.update', $user->id) }}">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label req">Full Name</label>
                                    {{ html()->text($name = 'name', $value = $user->name)->class('form-control')->placeholder('Full Name') }}
                                    @error('name')
                                        <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Username</label>
                                    {{ html()->text($name = 'username', $value = $user->username)->class('form-control')->placeholder('Username') }}
                                    @error('username')
                                        <small class="text-danger">{{ $errors->first('username') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Email</label>
                                    {{ html()->email($name = 'email', $value = $user->email)->class('form-control')->placeholder('Email') }}
                                    @error('email')
                                        <small class="text-danger">{{ $errors->first('email') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Mobile Number</label>
                                    {{ html()->text($name = 'mobile', $value = $user->mobile)->class('form-control')->maxlength(10)->placeholder('Mobile') }}
                                    @error('mobile')
                                        <small class="text-danger">{{ $errors->first('mobile') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label req">Branch <small>(Multiple selection enabled)</small></label>
                                    {{ html()->select($name = 'branches[]', $value = branches()->pluck('name', 'id'), $user->branches->pluck('branch_id'))->class('form-control select2')->multiple() }}
                                    @error('branches')
                                        <small class="text-danger">{{ $errors->first('branches') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Role</label>
                                    {{ html()->select($name = 'roles', $value = $roles, $userRole)->class('form-control select2')->placeholder('Select Role') }}
                                    @error('roles')
                                        <small class="text-danger">{{ $errors->first('roles') }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label req">Password</label>
                                    {{ html()->password($name = 'password', $value = NULL)->class('form-control')->placeholder('******') }}
                                    @error('password')
                                        <small class="text-danger">{{ $errors->first('password') }}</small>
                                    @enderror
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