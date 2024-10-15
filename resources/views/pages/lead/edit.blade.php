@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
<link rel="stylesheet"
    href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
@endsection
@section('content')
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Lead/</span> Create</h4>



        <!-- Multi Column with Form Separator -->
        <div class="card mb-4">
            <h5 class="card-header">Insert Data of Sale</h5>
            <form class="card-body" method="POST" action="{{ route('lead.update', $lead->id) }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="category" name="category" class="select2 form-select" data-allow-clear="true">
                                <option value="">Please Select</option>
                                @if(isset($lead->category_id))
                                <option value="{{$lead->category_id}}" selected>{{$lead->category->name}}</option>
                                @else
                                <option value="" selected>Please Select</option>
                                @endif
                                @foreach ($categories as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach

                            </select>
                            <label for="multicol-country">Categories</label>
                        </div>
                    </div>
                    <div class="col-md-6 select2-primary">
                        <div class="form-floating form-floating-outline">
                            <select id="multicol-language" name="sub_category[]" class="select2 form-select" multiple
                                >
                                <option value="">Please Select</option>
                                @foreach ($sub_categories as $sub)
                                <option value="{{ $sub->id }}" selected>{{ $sub->name }}</option>
                                @endforeach
                                @if(isset($lead->category_id))
                                @foreach ($related_subcategories as $rel_sub)
                                <option value="{{ $rel_sub->id }}">{{ $rel_sub->name }}</option>
                                @endforeach
                                @endif
                                {{-- <option value="en" selected>English</option>
                                <option value="fr" selected>French</option>
                                <option value="de">German</option>
                                <option value="pt">Portuguese</option> --}}
                            </select>
                            <label for="multicol-language">Sub Categories</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="multicol-first-name" name="business_name" class="form-control"
                                placeholder="John" value="{{ $lead->business_name_adv }}" />
                            <label for="multicol-first-name">Business Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="multicol-last-name" name="business_number" class="form-control"
                                placeholder="
                                +1111111111" value="{{ $lead->business_number_adv }}" />
                            <label for="multicol-last-name">Business Number</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="basic-default-email" name="email" class="form-control"
                                        placeholder="john.doe" aria-label="john.doe"
                                        aria-describedby="basic-default-email2" value="{{ $lead->off_email }}">
                                    <label for="basic-default-email">Official Email</label>
                                </div>
                                <span class="input-group-text" id="basic-default-email2">@example.com</span>
                            </div>
                            <div class="form-text">You can use letters, numbers &amp; periods</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control " name="website_url"
                                placeholder="Example.com" aria-label="Example.com" value="{{ $lead->website_url }}" />
                            <label for="multicol-phone">Website Url</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="call-status" name="call_status" class="select2 form-select"
                                data-allow-clear="true">

                                @if(isset($lead->call_status))
                                <option value="{{ $lead->call_status }}" selected>{{ $lead->call_status }}</option>
                                @else
                                <option value="">Please Select</option>
                                @endif
                                <option value="Interested">Interested</option>
                                <option value="Do Not Caller List">Do Not Caller List</option>
                                <option value="Asked to Callback">Asked to Callback</option>
                                <option value="No Picked">No Picked</option>
                                <option value="Picked">Picked</option>
                            </select>
                            <label for="call-status">Call Status</label>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control flatpickr-input active" name="call_back_time"
                                min="YYYY-MM-DD HH:MM" id="flatpickr-datetime" readonly="readonly" value="{{ $lead->call_back_time }}">
                            <label for="flatpickr-datetime">Call Back Time</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="lead-status" name="lead_status" class="select2 form-select"
                                data-allow-clear="true">
                                @if(isset($lead->lead_status))
                                <option value="{{ $lead->lead_status }}" selected>{{ $lead->lead_status }}</option>
                                @else
                                <option value="">Please Select</option>
                                @endif

                                <option value="Interested">Interested</option>
                                <option value="Do Not Caller List">Do Not Caller List</option>
                                <option value="Do Not Intrested">Do Not Intrested</option>

                            </select>
                            <label for="lead-status">Lead Status</label>
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
                        <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel1">Modal title</h4>
                                <button
                                  type="button"
                                  class="btn-close"
                                  data-bs-dismiss="modal"
                                  aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="row">
                                  <div class="col mb-4 mt-2">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" id="nameBasic" class="form-control" placeholder="Enter Name" />
                                      <label for="nameBasic">Name</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="row g-2">
                                  <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                      <input
                                        type="email"
                                        id="emailBasic"
                                        class="form-control"
                                        placeholder="xxxx@xxx.xx" />
                                      <label for="emailBasic">Email</label>
                                    </div>
                                  </div>
                                  <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                      <input type="date" id="dobBasic" class="form-control" />
                                      <label for="dobBasic">DOB</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                  Close
                                </button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                              </div>
                            </div>
                          </div>
                        </div>


</div>
<!-- / Content -->


<div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
@endsection
@section('js')
<script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
<script src="{{ asset('assets/js/forms-pickers.js') }}"></script>

<script>
    $('#category').change(function (e) {
    e.preventDefault();
    var selected = $(this).find('option:selected').val();
    var subcategories = $('#multicol-language');
    if (selected){
        subcategories.prop('disabled', false);
        $.ajax({
            type: "GET",
            url: "{{ route('front.get_subcategory') }}",
            data: {'selected' : selected},
            success: function (response) {
               sub_category = response.sub_category;

               subcategories.empty();
               subcategories.append('<option value="">Please Select</option>');
               $.each(sub_category, function (i, item) {
                   subcategories.append('<option value="' + item.id + '">' + item.name + '</option>');
               });
                console.log(sub_category);

            }
        });
    }
    else{
        subcategories.prop('disabled', true);
    }
});
</script>
@endsection
