@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
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

              <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sale /</span> Detail</h4>


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
                            @if (isset($sale->lead->business_name_adv))
                                <h4>{{ $sale->lead->business_name_adv }}</h4>
                            @endif
                            <ul
                              class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                              @if (isset($sale->lead->business_number_adv))
                              <li class="list-inline-item">
                                <i class="mdi mdi-phone me-1 mdi-20px text-primary"></i
                                ><span class="fw-medium">{{ $sale->lead->business_number_adv }}</span>
                              </li>
                              @endif

                             @if (isset($sale ->lead->client_address) )
                                 <li class="list-inline-item">
                                <i class="mdi mdi-map-marker-outline me-1 mdi-20px text-primary"></i>
                                    <span class="fw-medium">{{ $sale->lead->client_address }}</span>
                               </li>
                             @endif
                             @if (isset($sale->activation_date))
                             <li class="list-inline-item">
                               <i class="mdi mdi-calendar-blank-outline me-1 mdi-20px text-primary"></i
                               ><span class="fw-medium"> {{$sale->activation_date}}</span>
                             </li>
                             @endif
                            </ul>
                          </div>
                            @if (isset($sale->status))
                                <a href="javascript:void(0)" class="btn btn-primary">
                                    <i class="mdi mdi-account-check-outline me-1"></i>
                                    {{ $sale->status == 1 ? 'Active' : 'De-active' }}
                                </a>
                            @endif

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Header -->

              <!-- Navbar pills -->
              {{-- <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"
                        ><i class="mdi mdi-account-outline me-1 mdi-20px"></i>Profile</a
                      >
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages-profile-teams.html"
                        ><i class="mdi mdi-account-multiple-outline me-1 mdi-20px"></i>Teams</a
                      >
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages-profile-projects.html"
                        ><i class="mdi mdi-view-grid-outline me-1 mdi-20px"></i>Projects</a
                      >
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="pages-profile-connections.html"
                        ><i class="mdi mdi-link me-1 mdi-20px"></i>Connections</a
                      >
                    </li>
                  </ul>
                </div>
              </div> --}}
              <!--/ Navbar pills -->

              <!-- User Profile Content -->
              <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-5">
                  <!-- About User -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <small class="card-text text-uppercase">About</small>
                      <ul class="list-unstyled my-3 py-1">
                        @if (isset($sale->lead->client_name))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-account-outline mdi-24px text-primary"></i
                                ><span class="fw-medium mx-2">Full Name:</span> <span>{{$sale->lead->client_name}}</span>
                            </li>
                        @endif

                        @if (isset($sale->client_nature))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-star-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Client Nature:</span>
                                <span>{{$sale->client_nature}}</span>
                            </li>
                        @endif
                        @if (isset($sale->lead->client_address))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-flag-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Address:</span>
                                <span>{{$sale->lead->client_address}}</span>
                            </li>
                        @endif
                        @if (isset($sale->time_zone))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-translate mdi-24px text-primary"></i><span class="fw-medium mx-2">Time Zone:</span>
                                <span>{{$sale->time_zone}}</span>
                            </li>
                        @endif
                      </ul>
                      <small class="card-text text-uppercase">Contacts</small>
                      <ul class="list-unstyled my-3 py-1">
                        @if (isset($sale->lead->business_number_adv))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-phone-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Contact:</span>
                                <span>{{$sale->lead->business_number_adv}}</span>
                            </li>
                        @endif
                        @if (isset($sale->lead->off_email))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-email-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Email:</span>
                                <span>{{$sale->lead->off_email}}</span>
                            </li>
                        @endif
                        @if (isset($sale->call_type))
                            <li class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-message-outline mdi-24px text-primary"></i><span class="fw-medium mx-2">Call Type:</span>
                                <span>{{$sale->call_type}}</span>
                            </li>
                        @endif
                      </ul>
                      <small class="card-text text-uppercase">Social Link</small>
                      <ul class="list-unstyled mb-0 mt-3 pt-1">
                        @if(isset($sale->social_links))
                            @foreach($sale->social_links as $link)
                                <li class="d-flex align-items-center mb-3">
                                    <div class="d-flex flex-wrap">
                                    <span class="fw-medium me-2">{{ $link->social_name }}:</span> <span>{{ $link->social_link }}</span>
                                    </div>
                                </li>
                            @endforeach
                        @endif


                      </ul>
                    </div>
                  </div>
                  <!--/ About User -->
                  <!-- Profile Overview -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <small class="card-text text-uppercase">Teams</small>
                      <ul class="list-unstyled mb-0 mt-3 pt-1">
                        <li class="d-flex align-items-center mb-3">
                            <i class="mdi mdi-phone-hangup mdi-24px text-secondary me-2 text-primary text-primary"></i>
                            <div class="d-flex flex-wrap">
                              <span class="fw-medium me-2">Closers</span><span>
                                  {{-- <p>{{ $sale->lead->closers }}</p> --}}
                                  @if(isset($sale->lead->closers))
                                  <div class="g-5">
                                      @foreach($sale->lead->closers as $closer)
                                          <span class="badge rounded-pill bg-primary">{{ $closer->user->name }}</span>
                                      @endforeach
                                  </div>
                                  @endif
                                  </span>
                            </div>
                          </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="mdi mdi-face-agent mdi-24px text-secondary me-2 text-primary text-primary"></i>
                            <div class="d-flex flex-wrap">
                              <span class="fw-medium me-2">Customer Support</span><span>
                                  {{-- <p>{{ $sale->Customer_support }}</p> --}}
                                  @if (isset($sale->Customer_support))
                                      @foreach ($sale->Customer_support as $cs)
                                      <span class="badge rounded-pill bg-primary">{{ $cs->user->name }}</span>
                                      @endforeach
                                  @endif
                                  </span>
                            </div>
                        </li>


                      </ul>
                    </div>
                  </div>
                  <!--/ Profile Overview -->
                </div>
                <div class="col-xl-8 col-lg-7 col-md-7">
                  <!-- Activity Timeline -->
                  <div class="card card-action mb-4">
                    <div class="card-header align-items-center">
                      <h5 class="card-action-title mb-0">
                        <i class="mdi mdi-format-list-bulleted mdi-24px me-2"></i>Client Services
                      </h5>
                      <div class="card-action-element">
                        <div class="dropdown">


                        </div>
                      </div>
                    </div>
                    <div class="card-body pt-3 pb-0">
                      <ul class="timeline mb-0">

                        @if(isset($sale->clientServices))
                            @foreach ($sale->clientServices as $services)
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-danger"></span>
                                <div class="timeline-event">
                                  <div class="timeline-header mb-1">
                                    <h6 class="mb-0">{{ $services->name }}</h6>
                                    <small class="text-muted">Active</small>
                                  </div>
                                  <p class="mb-2">{{ $sale->lead->business_name_adv }}</p>
                                </div>
                              </li>
                                {{-- <li class="py-2">
                                    <h6 class="mb-0">{{ $services->name }}</h6>
                                </li> --}}
                            @endforeach
                        @endif

                      </ul>
                    </div>
                  </div>
                  <!--/ Activity Timeline -->
                  <div class="row">
                    <!-- Connections -->
                    <div class="col-lg-12 col-xl-6">
                      <div class="card card-action mb-4">
                        <div class="card-header align-items-center">
                          <h5 class="card-action-title mb-0">Keywords</h5>
                          <div class="card-action-element">
                            <div class="dropdown">

                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <ul class="list-unstyled mb-0">

                            @if (isset($sale->keyword))
                                @foreach ($sale->keyword as $keywords)
                                <li class="mb-3">
                                    <div class="d-flex align-items-start">
                                      <div class="d-flex align-items-start">
                                        <div class="avatar me-3">
                                            <img src="{{ asset('assets/img/icons/brands/support-label.png') }}" alt="Avatar" class="rounded-circle" />
                                          </div>
                                        <div class="me-2">
                                          <h6 class="mb-0">{{ $keywords->keyword }}</h6>
                                          @if(isset($keywords->area))
                                            <small>{{ $keywords->area->country }}, {{ $keywords->area->state }}, {{ $keywords->area->city }}</small>
                                          @endif
                                        </div>
                                      </div>
                                    </div>
                                  </li>
                                @endforeach
                            @endif

                          </ul>
                        </div>
                      </div>
                    </div>
                    <!--/ Connections -->
                    <!-- Teams -->
                    <div class="col-lg-12 col-xl-6">
                      <div class="card card-action mb-4">
                        <div class="card-header align-items-center">

                          <h5 class="card-action-title mb-0">Service Areas</h5>
                          <div class="card-action-element">
                            <div class="dropdown">


                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <ul class="list-unstyled mb-0">

                            @if(isset($sale->service_area))
                                @foreach($sale->service_area as $area)
                                <li class="mb-3">
                                    <div class="d-flex align-items-center">
                                      <div class="d-flex align-items-start">
                                        <div class="avatar me-3">
                                          <img
                                            src="../../assets/img/icons/brands/social-label.png"
                                            alt="Avatar"
                                            class="rounded-circle" />
                                        </div>
                                        <div class="me-2">
                                          <h6 class="mb-0"><small>{{ $area->country }}, {{ $area->state }}, {{ $area->city }}</small>                                          </h6>
                                          <small>{{ $area->state }}</small>
                                        </div>
                                      </div>
                                      <div class="ms-auto">
                                        <a href="javascript:;"
                                          ><span class="badge bg-label-primary rounded-pill">{{ $area->country }}</span></a
                                        >
                                      </div>
                                    </div>
                                  </li>
                                @endforeach
                            @endif

                          </ul>
                        </div>
                      </div>
                    </div>
                    <!--/ Teams -->
                  </div>

                  {{-- <p>{{ $sale->invoice }}</p> --}}
                  @if(isset($sale->invoice))

                  <!-- Projects table -->
                  <div class="card mb-4">
                    <div class="card-datatable table-responsive">
                      <table class="datatable-project table">
                        <thead class="table-light">
                          <tr>
                            <th>sr#</th>
                            <th>Invoice No.</th>
                            <th class="text-nowrap">Invoice Month</th>
                            <th>Invoice Amount</th>
                            <th>Invoice Due Date</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->invoice as $key=>$item)
                            <tr>
                                <td>{{ $key++ }}</td>
                                <td>{{ $item->invoice_number }}</td>
                                <td>{{ $item->month }}</td>
                                <td>{{ $item->total_amount }}</td>
                                <td>{{ $item->invoice_due_date }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!--/ Projects table -->

                  @endif

                </div>
              </div>
              <div class="row">

                <div class="col-xl-12 col-lg-5 col-md-5">
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
                                    <td>{{ \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d') }}</td> <!-- Formatted date (no time) -->
                                </tr>
                                @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                </div>
              </div>
              <!--/ User Profile Content -->
            </div>
            <!-- / Content -->

            <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
                    <div class="modal-content p-3 p-md-5">
                        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-body p-md-0">
                            <div class="text-center mb-4">
                                <h3 class="role-title mb-2 pb-0">Add New Comment</h3>
                                {{-- <p>Set role permissions</p> --}}
                            </div>
                            <!-- Add role form -->
                            <form id="add_comment" class="row g-3" method="POST" action="{{ route('comment.store') }}">
                                @csrf
                                <input type="hidden" name="lead_id" value="{{ $sale->lead_id }}">
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
        // Initialize Flatpickr
        flatpickr("#flatpickr-date", {
            minDate: threeDaysAgo // Set minimum date to 3 days ago
        });
</script>

<script>
    $(document).ready(function () {
        $('#add_comment').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Store current form values before submission, particularly time and select elements

            // Create a FormData object to handle form data
            let formData = new FormData(this);

            // Clear previous error messages

            $.ajax({
                url: $(this).attr('action'), // Form action URL
                type: $(this).attr('method'), // POST method
                data: formData,
                processData: false, // Important: do not process the data
                contentType: false, // Important: content type is false
                success: function (response) {


                    var comments = response.comments;
                    // console.log(invoice);

                    var table_content = ''
                    comments.forEach(function(comments, index) {
                        var createdAtDate = new Date(comments.created_at);
                        // Format the date to 'YYYY-MM-DD' (no time)
                        var formattedDate = createdAtDate.toISOString().split('T')[0]; // Extract date in 'YYYY-MM-DD' format

                        table_content += '<tr>\
                                <td>' + (index + 1) + '</td>\
                                <td>' + comments.Stage + '</td>\
                                <td>' + comments.due_date + '</td>\
                                <td>' + comments.user.name + '</td>\
                                <td>' + comments.comment + '</td>\
                                <td>' + formattedDate + '</td>\
                            </tr>';
                        });

                        // Clear the table first and then append the new rows
                        $('#comment_table tbody').empty().append(table_content);




                    // $('#keyword_table').append(row);
                    // $('#keyword').val('');
                    // $('areas_dropdown').val(response.area ? response.area.id : '');  // Ensure the dropdown is set or left blank if area is null





                    // Handle success response (display success message using SweetAlert2)
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                       showConfirmButton: false, // Remove the confirm button
                        timer: 1500, // Duration in milliseconds before the toast disappears
                        toast: true,
                        showConfirmButton: false
                    });


                    // Optionally reset the form only on success
                    // $('#keywordadd')[0].reset();
                    // This will reset the form fields
                },
                error: function (xhr) {
                    // Handle validation errors
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        let errorHtml = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function (key, value) {
                            errorHtml += '<li>' + value + '</li>';
                        });
                        errorHtml += '</ul></div>';
                        $('#saleForm').prepend(errorHtml); // Add errors to the form

                        // Display SweetAlert2 for validation errors
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorHtml, // Use the generated error HTML
                            showConfirmButton: false, // Remove the confirm button
                            timer: 1500, // Duration in milliseconds before the toast disappears
                            toast: true,
                        });
                    }
                    else if (xhr.responseJSON.error) {
                        // Show custom error message with SweetAlert2
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.error, // Show the custom error message
                            showConfirmButton: false, // Remove the confirm button
                            timer: 1500, // Duration in milliseconds before the toast disappears
                            toast: true,
                        });
                    }

                    // Restore time input and select values after error
                }
            });
        });
    });
</script>
@endsection
