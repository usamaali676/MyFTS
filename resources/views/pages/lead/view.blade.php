@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
@endsection
@section('content')
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">

              <h4 class="py-3 mb-4"><span class="text-muted fw-light">Lead /</span> Detail</h4>

              <!-- Header -->
              <div class="row">
                <div class="col-12">
                  <div class="card mb-4">
                    <div class="user-profile-header-banner">
                      <img src="../../assets/img/pages/profile-banner.png" alt="Banner image" class="rounded-top" />
                    </div>
                    <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                      <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        <img
                          src="../../assets/img/avatars/1.png"
                          alt="user image"
                          class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" />
                      </div>
                      <div class="flex-grow-1 mt-3 mt-sm-5">
                        <div
                          class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                          <div class="user-profile-info">
                            @if (isset($lead->business_name_adv))
                                <h4>{{ $lead->business_name_adv }}</h4>
                            @endif
                            <ul
                              class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                              @if (isset($lead->business_number_adv))
                              <li class="list-inline-item">
                                <i class="mdi mdi-phone me-1 mdi-20px text-primary"></i
                                ><span class="fw-medium">{{ $lead->business_number_adv }}</span>
                              </li>
                              @endif

                             @if (isset($lead->client_address))
                                 <li class="list-inline-item">
                                <i class="mdi mdi-map-marker-outline me-1 mdi-20px text-primary"></i>
                                    <span class="fw-medium">{{ $lead->client_address }}</span>
                               </li>
                             @endif
                             @if (isset($lead->created_at))
                             <li class="list-inline-item">
                               <i class="mdi mdi-calendar-blank-outline me-1 mdi-20px text-primary"></i
                               ><span class="fw-medium"> {{ $lead->created_at->format('M d, Y') }}</span>
                             </li>
                             @endif
                            </ul>
                          </div>
                            @if (isset($lead->lead_status))
                                <a href="javascript:void(0)" class="btn btn-outline-primary">
                                    <i class="mdi mdi-tag-outline me-1"></i>
                                    {{ $lead->lead_status }}
                                </a>
                            @endif

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Header -->

              <!-- No-sale banner -->
              <div class="row">
                <div class="col-12">
                  <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                      <i class="mdi mdi-information-outline me-2 mdi-24px"></i>
                      <span>This lead doesn't have a sale yet — showing lead details only.</span>
                    </div>
                    <a href="{{ route('sale.create', $lead->id) }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-cart-plus me-1"></i> Create Sale
                    </a>
                  </div>
                </div>
              </div>
              <!--/ No-sale banner -->

              <!-- Lead Detail Content -->
              <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-5">
                  <!-- About Lead -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <small class="card-text text-uppercase">About</small>
                      <ul class="list-unstyled my-3 py-1">
                        @if (isset($lead->client_name))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-account-outline mdi-24px text-primary"></i
                                ><span class="fw-medium mx-2">Full Name:</span> <span>{{ $lead->client_name }}</span>
                            </li>
                        @endif

                        @if (isset($lead->client_designation))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-briefcase-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Designation:</span>
                                <span>{{ $lead->client_designation }}</span>
                            </li>
                        @endif
                        @if (isset($lead->client_address))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-flag-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Address:</span>
                                <span>{{ $lead->client_address }}</span>
                            </li>
                        @endif
                        @if (isset($lead->lead_status))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-tag-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Lead Status:</span>
                                <span>{{ $lead->lead_status }}</span>
                            </li>
                        @endif
                        @if (isset($lead->call_status))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-phone-log-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Call Status:</span>
                                <span>{{ $lead->call_status }}</span>
                            </li>
                        @endif
                      </ul>
                      <small class="card-text text-uppercase">Contacts</small>
                      <ul class="list-unstyled my-3 py-1">
                        @if (isset($lead->business_number_adv))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-phone-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Contact:</span>
                                <span>{{ $lead->business_number_adv }}</span>
                            </li>
                        @endif
                        @if (isset($lead->additional_number))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-phone-plus-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Additional Number:</span>
                                <span>{{ $lead->additional_number }}</span>
                            </li>
                        @endif
                        @if (isset($lead->off_email))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-email-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Email:</span>
                                <span>{{ $lead->off_email }}</span>
                            </li>
                        @endif
                        @if (isset($lead->additional_email))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-email-plus-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Additional Email:</span>
                                <span>{{ $lead->additional_email }}</span>
                            </li>
                        @endif
                        @if (isset($lead->website_url))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-web mdi-24px text-primary"></i><span class="fw-medium mx-2">Website:</span>
                                <span>{{ $lead->website_url }}</span>
                            </li>
                        @endif
                      </ul>
                    </div>
                  </div>
                  <!--/ About Lead -->
                  <!-- Lead Overview -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <small class="card-text text-uppercase">Teams</small>
                      <ul class="list-unstyled mb-0 mt-3 pt-1">
                        <li class="d-flex align-items-center mb-3">
                            <i class="mdi mdi-account-tie-outline mdi-24px text-secondary me-2 text-primary text-primary"></i>
                            <div class="d-flex flex-wrap">
                              <span class="fw-medium me-2">Saler</span><span>
                                  @if($lead->saler)
                                      <span class="badge rounded-pill bg-primary">{{ $lead->saler->name }}</span>
                                  @endif
                                  </span>
                            </div>
                          </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="mdi mdi-phone-hangup mdi-24px text-secondary me-2 text-primary text-primary"></i>
                            <div class="d-flex flex-wrap">
                              <span class="fw-medium me-2">Closers</span><span>
                                  @if($lead->closers->isNotEmpty())
                                  <div class="g-5">
                                      @foreach($lead->closers as $closer)
                                          @if($closer->user)
                                              <span class="badge rounded-pill bg-primary">{{ $closer->user->name }}</span>
                                          @endif
                                      @endforeach
                                  </div>
                                  @endif
                                  </span>
                            </div>
                          </li>
                      </ul>
                    </div>
                  </div>
                  <!--/ Lead Overview -->
                </div>
                <div class="col-xl-8 col-lg-7 col-md-7">
                  <!-- Interested Services -->
                  <div class="card card-action mb-4">
                    <div class="card-header align-items-center">
                      <h5 class="card-action-title mb-0">
                        <i class="mdi mdi-format-list-bulleted mdi-24px me-2"></i>Interested Services
                      </h5>
                      <div class="card-action-element">
                        <div class="dropdown"></div>
                      </div>
                    </div>
                    <div class="card-body pt-3 pb-0">
                      <ul class="timeline mb-0">
                        @forelse($lead->company_services as $service)
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-danger"></span>
                                <div class="timeline-event">
                                  <div class="timeline-header mb-1">
                                    <h6 class="mb-0">{{ $service->name }}</h6>
                                  </div>
                                </div>
                              </li>
                        @empty
                            <li class="py-2 text-muted">No services selected yet.</li>
                        @endforelse
                      </ul>
                    </div>
                  </div>
                  <!--/ Interested Services -->
                </div>
              </div>
              <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-xl-6 col-md-6 col-sm-3 m-auto">
                                    <h3 style="margin-bottom: 0px">Comments</h3>
                                </div>
                                <div class="col-xl-6 col-md-6 col-sm-3 text-end">
                                    <button data-bs-target="#addRoleModal" data-bs-toggle="modal"
                                        class="btn btn-primary mb-3 text-nowrap add-new-role">
                                        Add Comment
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-datatable table-responsive">
                          <table id="comment_table" class="datatable-project table" >
                            <thead class="table-light">
                              <tr>
                                <th>sr#</th>
                                <th>Stage</th>
                                <th >Due Date</th>
                                <th>Responsible</th>
                                <th class="text-nowrap">Comment</th>
                                <th>Created At</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($comments as $key=>$comment)
                                <tr>
                                    <td>{{ $key++ }}</td>
                                    <td>{{ $comment->Stage }}</td>
                                    <td>{{ $comment->due_date }}</td>
                                    <td>{{ $comment->user->name }}</td>
                                    <td>{{ $comment->comment }}</td>
                                    <td>{{ \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                </div>
              </div>
              <!--/ Lead Detail Content -->
            </div>
            <!-- / Content -->

            <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
                    <div class="modal-content p-3 p-md-5">
                        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-body p-md-0">
                            <div class="text-center mb-4">
                                <h3 class="role-title mb-2 pb-0">Add New Comment</h3>
                            </div>
                            <!-- Add role form -->
                            <form id="add_comment" class="row g-3" method="POST" action="{{ route('comment.store') }}">
                                @csrf
                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                <div class="col-6 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="stages" name="stage" class="select2 form-select" data-allow-clear="true" >
                                            <option value="">Please Select</option>
                                            <option value="Lead">Lead</option>
                                            <option value="Oppertuniry">Oppertuniry</option>
                                            <option value="Pre-Sale">Pre-Sale</option>
                                            <option value="Close-Sale">Close-Sale</option>
                                            <option value="Active">Active</option>
                                            <option value="Deactive">Deactive</option>
                                            <option value="IT">IT</option>
                                            <option value="Bug">Bug</option>
                                            <option value="Query">Query</option>
                                            <option value="Resolved">Resolved</option>
                                        </select>
                                        <label for="multicol-country">Stage</label>
                                    </div>
                                </div>

                                <div class="col-6 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control flatpickr-input active" name="due_date"
                                        placeholder="YYYY-MM-DD" id="flatpickr-date">
                                        <label for="flatpickr-date">Due Date</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating form-floating-outline mb-6">
                                        <textarea class="form-control h-px-100" id="full-editor" name="comment" placeholder="Comments here..." spellcheck="false"></textarea>
                                        <label for="exampleFormControlTextarea1">Write Comment</label>
                                      </div>
                                </div>

                                <div class="col-6 text-center">
                                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                            <!--/ Add role form -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
@endsection
@section('js')
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
<script>
    const threeDaysAgo = new Date();
        threeDaysAgo.setDate(threeDaysAgo.getDate() - 0);
        flatpickr("#flatpickr-date", {
            minDate: threeDaysAgo
        });
</script>

<script>
    $(document).ready(function () {
        $('#add_comment').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    var comments = response.comments;

                    var table_content = ''
                    comments.forEach(function(comments, index) {
                        var createdAtDate = new Date(comments.created_at);
                        var formattedDate = createdAtDate.toISOString().split('T')[0];

                        table_content += '<tr>\
                                <td>' + (index + 1) + '</td>\
                                <td>' + comments.Stage + '</td>\
                                <td>' + comments.due_date + '</td>\
                                <td>' + comments.user.name + '</td>\
                                <td>' + comments.comment + '</td>\
                                <td>' + formattedDate + '</td>\
                            </tr>';
                        });

                        $('#comment_table tbody').empty().append(table_content);

                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                        toast: true,
                    });
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        let errorHtml = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function (key, value) {
                            errorHtml += '<li>' + value + '</li>';
                        });
                        errorHtml += '</ul></div>';
                        $('#saleForm').prepend(errorHtml);

                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorHtml,
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                        });
                    }
                    else if (xhr.responseJSON.error) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.error,
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                        });
                    }
                }
            });
        });
    });
</script>
@endsection
