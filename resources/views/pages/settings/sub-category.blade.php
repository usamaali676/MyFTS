@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/settings-enhance.css') }}" />
@endsection
@section('content')
    @php $perm = \App\Helpers\GlobalHelper::modulePermission(auth()->user(), 'subcategory'); @endphp
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="py-3 mb-4"><span class="text-muted fw-light">Settings /</span> Sub Categories</h4>
                @if($perm->create)
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="mdi mdi-plus me-1"></i> Add Sub Category
                    </button>
                @endif
            </div>

            <div class="card">
                <h5 class="card-header">Sub Categories</h5>
                <div class="card-datatable table-responsive">
                    <table id="recodetable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Business Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subCategories as $item)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->businessCategory->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-inline-block text-nowrap">
                                            <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                @if($perm->edit)
                                                    <a href="javascript:;" class="dropdown-item edit-record"
                                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                        data-category-id="{{ $item->business_category_id }}"
                                                        data-bs-toggle="modal" data-bs-target="#editModal"><i
                                                            class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                                @endif
                                                @if($perm->delete)
                                                    <a type="button" data-id="{{ $item->id }}" data-route="subcategory"
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
                    <div class="settings-modal-icon"><i class="mdi mdi-sitemap-outline"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Add Sub Category</h5>
                        <p class="settings-modal-subtitle mb-0">Create a new sub category record</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('subcategory.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating form-floating-outline mb-3">
                            <select name="business_category_id" id="add_category_id" class="form-select select2" required>
                                <option value="" disabled selected>Select a business category</option>
                                @foreach($businessCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <label>Business Category</label>
                        </div>
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
                        <h5 class="modal-title mb-0">Edit Sub Category</h5>
                        <p class="settings-modal-subtitle mb-0">Update this sub category's details</p>
                    </div>
                </div>
                <form method="POST" id="editForm" action="">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating form-floating-outline mb-3">
                            <select name="business_category_id" id="edit_category_id" class="form-select select2" required>
                                @foreach($businessCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <label>Business Category</label>
                        </div>
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
            language: window.rsEmptyStateHTML ? { emptyTable: rsEmptyStateHTML('sub categories') } : undefined
        });

        // The page-wide .select2 auto-init (form-layouts.js) sets
        // dropdownParent to the select's immediate wrapper. Inside our
        // modals that wrapper is clipped by the modal's rounded-corner
        // overflow:hidden, cropping the open dropdown list — so re-init
        // these two with dropdownParent pointing at the modal itself instead.
        $('#add_category_id, #edit_category_id').each(function () {
            const $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }
            $el.select2({
                width: '100%',
                dropdownParent: $el.closest('.modal')
            });
        });

        $('#recodetable').on('click', '.edit-record', function () {
            const id = $(this).data('id');
            $('#edit_name').val($(this).data('name'));
            $('#edit_category_id').val($(this).data('category-id')).trigger('change');
            $('#editForm').attr('action', `{{ url('settings/sub-category/update') }}/${id}`);
        });
    </script>
@endsection
