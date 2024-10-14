@extends('layouts.dashboard')
@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Role/</span> Edit</h4>

        <!-- Basic Layout -->
        <div class="row">
            <div class="card p-5">
                <div class="text-center mb-4">
                    <h3 class="role-title mb-2 pb-0">Add New Role</h3>
                    <p>Set role permissions</p>
                </div>
                <!-- Add role form -->
                <form class="row g-3" method="POST" action="{{ route('role.update', $role->id) }}">
                    @csrf
                    <!-- Include this to specify it's a PUT request -->
                    <div class="col-12 mb-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="modalRoleName" name="modalRoleName" class="form-control"
                                placeholder="Enter a role name" value="{{ old('modalRoleName', $role->name) }}"
                                tabindex="-1" />
                            <label for="modalRoleName">Role Name</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <h5>Role Permissions</h5>
                        <div class="table-responsive">
                            <table class="table table-flush-spacing">
                                <tbody>
                                    @foreach ($permissions as $permName)
                                    <tr>
                                        <td class="text-nowrap fw-medium">{{ Str::title($permName) }}</td>
                                        <td>
                                            <div class="d-flex" style="justify-content: space-around;">
                                                <div class="form-check me-3 me-lg-5">
                                                    <input class="form-check-input" type="checkbox" name="permissions[{{ $permName }}][create]" id="create_{{ $permName }}" {{ isset($rolePermissions[$permName]) && $rolePermissions[$permName]->create ? 'checked' : '' }} />
                                                    <label class="form-check-label" for="create_{{ $permName }}"> Create </label>
                                                </div>
                                                <div class="form-check me-3 me-lg-5">
                                                    <input class="form-check-input" type="checkbox" name="permissions[{{ $permName }}][view]" id="view_{{ $permName }}" {{ isset($rolePermissions[$permName]) && $rolePermissions[$permName]->view ? 'checked' : '' }} />
                                                    <label class="form-check-label" for="view_{{ $permName }}"> View </label>
                                                </div>
                                                <div class="form-check me-3 me-lg-5">
                                                    <input class="form-check-input" type="checkbox" name="permissions[{{ $permName }}][edit]" id="edit_{{ $permName }}" {{ isset($rolePermissions[$permName]) && $rolePermissions[$permName]->edit ? 'checked' : '' }} />
                                                    <label class="form-check-label" for="edit_{{ $permName }}"> Edit </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[{{ $permName }}][delete]" id="delete_{{ $permName }}" {{ isset($rolePermissions[$permName]) && $rolePermissions[$permName]->delete ? 'checked' : '' }} />
                                                    <label class="form-check-label" for="delete_{{ $permName }}"> Delete </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                </form>

                <!--/ Add role form -->
            </div>
        </div>
    </div>
</div>
@endsection
