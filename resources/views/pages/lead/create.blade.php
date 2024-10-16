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
            <form class="card-body" method="POST" action="{{ route('lead.store') }}">
                @csrf
                {{-- <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="multicol-username" class="form-control" placeholder="john.doe" />
                            <label for="multicol-username">Username</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="multicol-email" class="form-control" placeholder="john.doe"
                                    aria-label="john.doe" aria-describedby="multicol-email2" />
                                <label for="multicol-email">Email</label>
                            </div>
                            <span class="input-group-text" id="multicol-email2">@example.com</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-password-toggle">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" id="multicol-password" class="form-control"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="multicol-password2" />
                                    <label for="multicol-password">Password</label>
                                </div>
                                <span class="input-group-text cursor-pointer" id="multicol-password2"><i
                                        class="mdi mdi-eye-off-outline"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-password-toggle">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" id="multicol-confirm-password" class="form-control"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="multicol-confirm-password2" />
                                    <label for="multicol-confirm-password">Confirm Password</label>
                                </div>
                                <span class="input-group-text cursor-pointer" id="multicol-confirm-password2"><i
                                        class="mdi mdi-eye-off-outline"></i></span>
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{--
                <hr class="my-4 mx-n4" /> --}}
                {{-- <h6>2. Personal Info</h6> --}}
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="category" name="category" class="select2 form-select" data-allow-clear="true">
                                <option value="">Please Select</option>
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
                                disabled>
                                <option value="">Please Select</option>

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
                                placeholder="John" />
                            <label for="multicol-first-name">Business Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="multicol-last-name" name="business_number" class="form-control"
                                placeholder="
                                +1111111111" />
                            <label for="multicol-last-name">Business Number</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="email" id="basic-default-email" name="email" class="form-control"
                                        placeholder="john.doe" aria-label="john.doe"
                                        aria-describedby="basic-default-email2">
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
                                placeholder="Example.com" aria-label="Example.com" />
                            <label for="multicol-phone">Website Url</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="call-status" name="call_status" class="select2 form-select"
                                data-allow-clear="true">
                                <option value="">Please Select</option>
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
                                min="YYYY-MM-DD HH:MM" id="flatpickr-datetime" readonly="readonly">
                            <label for="flatpickr-datetime">Call Back Time</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="lead-status" name="lead_status" class="select2 form-select"
                                data-allow-clear="true">
                                <option value="">Please Select</option>
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
