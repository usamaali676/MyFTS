@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection
@section('content')
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="py-3 mb-4"> Users</h4>


              <!-- Responsive Datatable -->
              <div class="card">
                <h5 class="card-header">Responsive Datatable</h5>
                <div class="card-datatable table-responsive">
                  <table id="recodetable" class="table table-bordered">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $item)
                          <tr>
                            <td>{{ $srno ++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->role->name }}</td>
                            <td>
                                <div class="d-inline-block text-nowrap">
                                    <button
                                    class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                    <div class="dropdown-menu dropdown-menu-end m-0" style=""><a
                                    href="{{ route('user.edit', $item->id) }}" class="dropdown-item"><i
                                        class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                        {{-- <button type="button" class="btn btn-primary" id="confirm-color">Alert</button> --}}
                                        <a  type="button"
                                        data-id="{{ $item->id }}"
                                        data-route="user"
                                        data-bs-toggle="modal"
                                        data-bs-target="#basicModal"
                                        class="dropdown-item delete-record"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                </div>
                            </td>
                          </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <!--/ Responsive Datatable -->
            </div>
            <!-- / Content -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
@endsection
@section('js')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
<script>
    $('#recodetable').DataTable();

</script>
@endsection
