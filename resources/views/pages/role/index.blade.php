@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-1">Roles List</h4>
    <p class="mb-4">
        A role provided access to predefined menus and features so that depending on assigned role an
        administrator can have access to what user needs.
    </p>
    <!-- Role cards -->
    <div class="row g-4">
        @foreach ($roles as $item)
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <p>Total {{ $item->users_count }} users</p>
                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                            @foreach ($item->users as $user)
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="{{ $user->name }}" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/5.png" alt="Avatar" />
                            </li>
                            @endforeach

                            {{-- <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Allen Rieske" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/12.png" alt="Avatar" />
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Julee Rossignol" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/6.png" alt="Avatar" />
                            </li>
                            <li class="avatar">
                                <span class="avatar-initial rounded-circle pull-up bg-lighter text-body"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="3 more">+3</span>
                            </li> --}}
                        </ul>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="role-heading">
                            <h4 class="mb-1 text-body">{{ $item->name }}</h4>
                            <a href="{{ route('role.edit', $item->id) }}"
                                class="role-edit-modal"><span>Edit Role</span></a>
                        </div>
                        <a href="javascript:void(0);" class="text-muted"><i
                                class="mdi mdi-content-copy mdi-20px"></i></a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        {{-- <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <p>Total 7 users</p>
                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Jimmy Ressula" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/4.png" alt="Avatar" />
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="John Doe" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/1.png" alt="Avatar" />
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Kristi Lawker" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/2.png" alt="Avatar" />
                            </li>
                            <li class="avatar">
                                <span class="avatar-initial rounded-circle pull-up bg-lighter text-body"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="3 more">+3</span>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="role-heading">
                            <h4 class="mb-1 text-body">Manager</h4>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal"
                                class="role-edit-modal"><span>Edit Role</span></a>
                        </div>
                        <a href="javascript:void(0);" class="text-muted"><i
                                class="mdi mdi-content-copy mdi-20px"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <p>Total 5 users</p>
                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Andrew Tye" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/6.png" alt="Avatar" />
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Rishi Swaat" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/9.png" alt="Avatar" />
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Rossie Kim" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/12.png" alt="Avatar" />
                            </li>
                            <li class="avatar">
                                <span class="avatar-initial rounded-circle pull-up bg-lighter text-body"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="3 more">+3</span>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="role-heading">
                            <h4 class="mb-1 text-body">Users</h4>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal"
                                class="role-edit-modal"><span>Edit Role</span></a>
                        </div>
                        <a href="javascript:void(0);" class="text-muted"><i
                                class="mdi mdi-content-copy mdi-20px"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <p>Total 3 users</p>
                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Kim Karlos" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/3.png" alt="Avatar" />
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Katy Turner" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/9.png" alt="Avatar" />
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Peter Adward" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/15.png" alt="Avatar" />
                            </li>
                            <li class="avatar">
                                <span class="avatar-initial rounded-circle pull-up bg-lighter text-body"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="3 more">+3</span>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="role-heading">
                            <h4 class="mb-1 text-body">Support</h4>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal"
                                class="role-edit-modal"><span>Edit Role</span></a>
                        </div>
                        <a href="javascript:void(0);" class="text-muted"><i
                                class="mdi mdi-content-copy mdi-20px"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <p>Total 2 users</p>
                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Kim Merchent" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/10.png" alt="Avatar" />
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Sam D'souza" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/13.png" alt="Avatar" />
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                title="Nurvi Karlos" class="avatar pull-up">
                                <img class="rounded-circle" src="../../assets/img/avatars/15.png" alt="Avatar" />
                            </li>
                            <li class="avatar">
                                <span class="avatar-initial rounded-circle pull-up bg-lighter text-body"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="3 more">+3</span>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="role-heading">
                            <h4 class="mb-1 text-body">Restricted User</h4>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal"
                                class="role-edit-modal"><span>Edit Role</span></a>
                        </div>
                        <a href="javascript:void(0);" class="text-muted"><i
                                class="mdi mdi-content-copy mdi-20px"></i></a>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card h-100">
                <div class="row h-100">
                    <div class="col-5">
                        <div class="d-flex align-items-end h-100 justify-content-center">
                            <img src="../../assets/img/illustrations/add-new-role-illustration.png" class="img-fluid"
                                alt="Image" width="70" />
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="card-body text-sm-end text-center ps-sm-0">
                            <button data-bs-target="#addRoleModal" data-bs-toggle="modal"
                                class="btn btn-primary mb-3 text-nowrap add-new-role">
                                Add Role
                            </button>
                            <p class="mb-0">Add role, if it does not exist</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="fw-medium mb-1 mt-5">Total users with their roles</h4>
        <p class="mb-0 mt-1">Find all of your company’s administrator accounts and their associate roles.</p>

        <div class="col-12">
            <!-- Role Table -->
            <div class="card">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <h5 class="card-header">Roles</h5>
                <div class="card-datatable table-responsive">
                    <table id="recodetable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($role as $item)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <div class="d-inline-block text-nowrap"><button
                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                        <div class="dropdown-menu dropdown-menu-end m-0" style=""><a
                                                href="{{ route('role.edit', $item->id) }}" class="dropdown-item"><i
                                                    class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                                    {{-- <button type="button" class="btn btn-primary" id="confirm-color">Alert</button> --}}
                                                    <a type="button"
                                                    data-id="{{ $item->id }}"
                                                    data-route="role"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#basicModal"  class="dropdown-item delete-record" class="dropdown-item delete-record"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!--/ Role Table -->
        </div>

    </div>
    <!--/ Role cards -->

    <!-- Add Role Modal -->
    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body p-md-0">
                    <div class="text-center mb-4">
                        <h3 class="role-title mb-2 pb-0">Add New Role</h3>
                        <p>Set role permissions</p>
                    </div>
                    <!-- Add role form -->
                    <form class="row g-3" method="POST" action="{{ route('role.store') }}">
                        @csrf
                        <div class="col-12 mb-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="modalRoleName" name="modalRoleName" class="form-control"
                                    placeholder="Enter a role name" tabindex="-1" />
                                <label for="modalRoleName">Role Name</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <h5>Role Permissions</h5>
                            <!-- Permission table -->
                            <div class="table-responsive">
                                <table class="table table-flush-spacing">
                                    <tbody>
                                        <tr>
                                            <td class="text-nowrap fw-medium">
                                                Administrator
                                                <i class="mdi mdi-information-outline" data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Allows a full access to the system"></i>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAll" />
                                                    <label class="form-check-label" for="selectAll"> Select All </label>
                                                </div>
                                            </td>
                                        </tr>
                                        @foreach ($permissions as $perms)
                                        <tr>
                                            <td class="text-nowrap fw-medium">{{ Str::title($perms) }}</td>
                                            <td>
                                                <div class="d-flex" style="justify-content: space-around;">
                                                    <div class="form-check me-3 me-lg-5">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="permissions[{{ $perms }}][create]"
                                                            id="create_{{ $perms }}"  />
                                                        <label class="form-check-label" for="create_{{ $perms }}">
                                                            Create </label>
                                                    </div>
                                                    <div class="form-check me-3 me-lg-5">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="permissions[{{ $perms }}][view]"
                                                            id="view_{{ $perms }}"  />
                                                        <label class="form-check-label" for="view_{{ $perms }}"> View
                                                        </label>
                                                    </div>
                                                    <div class="form-check me-3 me-lg-5">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="permissions[{{ $perms }}][edit]"
                                                            id="edit_{{ $perms }}"  />
                                                        <label class="form-check-label" for="edit_{{ $perms }}"> Edit
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="permissions[{{ $perms }}][delete]"
                                                            id="delete_{{ $perms }}"  />
                                                        <label class="form-check-label" for="delete_{{ $perms }}">
                                                            Delete </label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach

                                        {{-- @foreach ($permissions as $perms)
                                        <tr>
                                            <td class="text-nowrap fw-medium">{{Str::title($perms) }}</td>
                                            <td>
                                                <div class="d-flex" style="justify-content: space-around;">
                                                    <div class="form-check me-3 me-lg-5">
                                                        <input class="form-check-input" type="checkbox" name="create"
                                                            id="create_{{ $perms }}" />
                                                        <label class="form-check-label" for="create_{{ $perms }}">
                                                            Create </label>
                                                    </div>
                                                    <div class="form-check me-3 me-lg-5">
                                                        <input class="form-check-input" type="checkbox" name="view"
                                                            id="view_{{ $perms }}" />
                                                        <label class="form-check-label" for="view_{{ $perms }}"> View
                                                        </label>
                                                    </div>
                                                    <div class="form-check me-3 me-lg-5">
                                                        <input class="form-check-input" type="checkbox" name="edit"
                                                            id="edit_{{ $perms }}" />
                                                        <label class="form-check-label" for="edit_{{ $perms }}"> Edit
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="delete"
                                                            id="delete_{{ $perms }}" />
                                                        <label class="form-check-label" for="delete_{{ $perms }}">
                                                            Delete </label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach --}}


                                    </tbody>
                                </table>
                            </div>
                            <!-- Permission table -->
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </form>
                    <!--/ Add role form -->
                </div>
            </div>
        </div>
    </div>
    <!--/ Add Role Modal -->

    <!-- / Add Role Modal -->
    {{-- <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true"> --}}

    <!-- Edit Role Modal -->
   {{-- <div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body p-md-0">
                    <div class="text-center mb-4">
                        <h3 class="role-title mb-2 pb-0">Edit Role</h3>
                        <p>Set role permissions</p>
                    </div>
                    <!-- Add role form -->
                    <form class="row g-3" method="POST" action="{{ route('role.update', $role->id) }}">
                        @csrf
                        @method('PUT') <!-- Include this to specify it's a PUT request -->
                        <div class="col-12 mb-4">
                            <div class="form-floating form-floating-outline">
                                <input
                                    type="text"
                                    id="modalRoleName"
                                    name="modalRoleName"
                                    class="form-control"
                                    placeholder="Enter a role name"
                                    value="{{ old('modalRoleName', $role->name) }}"
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
                                                            <input class="form-check-input" type="checkbox" name="permissions[{{ $permName }}][create]" id="create_{{ $permName }}" {{ $role->permissions[$permName]['create'] ? 'checked' : '' }} />
                                                            <label class="form-check-label" for="create_{{ $permName }}"> Create </label>
                                                        </div>
                                                        <div class="form-check me-3 me-lg-5">
                                                            <input class="form-check-input" type="checkbox" name="permissions[{{ $permName }}][view]" id="view_{{ $permName }}" {{ $role->permissions[$permName]['view'] ? 'checked' : '' }} />
                                                            <label class="form-check-label" for="view_{{ $permName }}"> View </label>
                                                        </div>
                                                        <div class="form-check me-3 me-lg-5">
                                                            <input class="form-check-input" type="checkbox" name="permissions[{{ $permName }}][edit]" id="edit_{{ $permName }}" {{ $role->permissions[$permName]['edit'] ? 'checked' : '' }} />
                                                            <label class="form-check-label" for="edit_{{ $permName }}"> Edit </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permissions[{{ $permName }}][delete]" id="delete_{{ $permName }}" {{ $role->permissions[$permName]['delete'] ? 'checked' : '' }} />
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
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>

                    <!--/ Add role form -->
                </div>
            </div>
        </div>
    </div>  --}}
    <!--/ Edit Role Modal -->
</div>
@endsection
@section('js')
<script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach((checkbox) => {
        checkbox.checked = this.checked;
    });
});

</script>

<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script>
    $('#recodetable').DataTable();

</script>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-record');
        const deleteConfirmButton = document.getElementById('deleteConfirmButton');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const deleteUrl = "{{ route('role.delete', ':id') }}".replace(':id', itemId);
                deleteConfirmButton.setAttribute('href', deleteUrl);
            });
        });
    });
</script> --}}

@endsection
