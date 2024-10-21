@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css') }}" />
{{-- <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" /> --}}
<style>
    .col-md-5 {
        margin-top: 0.5rem;
    }
</style>
@endsection
@section('content')
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Form Wizard /</span> Icons</h4>

        <!-- Default -->
        <div class="row">

            <!-- Default Icons Wizard -->
            <div class="col-12 mb-4">
                <div class="bs-stepper wizard-icons wizard-icons-example mt-2">
                    <div class="bs-stepper-header">
                        <div class="step" data-target="#account-details">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-icon">
                                    <svg viewBox="0 0 54 54">
                                        <use xlink:href="../../assets/svg/icons/form-wizard-account.svg#wizardAccount">
                                        </use>
                                    </svg>
                                </span>
                                <span class="bs-stepper-label">Client Details</span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#personal-info">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-icon">
                                    <svg viewBox="0 0 58 54">
                                        <use
                                            xlink:href="../../assets/svg/icons/form-wizard-personal.svg#wizardPersonal">
                                        </use>
                                    </svg>
                                </span>
                                <span class="bs-stepper-label">Sale Info</span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#address">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-icon">
                                    <svg viewBox="0 0 54 54">
                                        <use xlink:href="../../assets/svg/icons/form-wizard-address.svg#wizardAddress">
                                        </use>
                                    </svg>
                                </span>
                                <span class="bs-stepper-label">Services</span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#social-links">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-icon">
                                    <svg viewBox="0 0 54 54">
                                        <use
                                            xlink:href="../../assets/svg/icons/form-wizard-social-link.svg#wizardSocialLink">
                                        </use>
                                    </svg>
                                </span>
                                <span class="bs-stepper-label">Keywords</span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#review-submit">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-icon">
                                    <svg viewBox="0 0 54 54">
                                        <use xlink:href="../../assets/svg/icons/form-wizard-submit.svg#wizardSubmit">
                                        </use>
                                    </svg>
                                </span>
                                <span class="bs-stepper-label">Client Reporting</span>
                            </button>
                        </div>
                    </div>
                    <div class="bs-stepper-content">
                        <form onSubmit="return false">
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
                            <!-- Account Details -->
                            <div id="account-details" class="content">
                                <div class="content-header mb-3">
                                    <h6 class="mb-0">Lead Details</h6>
                                    <small>From Lead Model</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select id="client_nature" name="category" class="select2 form-select"
                                                data-allow-clear="true">
                                                <option value="">Please Select</option>
                                                @foreach ($client_enum as $item)
                                                <option value="{{$item}}">{{$item}}</option>
                                                @endforeach

                                            </select>
                                            <label for="multicol-country">Client Nature</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="business_name" name="business_name"
                                                class="form-control" placeholder="John"
                                                value="{{ $lead->business_name_adv }}"
                                                onkeydown="return /[a-zA-Z\s]/.test(event.key) || event.key === 'Backspace' || event.key === 'Tab';" />
                                            <label for="multicol-first-name">Business Name Adv</label>
                                        </div>
                                        @error('business_name')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="tel" pattern="\d*" maxlength="15" id="business_number"
                                                name="business_number" class="form-control" placeholder="
                                        +1111111111" value="{{ $lead->business_number_adv }}" />
                                            <label for="multicol-last-name">Business Number Adv</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control " name="client_name"
                                                placeholder="Client Name" aria-label="client_name"
                                                value="{{ $lead->client_name }}"
                                                onkeydown="return /[a-zA-Z\s]/.test(event.key) || event.key === 'Backspace' || event.key === 'Tab';" />
                                            <label for="client_name">Client Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="email" id="basic-default-email" name="email"
                                                        class="form-control" placeholder="john.doe"
                                                        aria-label="john.doe" value="{{ $lead->off_email }}"
                                                        aria-describedby="basic-default-email2">
                                                    <label for="basic-default-email">Official Email</label>
                                                </div>
                                                <span class="input-group-text"
                                                    id="basic-default-email2">@example.com</span>
                                            </div>
                                            <div class="form-text">You can use letters, numbers &amp; periods</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control " name="website_url"
                                                placeholder="Example.com" value="{{ $lead->website_url }}"
                                                aria-label="Example.com" />
                                            <label for="multicol-phone">Website Url</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select id="category" name="category" class="select2 form-select"
                                                data-allow-clear="true">
                                                <option value="">Please Select</option>
                                                @foreach ($call_enum as $item)
                                                <option value="{{$item}}">{{$item}}</option>
                                                @endforeach

                                            </select>
                                            <label for="multicol-country">Call Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control " name="time_zone"
                                                placeholder="Client Name" aria-label="time_zone" />
                                            <label for="time_zone">Time Zone</label>
                                        </div>
                                    </div>
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">Business Hours</h6>
                                        {{-- <small>From Lead Model</small> --}}
                                    </div>
                                    <div class="col-md-12" style="display: flex; flex-direction: column; gap: 25px;">
                                        <!-- Day -->
                                        <div class="row opening-day">
                                            <div class="col-md-2">
                                                <h5>Monday <input type="hidden" name="day[]" value="Monday"></h5>
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="open[]" id="monday_open">
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="closed[]" id="monday_closed">
                                            </div>
                                            <div class="col-md-2 d-flex" style="gap: 20px;">
                                                <div class="form-check custom-option custom-option-basic checked">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp1">
                                                        <input  data-day="check" data-day-name="monday" name="check[]" class="form-check-input" type="radio" value="open" id="customRadioTemp1"
                                                            checked="">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Open</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp2">
                                                        <input  data-day="check" data-day-name="monday" name="check[]" class="form-check-input" type="radio" value="closed"
                                                            id="customRadioTemp2">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Closed</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp3">
                                                        <input  data-day="check" data-day-name="monday" name="check[]" class="form-check-input" type="radio" value="24/7"
                                                            id="customRadioTemp3">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">24/7</span>
                                                        </span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Day / End -->

                                        <!-- Day -->
                                        <div class="row opening-day">
                                            <div class="col-md-2">
                                                <h5>Tuesday <input type="hidden" name="day[]" value="Tuesday"></h5>
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="open[]" id="tuesday_open">
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="closed[]" id="tuesday_closed">
                                            </div>
                                            <div class="col-md-2 d-flex" style="gap: 20px;">
                                                <div class="form-check custom-option custom-option-basic checked">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp4">
                                                        <input  data-day="check" data-day-name="tuesday" name="check[]" class="form-check-input" type="radio" value="open" id="customRadioTemp4"
                                                            checked="">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Open</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp5">
                                                        <input  data-day="check" data-day-name="tuesday" name="check[]" class="form-check-input" type="radio" value="closed"
                                                            id="customRadioTemp5">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Closed</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp6">
                                                        <input  data-day="check" data-day-name="tuesday" name="check[]" class="form-check-input" type="radio" value="24/7"
                                                            id="customRadioTemp6">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">24/7</span>
                                                        </span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Day / End -->

                                        <!-- Day -->
                                        <div class="row opening-day">
                                            <div class="col-md-2">
                                                <h5>Wednesday <input type="hidden" name="day[]" value="Wednesday"></h5>
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="open[]" id="wednesday_open">
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="closed[]" id="wednesday_closed">
                                            </div>
                                            <div class="col-md-2 d-flex" style="gap: 20px;">
                                                <div class="form-check custom-option custom-option-basic checked">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp7">
                                                        <input  data-day="check" data-day-name="wednesday" name="check[]" class="form-check-input" type="radio" value="open" id="customRadioTemp7"
                                                            checked="">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Open</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp8">
                                                        <input  data-day="check" data-day-name="wednesday" name="check[]" class="form-check-input" type="radio" value="closed"
                                                            id="customRadioTemp8">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Closed</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp9">
                                                        <input  data-day="check" data-day-name="wednesday" name="check[]" class="form-check-input" type="radio" value="24/7"
                                                            id="customRadioTemp9">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">24/7</span>
                                                        </span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Day / End -->

                                        <!-- Day -->
                                        <div class="row opening-day">
                                            <div class="col-md-2">
                                                <h5>Thursday <input type="hidden" name="day[]" value="Thursday"></h5>
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="open[]" id="thursday_open">
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="closed[]" id="thursday_closed">
                                            </div>
                                            <div class="col-md-2 d-flex" style="gap: 20px;">
                                                <div class="form-check custom-option custom-option-basic checked">
                                                    <label   class="form-check-label custom-option-content" for="customRadioTemp10">
                                                        <input data-day="check" data-day-name="thursday" name="check[]" class="form-check-input" type="radio" value="open" id="customRadioTemp10"
                                                            checked="">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Open</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp11">
                                                        <input data-day="check" data-day-name="thursday" name="check[]" class="form-check-input" type="radio" value="closed"
                                                            id="customRadioTemp11">
                                                        <span class="custom-option-header">
                                                            <span  class="h6 mb-0">Closed</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp12">
                                                        <input data-day="check" data-day-name="thursday" name="check[]" class="form-check-input" type="radio" value="24/7"
                                                            id="customRadioTemp12">
                                                        <span class="custom-option-header">
                                                            <span  class="h6 mb-0">24/7</span>
                                                        </span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Day / End -->

                                        <!-- Day -->
                                        <div class="row opening-day">
                                            <div class="col-md-2">
                                                <h5>Friday <input type="hidden" name="day[]" value="Friday"></h5>
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="open[]" id="friday_open">
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="closed[]" id="friday_closed">
                                            </div>
                                            <div class="col-md-2 d-flex" style="gap: 20px;">
                                                <div class="form-check custom-option custom-option-basic checked">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp13">
                                                        <input  data-day="check" data-day-name="friday" name="check[]" class="form-check-input" type="radio" value="open" id="customRadioTemp13"
                                                            checked="">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Open</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp14">
                                                        <input  data-day="check" data-day-name="friday" name="check[]" class="form-check-input" type="radio" value="closed"
                                                            id="customRadioTemp14">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Closed</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp15">
                                                        <input  data-day="check" data-day-name="friday" name="check[]" class="form-check-input" type="radio" value="24/7"
                                                            id="customRadioTemp15">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">24/7</span>
                                                        </span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Day / End -->

                                        <!-- Day -->
                                        <div class="row opening-day">
                                            <div class="col-md-2">
                                                <h5>Saturday <input type="hidden" name="day[]" value="Saturday"></h5>
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="open[]" id="saturday_open">
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="closed[]" id="saturday_closed">
                                            </div>
                                            <div class="col-md-2 d-flex" style="gap: 20px;">
                                                <div class="form-check custom-option custom-option-basic checked">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp16">
                                                        <input  data-day="check" data-day-name="saturday" name="saturday_check" class="form-check-input" type="radio" value="open" id="customRadioTemp16"
                                                            checked="">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Open</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp17">
                                                        <input  data-day="check" data-day-name="saturday" name="saturday_check" class="form-check-input" type="radio" value="closed"
                                                            id="customRadioTemp17">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Closed</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp18">
                                                        <input  data-day="check" data-day-name="saturday" name="saturday_check" class="form-check-input" type="radio" value="24/7"
                                                            id="customRadioTemp18">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">24/7</span>
                                                        </span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Day / End -->

                                        <!-- Day -->
                                        <div class="row opening-day">
                                            <div class="col-md-2">
                                                <h5>Sunday <input type="hidden" name="day[]" value="Sunday"></h5>
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="open[]" id="sunday_open">
                                            </div>
                                            <div class="col-md-3 form-floating form-floating-outline">
                                                <input type="time" class="form-control flatpickr-input active" name="closed[]" id="sunday_closed">
                                            </div>
                                            <div class="col-md-2 d-flex" style="gap: 20px;">
                                                <div class="form-check custom-option custom-option-basic checked">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp19">
                                                        <input  data-day="check" data-day-name="sunday" name="sunday_check" class="form-check-input" type="radio" value="open" id="customRadioTemp19"
                                                            checked="">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Open</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp20">
                                                        <input  data-day="check" data-day-name="sunday" name="sunday_check" class="form-check-input" type="radio" value="closed"
                                                            id="customRadioTemp20">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">Closed</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check custom-option custom-option-basic">
                                                    <label class="form-check-label custom-option-content" for="customRadioTemp21">
                                                        <input  data-day="check" data-day-name="sunday" name="sunday_check" class="form-check-input" type="radio" value="24/7"
                                                            id="customRadioTemp21">
                                                        <span class="custom-option-header">
                                                            <span class="h6 mb-0">24/7</span>
                                                        </span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Day / End -->
                                    </div>

                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">Social Link</h6>
                                        {{-- <small>From Lead Model</small> --}}
                                    </div>
                                    <!-- Form Repeater -->
                                    <div class="col-12">
                                        <div class="card">
                                            <h5 class="card-header">Form Repeater</h5>
                                            <div class="card-body">
                                                <form class="form-repeater">
                                                    <div data-repeater-list="group-a">
                                                        <div data-repeater-item>
                                                            <div class="row">
                                                                <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                                                                    <div class="form-floating form-floating-outline">
                                                                        <input type="text" id="form-repeater-1-1"
                                                                            class="form-control"
                                                                            placeholder="john.doe" />
                                                                        <label for="form-repeater-1-1">Username</label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                                                                    <div class="form-floating form-floating-outline">
                                                                        <input type="password" id="form-repeater-1-2"
                                                                            class="form-control"
                                                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                                                        <label for="form-repeater-1-2">Password</label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 col-lg-6 col-xl-2 col-12 mb-0">
                                                                    <div class="form-floating form-floating-outline">
                                                                        <select id="form-repeater-1-3"
                                                                            class="form-select">
                                                                            <option value="Male">Male</option>
                                                                            <option value="Female">Female</option>
                                                                        </select>
                                                                        <label for="form-repeater-1-3">Gender</label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 col-lg-6 col-xl-2 col-12 mb-0">
                                                                    <div class="form-floating form-floating-outline">
                                                                        <select id="form-repeater-1-4"
                                                                            class="form-select">
                                                                            <option value="Designer">Designer</option>
                                                                            <option value="Developer">Developer</option>
                                                                            <option value="Tester">Tester</option>
                                                                            <option value="Manager">Manager</option>
                                                                        </select>
                                                                        <label
                                                                            for="form-repeater-1-4">Profession</label>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="mb-3 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                                                    <button class="btn btn-outline-danger"
                                                                        data-repeater-delete>
                                                                        <i class="mdi mdi-close me-1"></i>
                                                                        <span class="align-middle">Delete</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <hr />
                                                        </div>
                                                    </div>
                                                    <div class="mb-0">
                                                        <button class="btn btn-primary" data-repeater-create>
                                                            <i class="mdi mdi-plus me-1"></i>
                                                            <span class="align-middle">Add</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Form Repeater -->
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-outline-secondary btn-prev" disabled>
                                            <i class="mdi mdi-arrow-left me-sm-1"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                            <i class="mdi mdi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Personal Info -->
                            <div id="personal-info" class="content">
                                {{-- <div class="content-header mb-3">
                                    <h6 class="mb-0">Personal Info</h6>
                                    <small>Enter Your Personal Info.</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="first-name" class="form-control"
                                                placeholder="John" />
                                            <label for="first-name">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="last-name" class="form-control" placeholder="Doe" />
                                            <label for="last-name">Last Name</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2" id="country">
                                                <option label=" "></option>
                                                <option>UK</option>
                                                <option>USA</option>
                                                <option>Spain</option>
                                                <option>France</option>
                                                <option>Italy</option>
                                                <option>Australia</option>
                                            </select>
                                            <label for="country">Country</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="selectpicker w-auto" id="language"
                                                data-style="btn-transparent" data-icon-base="mdi"
                                                data-tick-icon="mdi-check text-white" multiple>
                                                <option>English</option>
                                                <option>French</option>
                                                <option>Spanish</option>
                                            </select>
                                            <label for="language">Language</label>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-outline-secondary btn-prev">
                                            <i class="mdi mdi-arrow-left me-sm-1"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                            <i class="mdi mdi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div> --}}
                            </div>
                            <!-- Address -->
                            <div id="address" class="content">
                                {{-- <div class="content-header mb-3">
                                    <h6 class="mb-0">Address</h6>
                                    <small>Enter Your Address.</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="address-input"
                                                placeholder="98  Borough bridge Road, Birmingham" />
                                            <label for="address-input">Address</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="landmark"
                                                placeholder="Borough bridge" />
                                            <label for="landmark">Landmark</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="pincode" placeholder="658921" />
                                            <label for="pincode">Pincode</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="city"
                                                placeholder="Birmingham" />
                                            <label for="city">City</label>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-outline-secondary btn-prev">
                                            <i class="mdi mdi-arrow-left me-sm-1"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                            <i class="mdi mdi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div> --}}
                            </div>
                            <!-- Social Links -->
                            <div id="social-links" class="content">
                                {{-- <div class="content-header mb-3">
                                    <h6 class="mb-0">Social Links</h6>
                                    <small>Enter Your Social Links.</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="twitter" class="form-control"
                                                placeholder="https://twitter.com/abc" />
                                            <label for="twitter">Twitter</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="facebook" class="form-control"
                                                placeholder="https://facebook.com/abc" />
                                            <label for="facebook">Facebook</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="google" class="form-control"
                                                placeholder="https://plus.google.com/abc" />
                                            <label for="google">Google+</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="linkedin" class="form-control"
                                                placeholder="https://linkedin.com/abc" />
                                            <label for="linkedin">Linkedin</label>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-outline-secondary btn-prev">
                                            <i class="mdi mdi-arrow-left me-sm-1"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                            <i class="mdi mdi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div> --}}
                            </div>
                            <!-- Review -->
                            <div id="review-submit" class="content">

                                <div class="col-12 d-flex justify-content-between">
                                    <button class="btn btn-outline-secondary btn-prev">
                                        <i class="mdi mdi-arrow-left me-sm-1"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                    </button>
                                    <button class="btn btn-primary btn-submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Default Icons Wizard -->


            <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
    </div>
    @endsection
    @section('js')
    <script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/js/form-wizard-icons.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
    <script src="{{ asset('assets/js/forms-extras.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/autosize/autosize.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js') }}"></script>
    {{-- <script src="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets/js/forms-pickers.js') }}"></script> --}}

    <script>
        if (formRepeater.length) {
    var row = 2;
    var col = 1;
    formRepeater.on('submit', function (e) {
      e.preventDefault();
    });
    formRepeater.repeater({
      show: function () {
        var fromControl = $(this).find('.form-control, .form-select');
        var formLabel = $(this).find('.form-label');

        fromControl.each(function (i) {
          var id = 'form-repeater-' + row + '-' + col;
          $(fromControl[i]).attr('id', id);
          $(formLabel[i]).attr('for', id);
          col++;
        });

        row++;

        $(this).slideDown();
      },
      hide: function (e) {
        confirm('Are you sure you want to delete this element?') && $(this).slideUp(e);
      }
    });
  }
    </script>

<script>
    $(document).ready(function() {
        $('input[data-day="check"]').change(function(e) {
            e.preventDefault();
            var selected = $(this).val(); // Get the value of the checked radio button
            var day = $(this).data('day-name'); // Get the day from the data attribute

            if (selected == "closed" || selected == "24/7") {
                $('#' + day + '_open').prop('disabled', true);
                $('#' + day + '_closed').prop('disabled', true);
            } else {
                $('#' + day + '_open').prop('disabled', false);
                $('#' + day + '_closed').prop('disabled', false);
            }
        });
    });
    </script>

    {{-- <script>
        $(document).ready(function() {
            $('input[name$="_check"]').change(function(e) {
                e.preventDefault();
                var selected = $(this).val(); // Get the value of the checked radio button
                var day = $(this).attr('name').split('_')[0]; // Get the day (e.g., "monday", "tuesday")

                if (selected == "closed" || selected == "24/7") {
                    $('#' + day + '_open').prop('disabled', true);
                    $('#' + day + '_closed').prop('disabled', true);
                } else {
                    $('#' + day + '_open').prop('disabled', false);
                    $('#' + day + '_closed').prop('disabled', false);
                }
            });
        });
        </script> --}}

    {{-- <script>
    $('input[name="monday_check"]').change(function (e) {
        e.preventDefault();
        var selected = $(this).val(); // Get the value of the checked radio button
        if(selected == "closed" || selected == "24/7"){
            $('#monday_open').prop('disabled', true);
            $('#monday_closed').prop('disabled', true);
        }
        else{
            $('#monday_open').prop('disabled', false);
            $('#monday_closed').prop('disabled', false);
        }
    });

    </script> --}}

    @endsection
