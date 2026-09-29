@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/settings-enhance.css') }}" />
@endsection
@section('content')
    @php $perm = \App\Helpers\GlobalHelper::modulePermission(auth()->user(), 'zelleaccount'); @endphp
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="py-3 mb-4"><span class="text-muted fw-light">Settings /</span> Zelle Accounts</h4>
                @if($perm->create)
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="mdi mdi-plus me-1"></i> Add Zelle Account
                    </button>
                @endif
            </div>

            <div class="card">
                <h5 class="card-header">Zelle Accounts</h5>
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
                            @foreach($zelleAccounts as $item)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="d-inline-block text-nowrap">
                                            <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                @if($perm->edit)
                                                    <a href="javascript:;" class="dropdown-item edit-record"
                                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                        data-bs-toggle="modal" data-bs-target="#editModal"><i
                                                            class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                                @endif
                                                @if($perm->delete)
                                                    <a type="button" data-id="{{ $item->id }}" data-route="zelleaccount"
                                                        data-bs-toggle="modal" data-bs-target="#basicModal"
                                                        class="dropdown-item delete-record"><i
                                                            class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content settings-modal">
                <button type="button" class="btn-close settings-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-header settings-modal-header">
                    <div class="settings-modal-icon"><i class="mdi mdi-bank-transfer"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Add Zelle Account</h5>
                        <p class="settings-modal-subtitle mb-0">Create a new Zelle account record</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('zelleaccount.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="name" class="form-control" placeholder="Enter a name" required />
                            <label>Name</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary settings-modal-save">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content settings-modal">
                <button type="button" class="btn-close settings-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-header settings-modal-header">
                    <div class="settings-modal-icon"><i class="mdi mdi-pencil-outline"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Edit Zelle Account</h5>
                        <p class="settings-modal-subtitle mb-0">Update this Zelle account's details</p>
                    </div>
                </div>
                <form method="POST" id="editForm" action="">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="Enter a name" required />
                            <label>Name</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary settings-modal-save">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
    <script>
        $('#recodetable').DataTable({
            language: window.rsEmptyStateHTML ? { emptyTable: rsEmptyStateHTML('zelle accounts') } : undefined
        });

        $('#recodetable').on('click', '.edit-record', function () {
            const id = $(this).data('id');
            $('#edit_name').val($(this).data('name'));
            $('#editForm').attr('action', `{{ url('settings/zelle-account/update') }}/${id}`);
        });
    </script>
@endsection
