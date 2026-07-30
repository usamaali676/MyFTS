@extends('layouts.dashboard')
@section('content')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
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
                    <div class="col-md-4">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="name" id="name" value="{{ old('name', $trainee->name) }}" class="form-control" placeholder="Alex Trainee" />
                        <label for="name">Full Name</label>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="sudo_name" id="sudo_name" value="{{ old('sudo_name', $trainee->sudo_name) }}" class="form-control" placeholder="Alex Trainee" />
                        <label for="sudo_name">Sudo Name</label>
                      </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="row">
                            <div class="col-md mb-md-0 mb-5">
                            <div class="form-check custom-option custom-option-basic checked">
                                <label class="form-check-label custom-option-content" for="sudo_options_1">
                                <input class="form-check-input" name="sudo_options" type="radio" value="1" id="sudo_options_1" @if($trainee->sudo_options) checked @endif>
                                <span class="custom-option-header">
                                    <span class="h6 mb-0">Active</span>
                                </span>
                                </label>
                            </div>
                            </div>
                            <div class="col-md">
                            <div class="form-check custom-option custom-option-basic">
                                <label class="form-check-label custom-option-content" for="sudo_options_0">
                                <input class="form-check-input" name="sudo_options" type="radio" value="0" id="sudo_options_0" @if(!$trainee->sudo_options) checked @endif>
                                <span class="custom-option-header">
                                    <span class="h6 mb-0">Inactive</span>
                                </span>
                                </label>
                            </div>
                            </div>
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
