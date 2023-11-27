@extends("backend.base")
@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Role</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                            <svg class="stroke-icon">
                                <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item">Role</li>
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
                    <h5>Update Role</h5><span>Update Existing Role</span>
                    </div>
                    <div class="card-body">
                        <div class="card-wrapper">
                            <form class="row g-3" method="post" action="{{ route('role.update', $role->id) }}">
                                @csrf
                                <div class="col-md-12">
                                    <label class="form-label req">Role Name</label>
                                    {{ html()->text($name = 'name', $value = $role->name)->class('form-control')->placeholder('Role Name') }}
                                    @error('name')
                                        <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @enderror
                                </div>
                                <div class="row g-3">
                                    @forelse($permissions as $key => $permission)
                                    <div class="col-2 form-check ">
                                        {{ html()->checkbox($name = 'permission[]', in_array($permission->id, $rolePermissions) ? true : false, $value = $permission->id)->class('form-check-input') }}
                                        <label class="form-check-label">{{ $permission->name }}</label>
                                    </div>                                    
                                    @empty
                                    @endforelse
                                    @error('permission')
                                        <small class="text-danger">{{ $errors->first('permission') }}</small>
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