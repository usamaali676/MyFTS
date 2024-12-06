@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
<link rel="stylesheet"
    href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/24.6.0/build/css/intlTelInput.min.css" integrity="sha512-X3pJz9m4oT4uHCYS6UjxVdWk1yxSJJIJOJMIkf7TjPpb1BzugjiFyHu7WsXQvMMMZTnGUA9Q/GyxxCWNDZpdHA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .iti{
        width: 100%;
        padding: 0px;
    }
    .iti__dropdown-content{
        z-index: 20 !important;
    }

</style>
@endsection
@section('content')
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Lead/</span> Create</h4>



        <form class="card-body" method="POST" action="{{ route('lead.update', $lead->id) }}" id="leadForm" onsubmit="return validateForm()">
            @csrf
        <!-- Multi Column with Form Separator -->
        <div class="card mb-4 px-3 py-5">
            <h5 class="card-header">Insert Data of Sale</h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="category" name="category" class="select2 form-select" data-allow-clear="true" required>
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
                            <input type="text" id="business_name" name="business_name" class="form-control"
                                placeholder="John" value="{{ $lead->business_name_adv }}"
                                onkeydown="return /[a-zA-Z\s]/.test(event.key) || event.key === 'Backspace' || event.key === 'Tab';" />
                            <label for="multicol-first-name">Business Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="tel"  id="business_number" style="height: calc(2.940725rem + 2px);"
                            name="business_number" class="form-control" value="{{ $lead->business_number_adv }}" />
                            {{-- <label for="multicol-last-name">Business Number</label> --}}
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
                            <input type="text" class="form-control " name="client_name"
                                placeholder="Client Name" aria-label="client_name" value="{{ $lead->client_name }}"
                                onkeydown="return /[a-zA-Z\s]/.test(event.key) || event.key === 'Backspace' || event.key === 'Tab';" required/>
                            <label for="client_name">Client Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control " name="client_designation"
                                placeholder="Client Designation" aria-label="client_designation" value="{{ $lead->client_designation }}"
                                onkeydown="return /[a-zA-Z\s]/.test(event.key) || event.key === 'Backspace' || event.key === 'Tab';"/>
                            <label for="client_designation">Client Designation</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select id="countries" name="country" class="select2 form-select" data-allow-clear="true" >
                                <option value="">Please Select</option>
                                @if(isset($lead->country))
                                <option value="{{$lead->country}}" selected>{{$lead->country}}</option>
                                @endif
                            </select>
                            <label for="multicol-country">Countries</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select id="states" name="states" class="select2 form-select" data-allow-clear="true" >
                                <option value="">Please Select</option>
                                @if (isset($lead->state))
                                <option value="{{$lead->state}}" selected>{{$lead->state}}</option>
                                @endif
                            </select>
                            <label for="multicol-country">States</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select id="cities" name="cities" class="select2 form-select" data-allow-clear="true" >
                                <option value="">Please Select</option>
                                @if (isset($lead->city))
                                    <option value="{{$lead->city}}" selected>{{$lead->city}}</option>
                                @endif
                            </select>
                            <label for="multicol-country">Cities</label>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control " name="zip_code"
                                placeholder="ZIP Code" value="{{ $lead->zip_code }}" aria-label="zip_code"
                                 />
                            <label for="zip_code">ZIP Code</label>
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
                                        <option value="Not Picked">Not Picked</option>
                                        <option value="Picked">Picked</option>
                                        <option value="Busy">Busy</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Disconnected Number">Disconnected Number</option>
                                        <option value="Call Dropped">Call Dropped</option>
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
                                        <option value="Qualified">Qualified</option>
                                        <option value="Unqualified">Unqualified</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Converted">Converted</option>
                                        <option value="Closed - Lost">Closed - Lost</option>
                                        <option value="Closed - Won">Closed - Won</option>
                                        <option value="Follow-Up">Follow-Up</option>
                                        <option value="Recycled">Recycled</option>
                                        <option value="Duplicate">Duplicate</option>

                            </select>
                            <label for="lead-status">Lead Status</label>
                        </div>
                    </div>
                    <div class="col-md-6 select2-primary">
                        <div class="form-floating form-floating-outline">
                            <select id="multicol-closers" name="closers[]" class="select2 form-select" multiple
                                >

                                @if(isset($lead->closers))
                                @foreach ($lead->closers as $item)
                                    <option value="{{ $item->closer_id }}" selected>{{ $item->user->name }}</option>
                                @endforeach
                                @else
                                <option value="">Please Select</option>
                                @endif
                                @foreach ($closers as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                                {{-- <option value="en" selected>English</option>
                                <option value="fr" selected>French</option>
                                <option value="de">German</option>
                                <option value="pt">Portuguese</option> --}}
                            </select>
                            <label for="multicol-closers">Select Closers</label>
                        </div>
                    </div>
                    <div class="col-md-6 select2-primary">
                        <div class="form-floating form-floating-outline">
                            <select id="multicol-service" name="service[]" class="select2 form-select" multiple required>
                                <option value="">Please Select</option>
                                @if(isset($selected_company_services))
                                @foreach ($selected_company_services as $item)
                                    <option value="{{ $item->id }}" selected>{{ $item->name }}</option>
                                @endforeach
                                @else
                                <option value="">Please Select</option>
                                @endif
                                @foreach ($company_services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach

                            </select>
                            <label for="multicol-service">Pitched Services</label>
                        </div>
                    </div>
                </div>

        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6>Additional Contact Info</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="tel"  id="business_number2" style="height: calc(2.940725rem + 2px);"
                                    name="add_business_number" class="form-control" value="{{ $lead->additional_number }}"  />
                                <label for="multicol-last-name">Additional Business Number</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="email" id="basic-default-email2" name="add_email" class="form-control"
                                            placeholder="john.doe" aria-label="john.doe"
                                            aria-describedby="basic-default-email2" value="{{ $lead->additional_email }}">
                                        <label for="basic-default-email">Optional Email</label>
                                    </div>
                                    <span class="input-group-text" id="basic-default-email2">@example.com</span>
                                </div>
                                <div class="form-text">You can use letters, numbers &amp; periods</div>
                            </div>
                        </div>
                    </div>
                    {{-- <div id="repeater">

                            <div class="items">
                                <div class="item-content">
                                    <div class="row py-2">
                                        <div class="col-md-4">
                                            <div class="form-floating form-floating-outline">
                                                <select name="platform_name[]" class="form-control">
                                                    <option value="">Please Select</option>
                                                    <option value="phone">Phone</option>
                                                    <option value="email">Email</option>
                                                    <option value="url">URL</option>
                                                </select>
                                                <label> Platform</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" name="platform_value[]" placeholder="Value" />
                                                <label>Value</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-outline-danger remove-btn" style="color: #ff4d49" onclick="$(this).closest('.items').remove()">Remove</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="items" >
                            </div>

                            <div class="repeater-footer py-4" style="display: flex; justify-content: flex-end;">
                                <a class="btn btn-primary repeater-add-btn" style="color: #fff">Add</a>
                            </div>

                    </div> --}}

                        <!-- Hidden Template for New Repeater Item -->
                </div>
            </div>
        </div>


        <div class="pt-4">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
        </div>
        </form>

        <div class="col-xl-12 col-lg-5 col-md-5 pt-5">
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
                    <table id="comment_table" class="datatable-project table">
                        <thead class="table-light">
                            <tr>
                                <th>sr#</th>
                                <th>Stage</th>
                                <th>Due Date</th>
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
                                <!-- Formatted date (no time) -->
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


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
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/24.6.0/build/js/intlTelInput.min.js" integrity="sha512-/sRFlFRbcvObOo/SxW8pvmFZeMLvAF6hajRXeX15ekPgT4guXnfNSjLC98K/Tg2ObUgKX8vn9+Th5/mGHzZbEw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    const input = document.querySelector("#business_number");
    const iti =  intlTelInput(input, {
  initialCountry: "us",
  strictMode: true,
  utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/24.6.0/build/js/utils.min.js"
});
</script>
<script>
    $(document).ready(function () {
        var selected = $(this).find('option:selected').val();
        if(selected == "Asked to Callback"){
                $('#flatpickr-datetime').prop('disabled', false);
            }
            else{
                $('#flatpickr-datetime').prop('disabled', true);
            }
        $('#call-status').change(function (e) {
            e.preventDefault();
            var selected = $(this).find('option:selected').val();
            // alert(selected);
            if(selected == "Asked to Callback"){
                $('#flatpickr-datetime').prop('disabled', false);
            }
            else{
                $('#flatpickr-datetime').prop('disabled', true);
            }
        });
    });
    </script>

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
<script>
    function validateForm() {
        const businessName = document.getElementById('business_name').value;
        const businessNumber = document.getElementById('business_number').value;
        const email = document.getElementById('basic-default-email').value;
        const websiteUrl = document.getElementsByName('website_url')[0].value;
        const clientName = document.getElementsByName('client_name')[0].value;
        const clientAddress = document.getElementsByName('client_address')[0].value;

        // Check if business name is set and validate it
        if (businessName && businessName.trim() !== "") {
            // Validate business name (only letters and spaces)
            if (!/^[a-zA-Z\s]*$/.test(businessName)) {
                alert('Invalid Business Name. Only letters and spaces are allowed.');
                return false;
            }
        } else {
            alert('Business Name is required.');
            return false;
        }

        // Check if business number is set and validate phone number
        if (businessNumber && businessNumber.trim() !== "") {
            if (!iti.isValidNumber()) { // Assuming you use a library like intl-tel-input
                alert('Invalid Phone Number. Please enter a valid phone number.');
                return false;
            }
        } else {
            alert('Business Number (Phone) is required.');
            return false;
        }

        // Check if website URL is set and validate it
        if (websiteUrl && websiteUrl.trim() !== "") {
            // Validate website URL (must be a valid URL with domain)
            const urlPattern = /^(https?:\/\/)?([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?$/;
            if (!urlPattern.test(websiteUrl)) {
                alert('Invalid Website URL. Please enter a valid URL (e.g., http://example.com).');
                return false;
            }
        }

        // Check if email is set and validate it
        if (email && email.trim() !== "") {
            // Validate email format (must contain "@" and a valid domain)
            const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(email)) {
                alert('Invalid Email. Please enter a valid email address (e.g., user@example.com).');
                return false;
            }
        }

        // Check if client name is set and validate it
        if (clientName && clientName.trim() !== "") {
            // Validate client name (only letters and spaces)
            if (!/^[a-zA-Z\s]*$/.test(clientName)) {
                alert('Invalid Client Name. Only letters and spaces are allowed.');
                return false;
            }
        } else {
            alert('Client Name is required.');
            return false;
        }

        // Check if client address is set and validate it
        if (clientAddress && clientAddress.trim() !== "") {
            // Validate client address (allowing letters, digits, and certain symbols)
            if (!/^[a-zA-Z0-9\/\-\_\:\, ]*$/.test(clientAddress)) {
                alert('Invalid Client Address. Only letters, numbers, and certain symbols are allowed.');
                return false;
            }
        }

        return true; // All validations passed
    }
</script>
<script>
    $(document).ready(function() {
        // Load countries
        $.get('{{ route('front.countries') }}', function(data) {
            $.each(data, function(index, country) {
                // console.log(country);
                $('#countries').append(`<option value="${country.name}">${country.name} (${country.iso2})</option>`);
            });
        });

        // Load states on country select
        $('#countries').on('change', function() {
            const countryId = $(this).val();
            $('#states').empty().append('<option value="">Select State</option>').prop('disabled', false);
            $('#cities').empty().append('<option value="">Select City</option>').prop('disabled', true);

            if (countryId) {
                $.get(`/front/states/${countryId}`, function(data) {
                    $.each(data, function(index, state) {
                        $('#states').append(`<option value="${state.name}">${state.name} (${state.state_code})</option>`);
                    });
                });
            }
        });
        // Load cities on state select
        $('#states').on('change', function() {
            const conrtyId = $('#countries').val();
            const stateId = $(this).val();
            $('#cities').empty().append('<option value="">Select City</option>').prop('disabled', false);

            if (stateId) {
                $.get(`/front/cities/${stateId}/${conrtyId}`, function(data) {
                    $.each(data, function(index, city) {
                        $('#cities').append(`<option value="${city.name}">${city.name}</option>`);
                    });
                });
            }
        });
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
