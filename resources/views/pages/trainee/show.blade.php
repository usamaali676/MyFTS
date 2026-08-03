@extends('layouts.dashboard')

@section('content')
  <div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
          <h4 class="mb-1"><span class="text-muted fw-light">Trainee /</span> Details</h4>
          <p class="text-muted mb-0">View trainee profile and additional information.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('trainee.edit', $trainee->id) }}" class="btn btn-primary">
            <i class="mdi mdi-pencil-outline me-1"></i> Edit Trainee
          </a>
          <a href="{{ route('trainee.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back
          </a>
        </div>
      </div>

      <div class="card mb-4 overflow-hidden">
        <div class="card-body p-0">
          <div class="p-4 p-md-5 bg-primary">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 text-white">
              <div class="avatar avatar-xl">
                <span class="avatar-initial rounded-circle bg-white text-primary fw-bold fs-3">
                  {{ strtoupper(substr($trainee->name ?? 'T', 0, 1)) }}
                </span>
              </div>
              <div class="flex-grow-1">
                <h3 class="text-white mb-1">{{ $trainee->name ?: 'Trainee' }}</h3>
                <p class="mb-0 opacity-75">
                  <i class="mdi mdi-briefcase-outline me-1"></i>{{ $trainee->role ?: 'Role not assigned' }}
                  <span class="mx-2">•</span>
                  {{ $trainee->sudo_name ?: 'No sudo name' }}
                </p>
              </div>
              <div>
                @if($trainee->status === 'active')
                  <span class="badge rounded-pill bg-success px-3 py-2">Active</span>
                @elseif($trainee->status === 'inactive')
                  <span class="badge rounded-pill bg-danger px-3 py-2">Inactive</span>
                @elseif($trainee->status === 'suspended')
                  <span class="badge rounded-pill bg-warning px-3 py-2">Suspended</span>
                @elseif($trainee->status === 'onBoard')
                  <span class="badge rounded-pill bg-info px-3 py-2">On Board</span>
                @else
                  <span class="badge rounded-pill bg-secondary px-3 py-2">N/A</span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center">
              <i class="mdi mdi-account-details-outline mdi-24px text-primary me-2"></i>
              <h5 class="mb-0">Trainee Information</h5>
            </div>
            <div class="table-responsive">
              <table class="table table-borderless mb-0">
                <tbody>
                  <tr class="border-top">
                    <th scope="row" class="ps-4 py-3 text-muted fw-medium"><i class="mdi mdi-account-outline me-2"></i>Full Name</th>
                    <td class="py-3 pe-4 fw-medium">{{ $trainee->name ?: '-' }}</td>
                  </tr>
                  <tr>
                    <th scope="row" class="ps-4 py-3 text-muted fw-medium"><i class="mdi mdi-account-tag-outline me-2"></i>Sudo Name</th>
                    <td class="py-3 pe-4">{{ $trainee->sudo_name ?: '-' }}</td>
                  </tr>
                  <tr>
                    <th scope="row" class="ps-4 py-3 text-muted fw-medium"><i class="mdi mdi-briefcase-outline me-2"></i>Role</th>
                    <td class="py-3 pe-4">{{ $trainee->role ?: '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center">
              <i class="mdi mdi-history mdi-24px text-primary me-2"></i>
              <h5 class="mb-0">Record Activity</h5>
            </div>
            <div class="card-body">
              <div class="d-flex gap-3 mb-4">
                <div class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-primary"><i class="mdi mdi-account-plus-outline"></i></span></div>
                <div>
                  <p class="mb-1 fw-medium">Created by</p>
                  <span class="text-muted">{{ $trainee->createdBy->name ?? '-' }}</span>
                </div>
              </div>
              <div class="d-flex gap-3 mb-4">
                <div class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-success"><i class="mdi mdi-calendar-plus-outline"></i></span></div>
                <div>
                  <p class="mb-1 fw-medium">Created at</p>
                  <span class="text-muted">{{ $trainee->created_at ? $trainee->created_at->format('M d, Y h:i A') : '-' }}</span>
                </div>
              </div>
              <div class="d-flex gap-3">
                <div class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-info"><i class="mdi mdi-update"></i></span></div>
                <div>
                  <p class="mb-1 fw-medium">Last updated</p>
                  <span class="text-muted">{{ $trainee->updated_at ? $trainee->updated_at->format('M d, Y h:i A') : '-' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-body p-0">
          <div class="row g-0">
            {{-- <div class="col-md-3 col-lg-2 d-flex align-items-center p-4 border-end">

            </div> --}}
            <div class="col-md-12 col-lg-12 p-4">
                <div style="display: flex;">
                <i class="mdi mdi-information-outline mdi-24px text-primary d-block mb-2"></i>
                <h6 class="mb-0 text-uppercase text-muted" style="padding-top: 5px">&nbsp;Additional Info</h6>
              </div>
              <div class="text-break" style="min-height: 100%; white-space: pre-wrap;">{!! $trainee->additional_info ? e($trainee->additional_info) : '<span class="text-muted">No additional information available.</span>' !!}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content-backdrop fade"></div>
  </div>
@endsection
