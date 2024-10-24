@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
    rel="stylesheet"
/>
@endsection
@section('content')
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
                <h4 class="py-3 mb-4"><span class="text-muted fw-light">Lead/</span> All</h4>


              <!-- Responsive Datatable -->
              <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title">Leads</h5>
                    <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                      <div class="col-md-4 user_role">&nbsp;</div>
                      <div class="col-md-4 user_plan">&nbsp;</div>
                      <div class="col-md-2 user_status">
                        <a class="dt-button add-new btn btn-primary waves-effect waves-light" tabindex="0" href="{{ route('lead.create') }}" style="color: #fff"><span><i class="mdi mdi-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Lead</span></span></a>
                      </div>
                    </div>
                  </div>
                <div class="card-datatable table-responsive">
                  <table id="recodetable" class="table table-bordered">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Email</th>
                        <th>Category</th>
                        <th>Saler</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($leads as $item)
                          <tr>
                            <td>{{ $loop->index +1 }}</td>
                            <td>{{ $item->business_name_adv}}</td>
                            <td>{{ $item->business_number_adv }}</td>
                            <td>{{ $item->off_email}}</td>
                            <td><span class="badge rounded-pill bg-label-primary me-1">{{ $item->category->name }}</span></td>
                            <td>{{ $item->saler->name }}</td>
                            <td>{{ $item->call_status }}</td>
                            <td>
                                <div class="d-inline-block text-nowrap">
                                    <a href="{{ route('sale.create', $item->id) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect" data-bs-toggle="tooltip" title="Preview"><i class="ri-send-plane-2-line ri-20px"></i></a>
                                    <button
                                    class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                    <div class="dropdown-menu dropdown-menu-end m-0" style=""><a
                                    href="{{ route('lead.edit', $item->id) }}" class="dropdown-item"><i
                                        class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                        {{-- <button type="button" class="btn btn-primary" id="confirm-color">Alert</button> --}}
                                        <a  type="button"
                                        data-id="{{ $item->id }}"
                                        data-route="lead"
                                        data-bs-toggle="modal"
                                        data-bs-target="#basicModal"
                                        class="dropdown-item delete-record"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                </div>
                        </div></td>
                          </tr>

                        @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Email</th>
                        <th>Saler</th>
                        <th>Status</th>
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
