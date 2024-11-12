@extends('layouts.dashboard')
@section('content')
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="py-3 mb-4"><span class="text-muted fw-light">User/</span> Create</h4>

              <!-- Basic Layout -->


              <!-- Multi Column with Form Separator -->

              <div class="card mb-4">
                <form class="card-body" action="{{ route('user.update', $user->id ) }}" method="POST">
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
                  <h6>1. Account Details</h6>
                  <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                          <select id="role" name="role_id"  class="select2 form-select" data-allow-clear="true">
                            <option value="">Select</option>
                            @if(isset($user->role_id))
                                <option value="{{$user->role_id}}" selected>{{$user->role->name}}</option>
                            @else
                                @foreach ($role as $item)
                                @if($item->id != 1)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endif
                                @endforeach
                            @endif

                          </select>
                          <label for="role">Role</label>
                        </div>
                      </div>
                    <div class="col-md-6">
                      <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="multicol-email"
                            class="form-control"
                            name="email"
                            placeholder="john.doe"
                            aria-label="john.doe"
                            value="{{ $user->email }}"
                            aria-describedby="multicol-email2" />
                          <label for="multicol-email">Email</label>
                        </div>
                        <span class="input-group-text" id="multicol-email2">@example.com</span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-password-toggle">
                        <div class="input-group input-group-merge">
                          <div class="form-floating form-floating-outline">
                            <input
                              type="password"
                              id="multicol-password"
                              name="password"
                              class="form-control"
                              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                              aria-describedby="multicol-password2" />
                            <label for="multicol-password">Password</label>
                          </div>
                          <span class="input-group-text cursor-pointer" id="multicol-password2"
                            ><i class="mdi mdi-eye-off-outline"></i
                          ></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-password-toggle">
                        <div class="input-group input-group-merge">
                          <div class="form-floating form-floating-outline">
                            <input
                              type="password"
                              id="multicol-confirm-password"
                              class="form-control"
                              name="password_confirmation"
                              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                              aria-describedby="multicol-confirm-password2" />
                            <label for="multicol-confirm-password">Confirm Password</label>
                          </div>
                          <span class="input-group-text cursor-pointer" id="multicol-confirm-password2"
                            ><i class="mdi mdi-eye-off-outline"></i
                          ></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <hr class="my-4 mx-n4" />
                  <h6>2. Personal Info</h6>
                  <div class="row g-4">
                    <div class="col-md-12">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="name" id="multicol-first-name" value="{{ $user->name }}" class="form-control" placeholder="John" />
                        <label for="multicol-first-name">Full Name</label>
                      </div>
                    </div>
                    {{-- <div class="col-md-6">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="l_name" id="multicol-last-name" class="form-control" placeholder="Doe" />
                        <label for="multicol-last-name">Last Name</label>
                      </div>
                    </div> --}}
                  </div>
                  <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                  </div>
                </form>
              </div>


              <!-- Collapsible Section -->

              <!-- Form with Tabs -->


              <!-- Form Alignment -->

            </div>
            <!-- / Content -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
@endsection
