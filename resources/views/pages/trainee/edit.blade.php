@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/trainee-enhance.css') }}" />
@endsection
@section('content')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y trainee-enhanced">
              <h4 class="py-3 mb-4"><span class="text-muted fw-light">Trainee/</span> Edit</h4>

              <div class="card mb-4">
                <form class="card-body" action="{{ route('trainee.update', $trainee->id) }}" method="POST">
                  @csrf
                  @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                  @endif
                  <div class="row g-4">
                    <div class="col-md-6">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="name" id="name" value="{{ old('name', $trainee->name) }}" class="form-control" placeholder="Alex Trainee" />
                        <label for="name">Full Name</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="sudo_name" id="sudo_name" value="{{ old('sudo_name', $trainee->sudo_name) }}" class="form-control" placeholder="Alex Trainee" />
                        <label for="sudo_name">Sudo Name</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="role" id="role" value="{{ old('role', $trainee->role) }}" class="form-control" placeholder="TSR" />
                        <label for="role">Role</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" name="status" class="select2 form-select" data-allow-clear="true" required>
                                        <option value="">Please Select</option>

                                        <option value="active" {{ $trainee->status == 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>

                                        <option value="inactive" {{ $trainee->status == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>

                                        <option value="suspended" {{ $trainee->status == 'suspended' ? 'selected' : '' }}>
                                            Suspended
                                        </option>

                                        <option value="onBoard" {{ $trainee->status == 'onBoard' ? 'selected' : '' }}>
                                            On Board
                                        </option>
                                    </select>
                                    <label for="multicol-country">Status</label>
                                </div>
                    </div>
                    <div class="col-12">
                      <div class="form-floating form-floating-outline">
                        <textarea name="additional_info" id="additional_info" class="form-control" style="height: 100px" placeholder="Notes">{{ old('additional_info', $trainee->additional_info) }}</textarea>
                        <label for="additional_info">Additional Info</label>
                      </div>
                    </div>
                  </div>
                  <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <a href="{{ route('trainee.index') }}" class="btn btn-outline-secondary">Cancel</a>
                  </div>
                </form>
              </div>
            </div>
            <div class="content-backdrop fade"></div>
          </div>
@endsection
