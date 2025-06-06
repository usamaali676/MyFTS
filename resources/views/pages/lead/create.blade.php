@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
<link rel="stylesheet"
    href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/24.6.0/build/css/demo.min.css" integrity="sha512-aSEB9TAdgLoT7TbSjCH3v05X+iaOwPfSIaiVu7J7FZ2seZ+sDfoQe78zEDbPWFHhsYaoFIrGezAiNTLQQPIRmg==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
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


        <form class="card-body" method="POST" action="{{ route('lead.store') }}" id="leadForm" onsubmit="return validateForm()">
                <!-- Multi Column with Form Separator -->
                <div class="card mb-4 px-3 py-5">
                    <h5 class="card-header">Insert Data of Sale</h5>
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
                                    <select id="category" name="category[]" class="select2 form-select" data-allow-clear="true" multiple required>
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
                                    <input type="text" id="business_name" name="business_name" class="form-control" required
                                        placeholder="John"
                                        onkeydown="return /[a-zA-Z\s]/.test(event.key) || event.key === 'Backspace' || event.key === 'Tab';" />
                                    <label for="multicol-first-name">Business Name <span style="color: #ff4d49">*</span></label>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="tel"  id="business_number" style="height: calc(2.940725rem + 2px);"
                                        name="business_number" class="form-control" placeholder=" "  required />
                                    <label for="multicol-last-name" id="business_number_label" style="padding-left: 45px ">Business Number <span style="color: #ff4d49">*</span></label>
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
                                    <input type="text" class="form-control " name="website_url" placeholder="Example.com"
                                        aria-label="Example.com" />
                                    <label for="multicol-phone">Website Url</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control " name="client_name" placeholder="Client Name"
                                        aria-label="client_name"
                                        onkeydown="return /[a-zA-Z\s]/.test(event.key) || event.key === 'Backspace' || event.key === 'Tab';" required />
                                    <label for="client_name">Client Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control " name="client_designation"
                                        placeholder="Client Designation" aria-label="client_designation"
                                        onkeydown="return /[a-zA-Z\s]/.test(event.key) || event.key === 'Backspace' || event.key === 'Tab';" />
                                    <label for="client_designation">Client Designation</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="countries" name="country" class="select2 form-select" data-allow-clear="true" >
                                        <option value="">Please Select</option>
                                    </select>
                                    <label for="multicol-country">Countries</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="states" name="states" class="select2 form-select" data-allow-clear="true" >
                                        <option value="">Please Select</option>
                                    </select>
                                    <label for="multicol-country">States</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="cities" name="cities" class="select2 form-select" data-allow-clear="true" >
                                        <option value="">Please Select</option>
                                    </select>
                                    <label for="multicol-country">Cities</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control " name="zip_code"
                                        placeholder="ZIP Code" aria-label="zip_code"
                                         />
                                    <label for="zip_code">ZIP Code</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="call-status" name="call_status" class="select2 form-select"
                                        data-allow-clear="true" required>
                                        <option value="">Please Select</option>
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
                                    <input type="text"  class="form-control flatpickr-input active" name="call_back_time"
                                        min="YYYY-MM-DD HH:MM" id="flatpickr-datetime" readonly="readonly" disabled>
                                    <label for="flatpickr-datetime">Call Back Time</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="lead-status" name="lead_status" class="select2 form-select"
                                        data-allow-clear="true" required>
                                        <option value="">Please Select</option>
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
                                    <select id="multicol-closers" name="closers[]" class="select2 form-select" multiple>
                                        <option value="">Please Select</option>
                                        @foreach ($closers as $user)
                                        @php
                                            $closerName = explode(' -', $user->name)[0];// Use the part after the hyphen, or the original text if no hyphen exists
                                        @endphp
                                        <option value="{{ $user->id }}">{{ $closerName }}</option>
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

                            <div id="repeater">


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

                            </div>

                                <!-- Hidden Template for New Repeater Item -->
                        </div>
                    </div>
                </div>


                <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
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
    const input2 = document.querySelector("#business_number2");
    const iti =  intlTelInput(input, {
  initialCountry: "us",
  strictMode: true,
  utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/24.6.0/build/js/utils.min.js"
});
</script>

    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.js" integrity="sha512-bZAXvpVfp1+9AUHQzekEZaXclsgSlAeEnMJ6LfFAvjqYUVZfcuVXeQoN5LhD7Uw0Jy4NCY9q3kbdEXbwhZUmUQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
    $('#repeater').repeater({
        isFirstItemUndeletable: true,  // Optional: Prevent deleting the first item
        show: function() {
            $(this).slideDown();  // When the item is added, slide it down
        },
        hide: function(deleteElement) {
            $(this).slideUp(deleteElement);  // When the item is removed, slide it up
        }
    });
});

    </script> --}}

