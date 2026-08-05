@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />
@endsection
@section('content')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="d-flex justify-content-between align-items-center">
                <h4 class="py-3 mb-4"><span class="text-muted fw-light">Trainee/</span> List</h4>
                <a href="{{ route('trainee.create') }}" class="btn btn-primary">Create Trainee</a>
              </div>

              <div class="card">
                <h5 class="card-header">Trainees</h5>
                <div class="card-datatable table-responsive">
                  <table id="recordtable" class="table table-bordered">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Sudo Name</th>
                        <th>Role</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($trainees as $item)
                          <tr>
                            <td>{{ $srno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->sudo_name }}</td>
                            <td>{{ $item->role }}</td>
                            <td>{{ $item->days }}</td>
                            <td>
                                @if($item->status == 'active')
                                    <span class="badge rounded-pill bg-success">Active</span>
                                @elseif($item->status == 'inactive')
                                    <span class="badge rounded-pill bg-danger">Inactive</span>
                                @elseif($item->status == 'suspended')
                                    <span class="badge rounded-pill bg-warning">Suspended</span>
                                @elseif($item->status == 'onBoard')
                                    <span class="badge rounded-pill bg-info">On Board</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>{{ $item->createdBy->name ?? '-' }}</td>
                            <td>
                                <div class="d-inline-block text-nowrap">
                                    <button
                                    class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                    <div class="dropdown-menu dropdown-menu-end m-0">
                                        <a href="{{ route('trainee.edit', $item->id) }}" class="dropdown-item"><i class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                        <a href="javascript:;"
                                        data-id="{{ $item->id }}"
                                        data-route="trainee"
                                        data-bs-toggle="modal"
                                        data-bs-target="#basicModal"
                                        class="dropdown-item delete-record"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                        <a href="{{ route('trainee.show', $item->id) }}" class="dropdown-item"><i class="mdi mdi-eye-outline me-2"></i><span>View Details</span></a>
                                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#markAttendance{{ $item->id }}" class="dropdown-item"><i class="mdi mdi-calendar-check-outline me-2"></i><span>Mark Attendance</span></a>
                                        <a href="{{ route('traineeattendance.index', $item->id) }}" class="dropdown-item"><i class="mdi mdi-calendar-month-outline me-2"></i><span>View Attendance</span></a>
                                        <a href="{{ route('traineecomment.index', $item->id) }}" class="dropdown-item"><i class="mdi mdi-comment-text-outline me-2"></i><span>Comments</span></a>
                                    </div>
                                </div>
                            </td>
                          </tr>

                          <div class="modal fade" id="markAttendance{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Mark Attendance — {{ $item->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('traineeattendance.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="trainee_id" value="{{ $item->id }}">
                                        <input type="hidden" name="date" value="{{ now()->toDateString() }}">
                                        <div class="modal-body">
                                            <p class="mb-3">Mark attendance for today ({{ now()->format('M d, Y') }}):</p>
                                            <div class="d-flex gap-2">
                                                <button type="submit" name="status" value="on_time" class="btn btn-success flex-fill">On-Time</button>
                                                <button type="submit" name="status" value="late" class="btn btn-warning flex-fill">Late</button>
                                                <button type="submit" name="status" value="absent" class="btn btn-danger flex-fill">Absent</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                          </div>
                        @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="content-backdrop fade"></div>
          </div>
@endsection
@section('js')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
    <script>
        $('#recordtable').DataTable();
    </script>
@endsection
