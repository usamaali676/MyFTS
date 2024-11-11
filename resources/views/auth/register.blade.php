@extends('layouts.app')

@section('content')


<div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-4">
        <!-- Register Card -->
        <div class="card p-2">
          <!-- Logo -->
          <div class="app-brand justify-content-center mt-5">
            <a href="index.html" class="app-brand-link gap-2">
              <span class="app-brand-logo demo">
                <span style="color: var(--bs-primary)">
                    <img style="height: 40px; width: 60px" src="{{ asset('assets/img/favicon/fts-logo.svg') }}" alt="">
                </span>
              </span>
              <span class="app-brand-text demo text-heading fw-bold">FTS CRM</span>
            </a>
          </div>
          <!-- /Logo -->
          <div class="card-body mt-2">
            <h4 class="mb-2">Adventure starts here 🚀</h4>
            {{-- <p class="mb-4">Make your app management easy and fun!</p> --}}

            <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('register') }}" >
                @csrf
                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif
              <div class="form-floating form-floating-outline mb-3">
                <input
                  type="text"
                  class="form-control"
                  id="username"
                  name="name"
                  placeholder="Enter your username"
                  autofocus />
                <label for="username">Username</label>
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
              </div>
              <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" />
                <label for="email">Email</label>
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password" />
                    <label for="password">Password</label>
                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                  </div>
                  <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>

              <div class="mb-3 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password_confirmation"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password" />
                    <label for="password">Confirm Password</label>

                  </div>
                  <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>

              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                  <label class="form-check-label" for="terms-conditions">
                    I agree to
                    <a href="javascript:void(0);">privacy policy & terms</a>
                  </label>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100">Sign up</button>
            </form>

            <p class="text-center">
              <span>Already have an account?</span>
              <a href="{{ route('login') }}">
                <span>Sign in instead</span>
              </a>
            </p>


          </div>
        </div>
        <!-- Register Card -->
        <img
          alt="mask"
          src="../../assets/img/illustrations/auth-basic-register-mask-light.png"
          class="authentication-image d-none d-lg-block"
          data-app-light-img="illustrations/auth-basic-register-mask-light.png"
          data-app-dark-img="illustrations/auth-basic-register-mask-dark.png" />
      </div>
    </div>
  </div>
@endsection
