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
                                @foreach ($role as $item)
                                @if($item->id != 1)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endif
                                @endforeach
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
                    <div class="col-md-6">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="name" id="multicol-first-name" value="{{ $user->name }}" class="form-control" placeholder="John" />
                        <label for="multicol-first-name">Full Name</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="slack_member_id" id="slack_member_id" value="{{ $user->slack_member_id }}" class="form-control" placeholder="UABCDEF123" />
                        <label for="slack_member_id">Slack Member ID</label>
                      </div>
                    </div>
                                        <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                          <select id="user_type" name="user_type"  class="select2 form-select" data-allow-clear="true">
                            <option value="">Select</option>
                            <option value="Agent" @if($user->user_type == 'Agent') selected @endif>Agent</option>
                            <option value="Closer" @if($user->user_type == 'Closer') selected @endif>Closer</option>
                            <option value="Customer_Support" @if($user->user_type == 'Customer_Support') selected @endif>Customer Support</option>
                            <option value="IT" @if($user->user_type == 'IT') selected @endif>IT</option>

                          </select>
                          <label for="user_type">User Type</label>
                        </div>
                      </div>
                        <div class="col-xl-6">
                            <div class="row">
                                <div class="col-md mb-md-0 mb-5">
                                <div class="form-check custom-option custom-option-basic checked">
                                    <label class="form-check-label custom-option-content" for="customRadioTemp1">
                                    <input  class="form-check-input" name="status" type="radio" value="1" id="customRadioTemp1" @if($user->status == 1) checked @endif>
                                    <span class="custom-option-header">
                                        <span class="h6 mb-0">Active User</span>
                                    </span>
                                    </label>
                                </div>
                                </div>
                                <div class="col-md">
                                <div class="form-check custom-option custom-option-basic">
                                    <label class="form-check-label custom-option-content" for="customRadioTemp2">
                                    <input class="form-check-input" name="status" type="radio" value="0" id="customRadioTemp2" @if($user->status == 0) checked @endif>
                                    <span class="custom-option-header">
                                        <span class="h6 mb-0">Deactive User</span>
                                    </span>

                                    </label>
                                </div>
                                </div>
                            </div>

                        </div>
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