<script src="{{ asset('assets/js/leadreapeter.js') }}"></script>
<script>
    $(function(){
        $("#repeater").createRepeater_lead();
    });
</script>
{{-- <script>
    $(document).ready(function () {
        // Add a new repeater item on Add button click
        $('.repeater-add-btn').on('click', function () {
            let repeaterItem = $('.items').clone();
            repeaterItem.removeClass('d-none').removeAttr('id');
            $('#repeater').append(repeaterItem);
        });
    });
</script> --}}
<script>
    $('#category').change(function () {
        var selectedCategories = $(this).val(); // Get all selected categories as an array
        var subcategories = $('#multicol-language');

        // Clear existing subcategories
        subcategories.empty();
        subcategories.append('<option value="">Please Select</option>');

        if (selectedCategories && selectedCategories.length > 0) {
            subcategories.prop('disabled', false);

            // Loop through all selected categories and fetch subcategories for each
            $.each(selectedCategories, function (index, categoryId) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('front.get_subcategory') }}",
                    data: {'selected': categoryId}, // Send one category at a time to get its subcategories
                    success: function (response) {
                        var sub_category = response.sub_category;

                        // Loop through subcategories and add them to the dropdown
                        $.each(sub_category, function (i, item) {
                            // Avoid adding duplicate subcategory options
                            if ($('#multicol-language option[value="' + item.id + '"]').length === 0) {
                                subcategories.append('<option value="' + item.id + '">' + item.name + '</option>');
                            }
                        });
                    }
                });
            });
        } else {
            subcategories.prop('disabled', true); // Disable subcategories if no categories are selected
        }
    });
</script>


{{-- <script>
    $('#category').select(function (e) {
        alert("sdfsdfasd");
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

            //    subcategories.empty();
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
</script> --}}
<script>
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
</script>
<script>
    function validateForm() {
        const businessName = document.getElementById('business_name').value;
        const businessNumber = document.getElementById('business_number').value;
        const email = document.getElementById('basic-default-email').value;
        const websiteUrl = document.getElementsByName('website_url')[0].value;
        const clientName = document.getElementsByName('client_name')[0].value;
        const clientAddress = document.getElementsByName('client_address')[0].value;
        const phoneNumber = document.getElementById('phone_number').value;  // Assuming you have a phone number field

        // Check if all required fields are set
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



        if (email && email.trim() !== "") {
            // Validate email format (must contain "@" and a valid domain)
            const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(email)) {
                alert('Invalid Email. Please enter a valid email address (e.g., user@example.com).');
                return false;
            }
        }

        if (websiteUrl && websiteUrl.trim() !== "") {
            // Validate website URL (must be a valid URL with domain)
            const urlPattern = /^(https?:\/\/)?([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?$/;
            if (!urlPattern.test(websiteUrl)) {
                alert('Invalid Website URL. Please enter a valid URL (e.g., http://example.com).');
                return false;
            }
        }

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

        if (clientAddress && clientAddress.trim() !== "") {
            // Validate client address (allowing letters, digits, and certain symbols)
            if (!/^[a-zA-Z0-9\/\-\_\:\, ]*$/.test(clientAddress)) {
                alert('Invalid Client Address. Only letters, numbers, and certain symbols are allowed.');
                return false;
            }
        }

        // Validate phone number (assuming you're using a library like intl-tel-input for validation)
        if (phoneNumber && phoneNumber.trim() !== "") {
            if (!iti.isValidNumber()) {
                alert('Invalid Phone Number. Please enter a valid phone number.');
                return false;
            }
        } else {
            alert('Phone Number is required.');
            return false;
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
    const inputField = document.querySelector("#business_number");
    const label = document.querySelector("#business_number_label");

    // Hide the label when the input is focused
    inputField.addEventListener("focus", function() {
        label.style.display = "none"; // Hide label
    });

    // Show the label again when the input loses focus, but only if the field is empty
    inputField.addEventListener("blur", function() {
        if (inputField.value === "") {
            label.style.display = "block"; // Show label if the input is empty
        }
    });
</script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/24.6.0/build/js/intlTelInput.min.js" integrity="sha512-/sRFlFRbcvObOo/SxW8pvmFZeMLvAF6hajRXeX15ekPgT4guXnfNSjLC98K/Tg2ObUgKX8vn9+Th5/mGHzZbEw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
var input = document.querySelector("#business_number");
window.intlTelInput(input,({
 allowDropdown:true,

}));

</script> --}}
@endsection
