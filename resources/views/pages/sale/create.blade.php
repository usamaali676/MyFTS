@php
    $user = auth()->user();
    $sale_perm =  App\Models\Permission::where('role_id', $user->role_id)->where('name', "sale")->first();
    $Saleinfo = App\Models\Permission::where('role_id', $user->role_id)->where('name', "saleinfo")->first();
    // dd($Saleinfo);
    $Clientservices_perm = App\Models\Permission::where('role_id', $user->role_id)->where('name', "clientservices")->first();
    $Servicearea_perm = App\Models\Permission::where('role_id', $user->role_id)->where('name', "servicearea")->first();
    $Keyword_perm = App\Models\Permission::where('role_id', $user->role_id)->where('name', "keyword")->first();
    $Invoicecharges_perm = App\Models\Permission::where('role_id', $user->role_id)->where('name', "invoicecharges")->first();
    $Payment_perm = App\Models\Permission::where('role_id', $user->role_id)->where('name', "payment")->first();
@endphp
@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
{{-- <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" /> --}}
{{-- <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" /> --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/24.6.0/build/css/intlTelInput.min.css" integrity="sha512-X3pJz9m4oT4uHCYS6UjxVdWk1yxSJJIJOJMIkf7TjPpb1BzugjiFyHu7WsXQvMMMZTnGUA9Q/GyxxCWNDZpdHA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/plugins/monthSelect/style.min.css"  />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />

<link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
    rel="stylesheet"
/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css">

{{-- <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" /> --}}

<style>
    .col-md-5 {
        margin-top: 0.5rem;
    }

    .iti{
        width: 100%;
        padding: 0px;
    }
    .iti__dropdown-content{
        z-index: 20 !important;
    }
    ul.ui-menu.ui-widget.ui-widget-content.ui-autocomplete.ui-front {
    background: #fff;
    box-shadow: 0 0.125rem 0.625rem 0 rgba(76, 78, 100, 0.22);
    width: fit-content;
    list-style: none;
}
ul.ui-menu.ui-widget.ui-widget-content.ui-autocomplete.ui-front li{
    padding: 10px 0;
}
.flatpickr-monthSelect-theme-dark .flatpickr-current-month input.cur-year{
    color: #000 !important;
}
.flatpickr-monthSelect-theme-dark .flatpickr-monthSelect-month{
    color: #000 !important;
}
/* .light-style .flatpickr-calendar, .light-style .flatpickr-days{
    width: calc(19.375rem + 0* 2px) !important;
} */
</style>
@endsection
@section('content')
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sale /</span> Client Details</h4>
        </div>

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
                                <span class="bs-stepper-label">Invoice & Payments</span>
                            </button>
                        </div>
                        <div class="step" data-target="#refund-chargeback">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-icon">
                                    <svg viewBox="0 0 54 54">
                                        <use xlink:href="../../assets/svg/icons/form-wizard-submit.svg#wizardSubmit">
                                        </use>
                                    </svg>
                                </span>
                                <span class="bs-stepper-label">Refund & Charge Backs</span>
                            </button>
                        </div>
                        <div class="step" data-target="#reports">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-icon">
                                    <svg viewBox="0 0 54 54">
                                        <use xlink:href="../../assets/svg/icons/form-wizard-submit.svg#wizardSubmit">
                                        </use>
                                    </svg>
                                </span>
                                <span class="bs-stepper-label">Reports</span>
                            </button>
                        </div>
                    </div>
                    <div class="bs-stepper-content">
                            <form id="saleForm" method="POST" action="{{ route('sale.store') }}" onsubmit="return validateForm()">
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
                                <div id="successMessage" style="display:none;" class="alert alert-success"></div>
                                <!-- Account Details -->
                                <div id="account-details" class="content">
                                    @if(isset($sale_perm) && $sale_perm->create == 1)
                                        <div class="content-header mb-3">
                                            <h6 class="mb-0">Lead Details</h6>
                                            <small>From Lead Model</small>
                                        </div>
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <input type="hidden" name="lead_id" value="{{ $lead->id }}" id="">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="client_nature" name="client_nature" class="select2 form-select"
                                                        data-allow-clear="true">
                                                        @if(isset($sale) && isset($sale->client_nature))
                                                        <option value="{{$sale->client_nature}}" selected>{{ $sale->client_nature }}</option>
                                                        @else
                                                        <option value="">Please Select</option>
                                                        @endif
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
                                                    <input type="tel"  id="business_number" style="height: calc(2.940725rem + 2px);"
                                                        name="business_number" class="form-control"  value="{{ $lead->business_number_adv }}" />
                                                    {{-- <label for="multicol-last-name">Business Number Adv</label> --}}
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
                                                    <select id="category" name="call_type" class="select2 form-select"
                                                        data-allow-clear="true">
                                                        @if(isset($sale) && isset($sale->call_type))
                                                        <option value="{{$sale->call_type}}" selected>{{ $sale->call_type }}</option>
                                                        @else
                                                        <option value="">Please Select</option>
                                                        @endif
                                                        @foreach ($call_enum as $item)
                                                        <option value="{{$item}}">{{$item}}</option>
                                                        @endforeach

                                                    </select>
                                                    <label for="multicol-country">Call Type</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                <select name="timezone" class="select2 form-select" data-allow-clear="true">
                                                    @if(isset($sale) && isset($sale->time_zone))
                                                    <option value="{{$sale->time_zone}}" selected>{{ $sale->time_zone }}</option>
                                                    @endif
                                                    <optgroup label="US Time Zones">
                                                        <option value="Eastern Time (ET)">Eastern Time (ET)</option>
                                                        <option value="Central Time (CT)">Central Time (CT)</option>
                                                        <option value="Mountain Time (MT)">Mountain Time (MT)</option>
                                                        <option value="Pacific Time (PT)">Pacific Time (PT)</option>
                                                        <option value="Alaska Time (AKT)">Alaska Time (AKT)</option>
                                                        <option value="Hawaii-Aleutian Time (HAST)">Hawaii-Aleutian Time (HAST)</option>
                                                    </optgroup>

                                                        <optgroup label="Global Time Zones">
                                                            <!-- Africa -->
                                                            <option value="GMT (Abidjan)">GMT (Abidjan)</option>
                                                            <option value="GMT (Accra)">GMT (Accra)</option>
                                                            <option value="EAT (Addis Ababa)">EAT (Addis Ababa)</option>
                                                            <option value="CET (Algiers)">CET (Algiers)</option>
                                                            <option value="EAT (Asmara)">EAT (Asmara)</option>
                                                            <option value="WAT (Bangui)">WAT (Bangui)</option>
                                                            <option value="GMT (Banjul)">GMT (Banjul)</option>
                                                            <option value="GMT (Bissau)">GMT (Bissau)</option>
                                                            <option value="CAT (Blantyre)">CAT (Blantyre)</option>
                                                            <option value="WAT (Brazzaville)">WAT (Brazzaville)</option>
                                                            <option value="CAT (Bujumbura)">CAT (Bujumbura)</option>
                                                            <option value="EET (Cairo)">EET (Cairo)</option>
                                                            <option value="WET (Casablanca)">WET (Casablanca)</option>
                                                            <option value="CET (Ceuta)">CET (Ceuta)</option>
                                                            <option value="GMT (Conakry)">GMT (Conakry)</option>
                                                            <option value="GMT (Dakar)">GMT (Dakar)</option>
                                                            <option value="EAT (Dar es Salaam)">EAT (Dar es Salaam)</option>
                                                            <option value="EAT (Djibouti)">EAT (Djibouti)</option>
                                                            <option value="WET (El Aaiun)">WET (El Aaiun)</option>
                                                            <option value="GMT (Freetown)">GMT (Freetown)</option>
                                                            <option value="CAT (Gaborone)">CAT (Gaborone)</option>
                                                            <option value="CAT (Harare)">CAT (Harare)</option>
                                                            <option value="SAST (Johannesburg)">SAST (Johannesburg)</option>
                                                            <option value="CAT (Juba)">CAT (Juba)</option>
                                                            <option value="EAT (Kampala)">EAT (Kampala)</option>
                                                            <option value="CAT (Khartoum)">CAT (Khartoum)</option>
                                                            <option value="CAT (Kigali)">CAT (Kigali)</option>
                                                            <option value="WAT (Kinshasa)">WAT (Kinshasa)</option>
                                                            <option value="WAT (Lagos)">WAT (Lagos)</option>
                                                            <option value="WAT (Libreville)">WAT (Libreville)</option>
                                                            <option value="GMT (Lome)">GMT (Lome)</option>
                                                            <option value="WAT (Luanda)">WAT (Luanda)</option>

                                                            <!-- Asia -->
                                                            <option value="India Standard Time (IST)">India Standard Time (IST)</option>
                                                            <option value="China Standard Time (CST)">China Standard Time (CST)</option>
                                                            <option value="Japan Standard Time (JST)">Japan Standard Time (JST)</option>
                                                            <option value="Malaysia Time (MYT)">Malaysia Time (MYT)</option>
                                                            <option value="Singapore Time (SGT)">Singapore Time (SGT)</option>
                                                            <option value="Korea Standard Time (KST)">Korea Standard Time (KST)</option>
                                                            <option value="Gulf Standard Time (GST)">Gulf Standard Time (GST)</option>
                                                            <option value="Arabian Standard Time (AST)">Arabian Standard Time (AST)</option>
                                                            <option value="Philippine Time (PHT)">Philippine Time (PHT)</option>
                                                            <option value="Hong Kong Time (HKT)">Hong Kong Time (HKT)</option>

                                                            <!-- Europe -->
                                                            <option value="Greenwich Mean Time (GMT)">Greenwich Mean Time (GMT)</option>
                                                            <option value="Central European Time (CET)">Central European Time (CET)</option>
                                                            <option value="Central European Time (CET)">Central European Time (CET)</option>
                                                            <option value="Central European Time (CET)">Central European Time (CET)</option>
                                                            <option value="Central European Time (CET)">Central European Time (CET)</option>
                                                            <option value="Central European Time (CET)">Central European Time (CET)</option>
                                                            <option value="Eastern European Time (EET)">Eastern European Time (EET)</option>
                                                            <option value="Moscow Time (MSK)">Moscow Time (MSK)</option>
                                                            <option value="Turkey Time (TRT)">Turkey Time (TRT)</option>

                                                            <!-- North America -->
                                                            <option value="Eastern Time (ET)">Eastern Time (ET)</option>
                                                            <option value="Pacific Time (PT)">Pacific Time (PT)</option>
                                                            <option value="Central Time (CT)">Central Time (CT)</option>

                                                            <!-- Oceania -->
                                                            <option value="Australian Eastern Standard Time (AEST)">Australian Eastern Standard Time (AEST)</option>
                                                            <option value="Australian Western Standard Time (AWST)">Australian Western Standard Time (AWST)</option>
                                                            <option value="New Zealand Standard Time (NZST)">New Zealand Standard Time (NZST)</option>
                                                            <option value="Chamorro Standard Time (ChST)">Chamorro Standard Time (ChST)</option>
                                                            <option value="Fiji Standard Time (FJT)">Fiji Standard Time (FJT)</option>

                                                            <!-- South America -->
                                                            <option value="Brasília Time (BRT)">Brasília Time (BRT)</option>
                                                            <option value="Argentina Time (ART)">Argentina Time (ART)</option>
                                                            <option value="Peru Time (PET)">Peru Time (PET)</option>
                                                            <option value="Uruguay Time (UYT)">Uruguay Time (UYT)</option>

                                                            <!-- UTC Offsets -->
                                                            <option value="UTC+0">UTC+0</option>
                                                            <option value="UTC+1">UTC+1</option>
                                                            <option value="UTC+2">UTC+2</option>
                                                            <option value="UTC+3">UTC+3</option>
                                                            <option value="UTC+4">UTC+4</option>
                                                            <option value="UTC+5">UTC+5</option>
                                                            <option value="UTC+6">UTC+6</option>
                                                            <option value="UTC+7">UTC+7</option>
                                                            <option value="UTC+8">UTC+8</option>
                                                            <option value="UTC+9">UTC+9</option>
                                                            <option value="UTC+10">UTC+10</option>
                                                            <option value="UTC+11">UTC+11</option>
                                                            <option value="UTC+12">UTC+12</option>
                                                            <option value="UTC-1">UTC-1</option>
                                                            <option value="UTC-2">UTC-2</option>
                                                            <option value="UTC-3">UTC-3</option>
                                                            <option value="UTC-4">UTC-4</option>
                                                            <option value="UTC-5">UTC-5</option>
                                                            <option value="UTC-6">UTC-6</option>
                                                            <option value="UTC-7">UTC-7</option>
                                                            <option value="UTC-8">UTC-8</option>
                                                            <option value="UTC-9">UTC-9</option>
                                                            <option value="UTC-10">UTC-10</option>
                                                            <option value="UTC-11">UTC-11</option>
                                                            <option value="UTC-12">UTC-12</option>
                                                        </optgroup>
                                                    </select>

                                                    <label for="time_zone">Time Zone</label>
                                                </div>
                                            </div>
                                            <div class="content-header mb-3">
                                                <h6 class="mb-0">Business Hours</h6>
                                                {{-- <small>From Lead Model</small> --}}
                                            </div>
                                            @if(isset($sale) && count($sale->business_hours) > 0)
                                            <div class="col-md-12" style="display: flex; flex-direction: column; gap: 25px;">
                                                @foreach($sale->business_hours as $index => $business_hour)
                                                <!-- Day -->
                                                <div class="row opening-day">
                                                    <div class="col-md-2">
                                                        <h5>{{ $business_hour->day }} <input type="hidden" name="day[]" value="{{ $business_hour->day }}"></h5>
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input" @if($business_hour->is_closed != 1 && $business_hour->{"is_24/7"} != 1) value="{{ $business_hour->opening_time }}" @endif name="open[]" id="{{ Str::lower($business_hour->day) }}_open">
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input" @if($business_hour->is_closed != 1 && $business_hour->{"is_24/7"} != 1) value="{{ $business_hour->closing_time }}" @endif name="closed[]" id="{{ Str::lower($business_hour->day) }}_closed">
                                                    </div>
                                                    <div class="col-md-2 d-flex" style="gap: 20px;">
                                                        <div class="form-check custom-option custom-option-basic checked">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp{{ $index }}_1">
                                                                <input  data-day="check" data-day-name="{{ Str::lower($business_hour->day) }}" name="{{ Str::lower($business_hour->day) }}_check" class="form-check-input" type="radio" @if($business_hour->is_closed != 1 && $business_hour->{"is_24/7"} != 1) checked @endif value="open" id="customRadioTemp{{ $index }}_1"
                                                                    checked="">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Open</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp{{ $index }}_2">
                                                                <input  data-day="check" data-day-name="{{ Str::lower($business_hour->day) }}" name="{{ Str::lower($business_hour->day) }}_check" class="form-check-input" type="radio" @if($business_hour->is_closed == 1) checked @endif value="closed"
                                                                    id="customRadioTemp{{ $index }}_2">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Closed</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp{{ $index }}_3">
                                                                <input  data-day="check" data-day-name="{{ Str::lower($business_hour->day) }}" name="{{ Str::lower($business_hour->day) }}_check" class="form-check-input" @if($business_hour->{"is_24/7"} == 1) checked @endif type="radio" value="24/7"
                                                                    id="customRadioTemp{{ $index }}_3">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">24/7</span>
                                                                </span>
                                                            </label>
                                                        </div>

                                                    </div>
                                                </div>
                                                <!-- Day / End -->
                                                @endforeach
                                            </div>
                                            @else
                                                @php
                                                    $business_hours = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday','Saturday','Sunday']
                                                @endphp
                                                <div class="col-md-12" style="display: flex; flex-direction: column; gap: 25px;">
                                                    @foreach($business_hours as $index=>$business_hour)
                                                    <!-- Day -->
                                                    <div class="row opening-day">
                                                        <div class="col-md-2">
                                                            <h5>{{ $business_hour }} <input type="hidden" name="day[]" value="{{ $business_hour }}"></h5>
                                                        </div>
                                                        <div class="col-md-3 form-floating form-floating-outline">
                                                            <input type="time" class="form-control flatpickr-input" value="" name="open[]" id="{{ Str::lower($business_hour) }}_open">
                                                        </div>
                                                        <div class="col-md-3 form-floating form-floating-outline">
                                                            <input type="time" class="form-control flatpickr-input" value="" name="closed[]" id="{{ Str::lower($business_hour) }}_closed">
                                                        </div>
                                                        <div class="col-md-2 d-flex" style="gap: 20px;">
                                                            <div class="form-check custom-option custom-option-basic checked">
                                                                <label class="form-check-label custom-option-content" for="customRadioTemp{{ $index }}_1">
                                                                    <input  data-day="check" data-day-name="{{ Str::lower($business_hour) }}" name="{{ Str::lower($business_hour) }}_check" class="form-check-input" type="radio" value="open" id="customRadioTemp{{ $index }}_1"
                                                                        checked="">
                                                                    <span class="custom-option-header">
                                                                        <span class="h6 mb-0">Open</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check custom-option custom-option-basic">
                                                                <label class="form-check-label custom-option-content" for="customRadioTemp{{ $index }}_2">
                                                                    <input  data-day="check" data-day-name="{{ Str::lower($business_hour) }}" name="{{ Str::lower($business_hour) }}_check" class="form-check-input" type="radio" value="closed"
                                                                        id="customRadioTemp{{ $index }}_2">
                                                                    <span class="custom-option-header">
                                                                        <span class="h6 mb-0">Closed</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check custom-option custom-option-basic">
                                                                <label class="form-check-label custom-option-content" for="customRadioTemp{{ $index }}_3">
                                                                    <input  data-day="check" data-day-name="{{ Str::lower($business_hour) }}" name="{{ Str::lower($business_hour) }}_check" class="form-check-input" type="radio" value="24/7"
                                                                        id="customRadioTemp{{ $index }}_3">
                                                                    <span class="custom-option-header">
                                                                        <span class="h6 mb-0">24/7</span>
                                                                    </span>
                                                                </label>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <!-- Day / End -->
                                                    @endforeach
                                                </div>
                                            @endif

                                            <!-- Copy buttons -->
                                            <div class="col-md-12" style="margin-top: 20px;">
                                                <button type="button" id="copyToAll" class="btn btn-primary">Copy to All Days</button>
                                                <button type="button" id="copyToWorkingDays" class="btn btn-secondary">Copy to Working Days (Mon-Fri)</button>
                                            </div>



                                            <div class="content-header mb-3">
                                                <h6 class="mb-0">Social Link</h6>
                                                {{-- <small>From Lead Model</small> --}}
                                            </div>
                                            <!-- Form Repeater -->
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div id="repeater">
                                                            {{-- <div class="items">
                                                                <!-- Repeater Content -->

                                                                <div class="item-content">
                                                                    <div class="row py-2">
                                                                        <div class="col-md-4">
                                                                            <div class="form-floating form-floating-outline">
                                                                                <select id="social_links" name="social_name[]" class="form-select" data-allow-clear="true">
                                                                                    <option value="">Please Select</option>
                                                                                    @foreach ($social_links as $item)
                                                                                    <option value="{{$item}}">{{$item}}</option>
                                                                                    @endforeach

                                                                                </select>
                                                                                <label for="multicol-country">Social Platform</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-floating form-floating-outline">
                                                                                <input type="text" class="form-control " name="social_link[]" placeholder="Social Link" id="social_link" placeholder
                                                                                    aria-label="social_link" />
                                                                                <label for="time_zone">Social Link</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="pull-right repeater-remove-btn">
                                                                                <a id="remove-btn" class="btn btn-outline-danger remove-btn waves-effect" style="color: #ff4d49 " disabled="true"
                                                                                    onclick="$(this).parents('.items').remove()">
                                                                                    Remove
                                                                            </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div> --}}
                                                            @if(isset($sale) && count($sale->social_links) > 0)
                                                            {{-- <h1>fdgsdfg</h1> --}}
                                                                @foreach($sale->social_links as $list)
                                                                <div class="items">
                                                                    <!-- Repeater Content -->
                                                                    <div class="item-content">
                                                                        <div class="row py-2">
                                                                            <div class="col-md-4">
                                                                                <div class="form-floating form-floating-outline">
                                                                                    <select id="social_links" name="social_name[]" class="form-select" data-allow-clear="true">
                                                                                        <option value="">Please Select</option>
                                                                                        <option value="{{ $list->social_name}}" selected>{{ $list->social_name }}</option>
                                                                                        @foreach ($social_links as $item)
                                                                                        <option value="{{$item}}">{{$item}}</option>
                                                                                        @endforeach

                                                                                    </select>
                                                                                    <label for="multicol-country">Social Platform</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-floating form-floating-outline">
                                                                                    <input type="text" class="form-control social_link" name="social_link[]" placeholder="Social Link" value="{{ $list->social_link }}" id="social_link" placeholder
                                                                                        aria-label="social_link" />
                                                                                    <label for="time_zone">Social Link</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="pull-right repeater-remove-btn">
                                                                                    <a id="remove-btn" class="btn btn-outline-danger remove-btn waves-effect" style="color: #ff4d49 " disabled="true"
                                                                                        onclick="$(this).parents('.items').remove()">
                                                                                        Remove
                                                                                </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            @else
                                                                <div class="items">
                                                                    <!-- Repeater Content -->
                                                                    {{-- <h1>dfgdfg</h1> --}}
                                                                    <div class="item-content">
                                                                        <div class="row py-2">
                                                                            <div class="col-md-4">
                                                                                <div class="form-floating form-floating-outline">
                                                                                    <select id="social_links" name="social_name[]" class="form-select" data-allow-clear="true">
                                                                                        <option value="">Please Select</option>
                                                                                        @foreach ($social_links as $item)
                                                                                        <option value="{{$item}}">{{$item}}</option>
                                                                                        @endforeach

                                                                                    </select>
                                                                                    <label for="multicol-country">Social Platform</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-floating form-floating-outline">
                                                                                    <input type="text" class="form-control " name="social_link[]" placeholder="Social Link" id="social_link" placeholder
                                                                                        aria-label="social_link" />
                                                                                    <label for="time_zone">Social Link</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="pull-right repeater-remove-btn">
                                                                                    <a id="remove-btn" class="btn btn-outline-danger remove-btn waves-effect" style="color: #ff4d49 " disabled="true"
                                                                                        onclick="$(this).parents('.items').remove()">
                                                                                        Remove
                                                                                </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="items" data-group="test">
                                                            </div>
                                                            <div class="repeater-footer py-4" style="display: flex; justify-content: flex-end;">
                                                                <a class="btn btn-primary repeater-add-btn" style="color: #fff">Add</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Form Repeater -->
                                            <div class="col-12 d-flex justify-content-between">
                                                <a class="btn btn-outline-secondary btn-prev" style="color: #6d788d" disabled>
                                                    <i class="mdi mdi-arrow-left me-sm-1"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                </a>
                                                <div class="last-buttons d-flex" style="gap: 20px;">
                                                    <button class="btn btn-outline-primary waves-effect" type="submit">Save</button>
                                                <button type="button" id="first_next" class="btn btn-primary btn-next" style="color: #fff" disabled>
                                                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                    <i class="mdi mdi-arrow-right"></i>
                                                </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                            <!-- Personal Info -->
                            <div id="personal-info" class="content">
                                @if(isset($Saleinfo) && $Saleinfo->view == 1)
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">Sale Info</h6>
                                    </div>
                                    <form id="detail_form" method="POST" action="{{ route('saleInfo.store') }}" >
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
                                        <div id="successMessage" style="display:none;" class="alert alert-success"></div>
                                    <div class="row g-4">
                                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                        @if(isset($sale))
                                        <input type="hidden" id="sale_id" name="sale_id" value="{{ $sale->id }}">
                                        @else
                                        <input type="hidden" id="sale_id" name="sale_id" value="">
                                        @endif
                                        <div class="col-sm-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" id="first-name" class="form-control"
                                                    placeholder="John" value="{{ $lead->saler->name }}" disabled/>
                                                <label for="first-name">Sale Rep</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 select2-primary">
                                            <div class="form-floating form-floating-outline">
                                                <select id="multicol-closers" name="closers[]" class="select2 form-select" multiple>
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

                                                </select>
                                                <label for="multicol-closers">Select Closers</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-6">
                                            <div class="form-floating form-floating-outline">
                                            @if(isset($sale) && isset($sale->signup_date))
                                            <input type="text" class="form-control flatpickr-input active" name="signup_date" placeholder="YYYY-MM-DD" id="flatpickr-date" value="{{ $sale->signup_date }}" readonly="readonly">
                                            @else
                                            <input type="text" class="form-control flatpickr-input active" name="signup_date" placeholder="YYYY-MM-DD" id="flatpickr-date"  readonly="readonly">
                                            @endif
                                            <label for="flatpickr-date">Signup Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 select2-primary">
                                            <div class="form-floating form-floating-outline">
                                                <select id="multicol-cs" name="customer_support[]" class="select2 form-select" multiple>
                                                    @if(isset($sale) && isset($sale->Customer_support))
                                                    @foreach ($sale->Customer_support as $item)
                                                    <option value="{{ $item->id }}" selected>{{ $item->user->name }}</option>
                                                    @endforeach
                                                    @foreach ($csr as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                    @else
                                                    <option value="">Please Select</option>
                                                    @foreach ($csr as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                    @endif


                                                </select>
                                                <label for="multicol-closers">Select Customer Support Representative</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="switch switch-lg">

                                                <input type="checkbox" class="switch-input" name="sale_status"
                                                    @if(isset($sale) &&
                                                    $sale->status == 1) checked
                                                        @if($user->role_id == 1 || $user->role->name == "Customer Support")
                                                        {{-- <p>{{ $user->role_id }}</p> --}}
                                                            @readonly(false)
                                                        @else
                                                            @readonly(true)
                                                        @endif
                                                    @else
                                                        @readonly(true)
                                                    @endif
                                                    >
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on">
                                                        <i class="ri-check-line"></i>
                                                    </span>
                                                    <span class="switch-off">
                                                        <i class="ri-close-line"></i>
                                                    </span>
                                                </span>
                                                <span class="switch-label">Sale Active Status</span>
                                            </label>
                                        </div>
                                        <div class="col-md-6 select2-primary">
                                            <div class="row">
                                                <div class="col-md-3"><h6>Activation Date</h6></div>
                                                @if(isset($sale) && $sale->activation_date != NULL)
                                                <div class="col-md-3">{{ $sale->activation_date }}</div>
                                                @else
                                                <div class="col-md-3">N/A</div>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- <div class="col-12 d-flex justify-content-between">
                                            <button class="btn btn-outline-secondary btn-prev">
                                                <i class="mdi mdi-arrow-left me-sm-1"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </button>
                                            <button class="btn btn-primary btn-next">
                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                <i class="mdi mdi-arrow-right"></i>
                                            </button>
                                        </div> --}}
                                        <div class="col-12 d-flex justify-content-between">
                                            <a class="btn btn-outline-secondary btn-prev" style="color: #6d788d" disabled>
                                                <i class="mdi mdi-arrow-left me-sm-1"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </a>
                                            <div class="last-buttons d-flex" style="gap: 20px;">
                                                <button class="btn btn-outline-primary waves-effect" type="submit">Save</button>
                                            <a class="btn btn-primary btn-next" style="color: #fff">
                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                <i class="mdi mdi-arrow-right"></i>
                                            </a>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                @endif
                            </div>
                            <!-- Address -->
                            <div id="address" class="content">
                                @if(isset($Clientservices_perm) && $Clientservices_perm->view == 1)
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">Services</h6>
                                    </div>
                                    <div class="row g-4">
                                        <form id="serviceform" method="POST" action="{{ route('clientServices.store') }}">
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
                                            @if(isset($sale))
                                            <input type="hidden" id="sale_id2" name="sale_id2" value="{{ $sale->id }}">
                                            @else
                                            <input type="hidden" id="sale_id2" name="sale_id2" value="">
                                            @endif
                                            <div class="row">
                                                <div class="col-sm-9">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" class="form-control" name="service_name" id="service_name" placeholder="Client Services" />
                                                        <label for="address-input">Client Services</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button class="btn btn-primary">Add</button>
                                                </div>
                                            </div>
                                        </form>
                                        <form id="sync_services" method="POST" action="{{ route('clientServices.create') }}">
                                            @csrf
                                            <div class="row g-4">
                                                @if(isset($sale))
                                                <input type="hidden" id="sale_id3" name="sale_id3" value="{{ $sale->id }}">
                                                @else
                                                <input type="hidden" id="sale_id3" name="sale_id3" value="">
                                                @endif
                                                <h6 class="mb-0">Services Selection</h6>
                                                <!-- Responsive Datatable -->
                                                <div class="card py-3 my-5">
                                                    {{-- <h5 class="card-header">Responsive Datatable</h5> --}}

                                                    <div class="card-datatable table-responsive" style="padding-bottom: 5rem">
                                                        <div class="table-responsive">
                                                        <table id="service_table" class="table table-bordered">
                                                        {{-- <table id="service_table" class=" table table-bordered"> --}}

                                                            <thead>
                                                                <tr>
                                                                    <th>Client Service Name</th>
                                                                    <th>Company Services</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(isset($sale) && $sale->clientServices->isNotEmpty())
                                                                @foreach ($sale->clientServices as $index => $s_service)
                                                                <tr>
                                                                    <td>{{ $s_service->name }}
                                                                        <input type="hidden"  name="client_service[{{ $index }}]" value="{{ $s_service->id }}" id="">
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12 select2-primary">
                                                                            <div class="form-floating form-floating-outline">
                                                                                <select name="company_service[{{ $index }}][]" class="select2 form-select" multiple >
                                                                                    <option value="">Please Select</option>
                                                                                    @php
                                                                                    // Get the specific `Company Services` for this `Sale` and `Client Service`
                                                                                    $selectedCompanyServiceIds = \App\Models\SaleClientServiceCompanyService::where('sale_id', $sale->id)
                                                                                        ->where('client_service_id', $s_service->id)
                                                                                        ->pluck('company_service_id')
                                                                                        ->toArray();
                                                                                @endphp

                                                                                @foreach ($company_services as $item)
                                                                                    <option value="{{ $item->id }}" {{ in_array($item->id, $selectedCompanyServiceIds) ? 'selected' : '' }}>
                                                                                        {{ $item->name }}
                                                                                    </option>
                                                                                @endforeach

                                                                                </select>
                                                                                <label for="multicol-closers">FTS Services</label>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <a class="service_delete" data-id="{{ $s_service->id }}"  style="font-size:20px;" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete"><i class="ri-delete-bin-5-line ri-50px"></i></a>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                                @endif

                                                            </tbody>

                                                        </table>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6"></div>
                                                        <div class="col-md-6" style="text-align: right">
                                                            <button class="btn btn-outline-primary waves-effect" type="submit">Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/ Responsive Datatable -->

                                            </div>

                                            {{-- <div class="row g-4">
                                                <h4>Client Service Area</h4>
                                                <form action="">
                                                <div class="row py-4">
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select id="countries" name="country" class="select2 form-select" data-allow-clear="true" >
                                                                <option value="">Please Select</option>
                                                            </select>
                                                            <label for="multicol-country">Countries</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select id="states" name="states" class="select2 form-select" data-allow-clear="true" >
                                                                <option value="">Please Select</option>
                                                            </select>
                                                            <label for="multicol-country">States</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <select id="cities" name="cities" class="select2 form-select" data-allow-clear="true" >
                                                                <option value="">Please Select</option>
                                                            </select>
                                                            <label for="multicol-country">Cities</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                </form>
                                            </div> --}}

                                        </form>
                                    </div>
                                @endif
                                @if(isset($Servicearea_perm) && $Servicearea_perm->view == 1)
                                    <div class="row g-4">
                                            <h4>Client Service Area</h4>
                                            <form id="service_area" action="{{ route('serviceArea.store') }}" method="POST" >
                                                @csrf
                                            <div class="row py-4">
                                                @if(isset($sale))
                                                <input type="hidden" value="{{ $sale->id }}" name="sale_id4" id="sale_id4">
                                                @else
                                                <input type="hidden" name="sale_id4" id="sale_id4">
                                                @endif
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="countries" name="country" class="select2 form-select" data-allow-clear="true" >
                                                            <option value="">Please Select</option>
                                                        </select>
                                                        <label for="multicol-country">Countries</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="states" name="states" class="select2 form-select" data-allow-clear="true" >
                                                            <option value="">Please Select</option>
                                                        </select>
                                                        <label for="multicol-country">States</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="cities" name="cities" class="select2 form-select" data-allow-clear="true" >
                                                            <option value="">Please Select</option>
                                                        </select>
                                                        <label for="multicol-country">Cities</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                            </form>
                                    </div>
                                    <div class="row g-4">

                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="areas_we_serve" class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Service Area</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($sale) && count($sale->service_area) > 0)
                                                        @foreach ($sale->service_area as $area)
                                                        <tr>
                                                            <td>{{ $area->country }}, {{ $area->state }}, {{ $area->city }}</td>
                                                            <td><a  type="button" id="{{ $area->id }}"
                                                                class="dropdown-item delete-record area_delete" data-confirm="Are you sure to delete this item?"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a></td>
                                                        </tr>
                                                        @endforeach
                                                        @endif


                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                        <div class="col-12 d-flex justify-content-between">
                                            <a class="btn btn-outline-secondary btn-prev" style="color: #6d788d" disabled>
                                                <i class="mdi mdi-arrow-left me-sm-1"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                            </a>
                                            <div class="last-buttons d-flex" style="gap: 20px;">
                                                {{-- <button class="btn btn-outline-primary waves-effect" type="submit">Save</button> --}}
                                            <a class="btn btn-primary btn-next" style="color: #fff">
                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                <i class="mdi mdi-arrow-right"></i>
                                            </a>
                                            </div>
                                        </div>


                            </div>
                            <!-- Social Links -->
                            <div id="social-links" class="content">
                                @if(isset($Keyword_perm) && $Keyword_perm->view == 1)
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">Services/Sub-services/Keywords</h6>
                                        <small>Add Services/Sub-services/Keywords against Areas</small>
                                    </div>
                                    <form id="keywordadd" action="{{ route('keyword.store') }}" method="POST">
                                        @csrf
                                        <div class="row g-4">
                                            @if(isset($sale))
                                            <input type="hidden" name="sale_id5" id="sale_id5" value="{{ $sale->id }}">
                                            @else
                                            <input type="hidden" name="sale_id5" id="sale_id5" >
                                            @endif
                                            <div class="col-md-12">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="keyword" name="keyword" class="form-control" placeholder="Keyword" />
                                                    <label for="Keyword">Keyword</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="areas_dropdown" name="area_id" class="select2 form-select" data-allow-clear="true">
                                                        <option value="">Please Select</option>
                                                        @if(isset($sale) && count($sale->service_area) > 0)
                                                        @foreach ($sale->service_area as $area)
                                                        <option value="{{$area->id }}">{{ $area->country }}, {{ $area->state }}, {{ $area->city }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <label for="multicol-country">Service Area</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <button type="submit" class="btn btn-primary">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row g-4 py-5">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="keyword_table" class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Keyword</th>
                                                            <th>Service Area</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($sale) && count($sale->keyword) > 0)
                                                            @foreach ($sale->keyword as $keyword)
                                                            <tr>
                                                                <td>{{ $keyword->keyword }}</td>
                                                                {{-- <td>{{ $keyword->area }}</td> --}}
                                                                @if (isset($keyword->area) && $keyword->area != Null)
                                                                   <td>{{ $keyword->area->country }}, {{ $keyword->area->state }}, {{ $keyword->area->city }}</td>
                                                                @else
                                                                <td>N/A</td>
                                                                @endif
                                                                <td> <a  type="button" id="{{ $keyword->id }}"
                                                                    class="dropdown-item delete-record keyword_delete" data-confirm="Are you sure to delete this item?"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a></td>

                                                            </tr>
                                                            @endforeach
                                                        @endif

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                            </div>
                            {{-- </div> --}}
                            <div id="review-submit" class="content">
                                @if(isset($Invoicecharges_perm) && $Invoicecharges_perm->view == 1)
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">Invoice</h6>
                                        <small>Manage Invoices</small>
                                    </div>
                                    <form id="add_service_charge" action="{{ route('invoiceCharges.store') }}" method="POST">
                                        @csrf
                                        @if(isset($sale))
                                        <input type="hidden" name="sale_id6" id="sale_id6" value="{{ $sale->id }}">
                                        @else
                                        <input type="hidden" name="sale_id6" id="sale_id6" value="">
                                        @endif
                                        <div class="row g-4">
                                            <div class="col-sm-6">
                                                <label class="switch switch-lg">
                                                    <input type="checkbox" class="switch-input" name="invoice_status" @if(isset($invoice) &&
                                                        $invoice->invoice_active_status == 1) checked @endif>
                                                    <span class="switch-toggle-slider">
                                                        <span class="switch-on">
                                                            <i class="ri-check-line"></i>
                                                        </span>
                                                        <span class="switch-off">
                                                            <i class="ri-close-line"></i>
                                                        </span>
                                                    </span>
                                                    <span class="switch-label">Invoice Active Status</span>
                                                </label>
                                            </div>
                                            <div class="col-md-6 col-12 mb-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control flatpickr-input active" name="activation_date"
                                                        {{-- @if(isset($invoice) && isset($invoice->activation_date)) value="{{ $invoice->activation_date }}"
                                                    @endif --}}
                                                    placeholder="YYYY-MM-DD" id="flatpickr-date" readonly="readonly">
                                                    <label for="flatpickr-date">Invoice Date</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="company_service_charge" name="company_service_charge" class="select2 form-select"
                                                        data-allow-clear="true">
                                                        <option value="">Please Select</option>
                                                        @if(isset($sale) && count($sale->companyServices) > 0)
                                                        @foreach ($sale->companyServices->unique('id') as $comp_ser)
                                                        <option value="{{$comp_ser->id }}" data-name="{{ $comp_ser->name }}">{{ $comp_ser->name }} </option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <label for="multicol-country">Invoiced Services</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating form-floating-outline">
                                                            <div class="input-group input-group-merge">
                                                                <span class="input-group-text">$</span>
                                                                <div class="form-floating form-floating-outline">
                                                                    <input type="number" id="service_amount" name="amount" class="form-control" placeholder="499"
                                                                        aria-label="Amount (to the nearest dollar)">
                                                                    <label>Amount</label>
                                                                </div>
                                                                <span class="input-group-text">.00</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating form-floating-outline">
                                                            <div class="form-check mt-3">
                                                                <input class="form-check-input" name="is_complementary" type="checkbox" value="1"
                                                                    id="complementery_check">
                                                                <label class="form-check-label" for="complementery_check">
                                                                    Complementary
                                                                </label>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="button" id="add_service" class="btn btn-primary">Add</button>
                                                    </div>
                                                </div>



                                            </div>
                                        </div>

                                        <div class="row py-4">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table id="invoice_service_table" class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Invoiced Service</th>
                                                                <th>Is Complementary</th>
                                                                <th>Service Service ($)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            {{-- @if(isset($invoice) && count($invoice->servicecharges) > 0)
                                                            @foreach ($invoice->servicecharges as $item)
                                                            <tr>
                                                                <td>{{ $item->service_name->name }}
                                                                    <input type="hidden" name="service_id[]" value="{{ $item->service_name->id }}">
                                                                </td>
                                                                <td><input class="form-check-input" name="is_complementary" type="checkbox" value="true"
                                                                        id="defaultCheck1" @if($item->is_complementary === 1) checked @endif readonly>
                                                                        <input type="hidden" name="is_complementary[]" @if($item->is_complementary == 1) value="1" @else value="0" @endif>
                                                                </td>
                                                                @if($item->is_complementary == 0)
                                                                <td>{{ $item->charged_price }}
                                                                    <input type="hidden" name="amount[]" value="{{ $item->amount }}">
                                                                </td>
                                                                @else
                                                                <td>0</td>
                                                                @endif

                                                            </tr>
                                                            @endforeach
                                                            @endif --}}

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row g-4 pt-3">
                                            <div class="col-md-6 col-12 mb-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="month" class="form-control flatpickr-input active" format="YYYY" name="month"
                                                    {{-- @if(isset($invoice) && isset($invoice->month))
                                                        value="{{ $invoice->month }}"
                                                    @endif --}}
                                                    placeholder="YYYY-MM-DD" id="flatpickr-date" readonly="readonly">
                                                    <label for="flatpickr-date">Month</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="discount" name="discount_type" class="select2 form-select" data-allow-clear="true">
                                                        <option value="">Please Select</option>
                                                        {{-- @if(isset($invoice) && isset($invoice->discount_type))
                                                        <option value="{{$invoice->discount_type}}" selected>{{ $invoice->discount_type }}</option>
                                                        @endif --}}
                                                        <option value="New Client Discount">New Client Discount</option>
                                                        <option value="New Year Discount">New Year Discount</option>
                                                        <option value="X-max Discount">X-max Discount</option>
                                                    </select>
                                                    <label for="multicol-country">Discount Type</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text">$</span>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" id="discount_amount" name="discount_amount"
                                                            class="form-control" placeholder="499"
                                                            aria-label="Amount (to the nearest dollar)">
                                                            <label>Discount Amount</label>
                                                        </div>
                                                        <span class="input-group-text">.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mb-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control flatpickr-input active" name="invoice_due_date"
                                                        {{-- @if(isset($invoice) && isset($invoice->invoice_due_date)) value="{{ $invoice->invoice_due_date }}"
                                                    @endif --}}
                                                    placeholder="YYYY-MM-DD" id="flatpickr-date" readonly="readonly">
                                                    <label for="flatpickr-date">Invoice Due Date</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="invoice_freq" name="invoice_freq" class="select2 form-select" data-allow-clear="true">
                                                        <option value="">Please Select</option>
                                                        {{-- @if(isset($invoice) && isset($invoice->invoice_frequency))
                                                        <option value="{{$invoice->invoice_frequency}}" selected>{{ $invoice->invoice_frequency }}</option>
                                                        @endif --}}
                                                        <option value="Monthly">Monthly</option>
                                                        <option value="Bi-annually">Bi-annually</option>
                                                        <option value="Annually">Annually</option>
                                                    </select>
                                                    <label for="multicol-country">Invoice Frequency</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mb-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="invoice_number" name="invoice_no" class="form-control"
                                                     {{-- @php
                                                        $date=Carbon\Carbon::now()->format('M Y');
                                                    // dd($date);
                                                    @endphp

                                                    @if((isset($invoice)) && $invoice->month == $date)
                                                    @if(isset($invoice)) value="{{ $invoice->invoice_number }}" @endif
                                                    @endif --}}
                                                    placeholder="Invoice No." disabled/>
                                                    <label for="Keyword">Invoice No.</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text">$</span>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" id="invoice_amount" name="invoice_amount" class="form-control"
                                                                placeholder="499"
                                                            aria-label="Amount (to the nearest dollar)" readonly>
                                                            <label>Invoice Amount </label>
                                                        </div>
                                                        <span class="input-group-text">.00</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 text-right">
                                                <div class="invoice-button d-flex " style="gap: 0px 20px;">

                                                        {{-- <a id="view-invoice" @if (isset($invoice) && isset($invoice->invoice_number)) style="display: block !important;"  href="{{ route('front.invoiceView', $invoice->invoice_number) }}" @else style="display: none !important;" @endif  target="_blank" class="btn btn-success" style="color: #fff">View Invoice</a> --}}
                                                        <button id="genrate-invoice"   type="submit" class="btn btn-primary">Generate Invoice</button>

                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                    <div class="row py-4">
                                        <h4>Invoices</h4>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="invoice_table_gen" class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr</th>
                                                            <th>Invoice Number</th>
                                                            <th>Invoice Month</th>
                                                            <th>Invoice Date</th>
                                                            <th>Due Date</th>
                                                            <th>Invoice Amount</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($all_invoices) && count($all_invoices) > 0)
                                                        @foreach ($all_invoices as $key=>$item)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $item->invoice_number }}</td>
                                                            <td>{{ $item->month }}</td>
                                                            <td>{{ $item->activation_date }}</td>
                                                            <td>{{ $item->invoice_due_date }}</td>
                                                            <td>{{ $item->total_amount }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @endif

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <!-- /Invoice Form -->
                                @if(isset($Payment_perm) && $Payment_perm->view == 1)
                                <form id="make_payment" action="{{ route('payment.store') }}" method="POST">
                                    @csrf
                                    <div class="row g-4 py-3">
                                        <h4>Payment Detail</h4>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <select id="invoice_number_id" name="invoice_id" class="select2 form-select" data-allow-clear="true">
                                                    <option value="">Please Select</option>
                                                    @if(isset($all_invoices) && count($all_invoices) > 0)
                                                        @foreach ($all_invoices as $item)
                                                            <option value="{{$item->id }}">{{ $item->invoice_number }} (Month: {{ $item->month }})</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <label for="multicol-country">Invoice No.</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <select id="merchant" name="merchant" class="select2 form-select" data-allow-clear="true">
                                                    <option value="">Please Select</option>
                                                    @if(isset($mehchant) && count($mehchant) > 0)
                                                        @foreach ($mehchant as $item)
                                                            <option value="{{$item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <label for="multicol-country">Select Merchant</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <select id="mop" name="mop" class="select2 form-select" data-allow-clear="true">
                                                    <option value="">Please Select</option>
                                                    <option value="Credit Card">Credit Card</option>
                                                    <option value="PayPal">PayPal</option>
                                                    <option value="Zelle">Zelle</option>
                                                    <option value="Cash App">Cash App</option>
                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                    <option value="other">other</option>
                                                </select>
                                                <label for="multicol-country">Mode of Payment</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div  id="embed_mop" class="input-group input-group-merge">

                                                {{-- <span class="input-group-text cursor-pointer p-1" id="paymentCard"><span class="card-type w-px-50"></span></span> --}}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <select id="payment_type" name="payment_type" class="select2 form-select" data-allow-clear="true">
                                                    <option value="">Please Select</option>
                                                    <option value="Full Payment">Full Payment</option>
                                                    <option value="Partials Payment">Partials Payment</option>
                                                    <option value="Advance Payment">Advance Payment</option>
                                                </select>
                                                <label for="multicol-country">Payment Type</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" id="payment_amount" name="payment_amount" class="form-control"  readonly>
                                                <label for="payment_amount">Payment Amount</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" id="Trans_id" name="trans_id" class="form-control"  >
                                                <label for="Trans_id">Int. Trans. Ids</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <div class="mb-4">
                                                    <input class="form-control" name="trans_ss" type="file" id="formFile">
                                                    <label for="formFile" class="form-label">Upload Recipt SS</label>

                                                </div>
                                                {{-- <label for="Trans_id">Recipt Upload</label> --}}
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-right m-auto">
                                            <button type="submit" class="btn btn-primary">Make Payment</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="row py-4">
                                    <h4>Payments</h4>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="payment_table" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Sr</th>
                                                        <th>Invoice Number</th>
                                                        <th>Invoice Month</th>
                                                        <th>Invoice Date</th>
                                                        <th>Due Date</th>
                                                        <th>Payment Status</th>
                                                        <th>Invoice Amount</th>
                                                        <th>Paid Amount</th>
                                                        <th>Balance Amount</th>
                                                        <th>Merchant Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($payments) && count($payments) > 0)
                                                    @foreach ($payments as $key=>$item)

                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $item->invoice->invoice_number }}</td>
                                                        <td>{{ $item->invoice->month }}</td>
                                                        <td>{{ $item->invoice->activation_date }}</td>
                                                        <td>{{ $item->invoice->invoice_due_date }}</td>
                                                        <td>{{ $item->payment_type }}</td>
                                                        <td>{{ $item->invoice->total_amount }}</td>
                                                        <td>{{ $item->amount }}</td>
                                                        <td>{{ $item->balance }}</td>
                                                        <td>{{ $item->merchant->name }}</td>
                                                    </tr>
                                                    @endforeach
                                                    @endif

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="row g-4 pt-4">
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-outline-secondary btn-prev" style="color: #6d788d" disabled>
                                            <i class="mdi mdi-arrow-left me-sm-1"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </a>
                                        <div class="last-buttons d-flex" style="gap: 20px;">

                                                <a  id="preview-sale" class="btn btn-outline-primary waves-effect"  @if(isset($sale)) href="{{ route('sale.detail', $sale->id) }}" @disabled(false) @else href="#" @disabled(true) @endif>Preview</a>

                                        <a class="btn btn-primary btn-next" style="color: #fff">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                            <i class="mdi mdi-arrow-right"></i>
                                        </a>
                                        </div>
                                    </div>
                                    {{-- <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-outline-secondary btn-prev">
                                            <i class="mdi mdi-arrow-left me-sm-1"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-primary btn-submit">Finish</button>
                                    </div> --}}
                                </div>
                            </div>
                            <div id="refund-chargeback" class="content">
                                <div class="content-header mb-3">
                                    <h6 class="mb-0">Refund</h6>
                                    <small>Client Refunds</small>
                                </div>
                                <div class="row g-4">
                                    @if(isset($Saleinfo) && $Saleinfo->view == 1)
                                    <div class="content-header mb-3">

                                    </div>
                                   <form id="refund_form" method="POST" action="{{ route('refund.store') }}">
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
                                        <div id="successMessage" style="display:none;" class="alert alert-success"></div>
                                        <div class="row g-4">
                                            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                            <div class="col-md-6 select2-primary">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="refund_type" name="refund_type" class="select2 form-select">
                                                        <option value="">Select Refund Type</option>
                                                        <option value="Full">Full</option>
                                                        <option value="Partial">Partial</option>
                                                    </select>
                                                    <label for="multicol-closers">Select Refund Type</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 select2-primary">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="select-invoice" name="invoice_id" class="select2 form-select">
                                                        <option value="">Select Invoice</option>
                                                        @if(isset($sale) && isset($sale->invoice))
                                                        @foreach ($sale->invoice as $invoice)
                                                        <option value="{{ $invoice->id }}">{{ $invoice->invoice_number }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <label for="select-invoice">Select Invoice</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text">$</span>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" id="refund_amount" name="refund_amount" class="form-control"
                                                                placeholder="499" aria-label="Amount (to the nearest dollar)">
                                                            <label>Amount</label>
                                                        </div>
                                                        <span class="input-group-text">.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mb-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control flatpickr-input active" name="claim_date"
                                                        placeholder="YYYY-MM-DD" id="flatpickr-date" readonly="readonly">
                                                    <label for="flatpickr-date">Claim Date</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="merchant" name="merchant_id" class="select2 form-select" data-allow-clear="true">
                                                        <option value="">Please Select</option>
                                                        @if(isset($mehchant) && count($mehchant) > 0)
                                                        @foreach ($mehchant as $item)
                                                        <option value="{{$item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <label for="multicol-country">Select Merchant</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="first-name" class="form-control" placeholder="John"
                                                        value="{{ $lead->saler->name }}" disabled />
                                                    <label for="first-name">Sale Rep</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 select2-primary">
                                                <div class="form-floating form-floating-outline" @readonly(true) @disabled(true)>
                                                    <span>Closers</span>
                                                    <div class="d-flex ">
                                                        @if(isset($lead->closers))
                                                        @foreach ($lead->closers as $item)
                                                        <span class="badge rounded-pill bg-primary p-2" style="margin: 5px 20px 0px 0px">{{
                                                            $item->user->name }}</span>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 select2-primary">
                                                <div class="form-floating form-floating-outline" @readonly(true) @disabled(true)>
                                                    <span>Customer Support Representator</span>
                                                    <div class="d-flex ">
                                                        @if(isset($sale) && isset($sale->Customer_support))
                                                        @foreach ($sale->Customer_support as $item)
                                                        <span class="badge rounded-pill bg-primary p-2" style="margin: 5px 20px 0px 0px">{{
                                                            $item->user->name }}</span>
                                                        @endforeach

                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">

                                                <h5 class="card-header pb-3">Refund Reason</h5>
                                                <div class="card-body">
                                                        <textarea class="form-control" style="border-radius: 0px" name="refund_reason" id="full-editor"
                                                            cols="30" rows="5"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-right m-auto py-3">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>

                                        </div>
                                    </form>
                                @endif
                                    <div class="row py-4">
                                        <h4>Refunds</h4>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="refund_table" class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr</th>
                                                            <th>Invoice Number</th>
                                                            <th>Refund Type</th>
                                                            <th>Refund Amount</th>
                                                            <th>Claim Date</th>
                                                            <th>Merchant Account</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($refunds) && count($refunds) > 0)
                                                        @foreach ($refunds as $key=>$item)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $item->invoice->invoice_number }}</td>
                                                            <td>{{ $item->refund_type }}</td>
                                                            <td>{{ $item->refund_amount }}</td>
                                                            <td>{{ $item->claim_date }}</td>
                                                            <td>{{ $item->merchant->name }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">ChargeBack</h6>
                                        <small>Client ChargeBack</small>
                                    </div>
                                    <form id="chargeback_form" method="POST" action="{{ route('chargeback.store') }}">
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
                                        <div id="successMessage" style="display:none;" class="alert alert-success"></div>
                                        <div class="row g-4">
                                            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                            <div class="col-md-6 select2-primary">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="select-invoice-chargeback" name="invoice_id" class="select2 form-select">
                                                        <option value="">Select Invoice</option>
                                                        @if(isset($sale) && isset($sale->invoice))
                                                        @foreach ($sale->invoice as $invoice)
                                                        <option value="{{ $invoice->id }}">{{ $invoice->invoice_number }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <label for="select-invoice">Select Invoice</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mb-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control flatpickr-input active" name="claim_date"
                                                        placeholder="YYYY-MM-DD" id="flatpickr-date" readonly="readonly">
                                                    <label for="flatpickr-date">Claim Date</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="merchant-chargeback" name="merchant_id" class="select2 form-select" data-allow-clear="true">
                                                        <option value="">Please Select</option>
                                                        @if(isset($mehchant) && count($mehchant) > 0)
                                                        @foreach ($mehchant as $item)
                                                        <option value="{{$item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <label for="multicol-country">Select Merchant</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="first-name" class="form-control" placeholder="John"
                                                        value="{{ $lead->saler->name }}" disabled />
                                                    <label for="first-name">Sale Rep</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 select2-primary">
                                                <div class="form-floating form-floating-outline" @readonly(true) @disabled(true)>
                                                    <span>Closers</span>
                                                    <div class="d-flex ">
                                                        @if(isset($lead->closers))
                                                        @foreach ($lead->closers as $item)
                                                        <span class="badge rounded-pill bg-primary p-2" style="margin: 5px 20px 0px 0px">{{
                                                            $item->user->name }}</span>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 select2-primary">
                                                <div class="form-floating form-floating-outline" @readonly(true) @disabled(true)>
                                                    <span>Customer Support Representator</span>
                                                    <div class="d-flex ">
                                                        @if(isset($sale) && isset($sale->Customer_support))
                                                        @foreach ($sale->Customer_support as $item)
                                                        <span class="badge rounded-pill bg-primary p-2" style="margin: 5px 20px 0px 0px">{{
                                                            $item->user->name }}</span>
                                                        @endforeach

                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">

                                                <h5 class="card-header pb-3">ChargeBack Reason</h5>
                                                <div class="card-body">
                                                        <textarea class="form-control" style="border-radius: 0px" name="chargeBack_reason" id="full-editor"
                                                            cols="30" rows="5"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-right m-auto py-3">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>

                                        </div>
                                    </form>
                                    <div class="row py-4">
                                        <h4>ChargeBack</h4>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="chargeBack_table" class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr</th>
                                                            <th>Invoice Number</th>
                                                            <th>Claim Date</th>
                                                            <th>Merchant Account</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($chargeBack) && count($chargeBack) > 0)
                                                        @foreach ($chargeBack as $key=>$item)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $item->invoice->invoice_number }}</td>
                                                            <td>{{ $item->claim_date }}</td>
                                                            <td>{{ $item->merchant->name }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-outline-secondary btn-prev" style="color: #6d788d" disabled>
                                            <i class="mdi mdi-arrow-left me-sm-1"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </a>
                                        <div class="last-buttons d-flex" style="gap: 20px;">
                                            {{-- <button class="btn btn-outline-primary waves-effect" type="submit">Save</button> --}}
                                            <a class="btn btn-primary btn-next" style="color: #fff">
                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                <i class="mdi mdi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="reports" class="content">
                                <div class="content-header mb-3">
                                    <h6 class="mb-0">Reports</h6>
                                    <small>Reports</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h5 class="card-header pb-3">Reports</h5>
                                        <div class="card-body">
                                            <textarea class="form-control" style="border-radius: 0px" name="chargeBack_reason" id="full-editor"
                                                cols="30" rows="5"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-outline-secondary btn-prev" style="color: #6d788d" disabled>
                                            <i class="mdi mdi-arrow-left me-sm-1"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </a>
                                        <div class="last-buttons d-flex" style="gap: 20px;">
                                            {{-- <button class="btn btn-outline-primary waves-effect" type="submit">Save</button> --}}
                                            <a class="btn btn-primary btn-next" style="color: #fff">
                                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                <i class="mdi mdi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <!-- /Default Icons Wizard -->

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
    <script src="{{ asset('assets/js/repeater.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/front-page-payment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/forms-editors.js') }}"></script>
    {{-- <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script> --}}
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets/js/forms-pickers.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/24.6.0/build/js/intlTelInput.min.js"
        integrity="sha512-/sRFlFRbcvObOo/SxW8pvmFZeMLvAF6hajRXeX15ekPgT4guXnfNSjLC98K/Tg2ObUgKX8vn9+Th5/mGHzZbEw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(':checkbox[readonly]').click(function(){
            return false;
        });
    </script>
    <script>
        const threeDaysAgo = new Date();
            threeDaysAgo.setDate(threeDaysAgo.getDate() - 3);

            // Initialize Flatpickr
            flatpickr("#flatpickr-date", {
                minDate: threeDaysAgo // Set minimum date to 3 days ago
            });
    </script>
    <script>
        const input = document.querySelector("#business_number");
        const iti =  intlTelInput(input, {
      initialCountry: "us",
      strictMode: true,
      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/24.6.0/build/js/utils.min.js"
    });
    </script>

    <script>
        flatpickr("#month", {
            plugins: [
            new monthSelectPlugin({
            shorthand: true, //defaults to false
            dateFormat: "M Y", //defaults to "F Y"
            altFormat: "F Y", //defaults to "F Y"
            theme: "dark" // defaults to "light"
            })
        ]

        });
    </script>



    <script>
        $(function(){
            $("#repeater").createRepeater();
        });
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
    <script>
        $(document).ready(function() {
            // Loop through each day of the week
            var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            days.forEach(function(day) {
                var openTimeInput = $('#' + day + '_open');
                var closedTimeInput = $('#' + day + '_closed');

                // Check on page load if _open already has a value
                if (openTimeInput.val()) {
                    // Enable the closed time input if _open has a value
                    closedTimeInput.prop('disabled', false);
                    // Set the minimum value of _closed to the current value of _open
                    closedTimeInput.attr('min', openTimeInput.val());
                } else {
                    // Disable the closed time input by default if no value in _open
                    closedTimeInput.prop('disabled', true);
                }

                // On change event for the open time input
                openTimeInput.change(function() {
                    var openTime = $(this).val(); // Get the selected open time

                    if (openTime) {
                        // Enable the closed time input and set its minimum value
                        closedTimeInput.prop('disabled', false);
                        closedTimeInput.attr('min', openTime);
                    } else {
                        // Disable the closed time input if open time is cleared
                        closedTimeInput.prop('disabled', true).val(''); // Clear closed time when disabling
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
    // Function to copy times to all days (Monday-Sunday)
    $('#copyToAll').click(function() {
        var openTime = $('#monday_open').val();
        var closedTime = $('#monday_closed').val();

        // Loop through all days and set the same times
        $('input[name^="open"]').each(function() {
            $(this).val(openTime);
        });
        $('input[name^="closed"]').each(function() {
            $(this).val(closedTime);
        });
    });

    // Function to copy times to working days (Monday - Friday)
    $('#copyToWorkingDays').click(function() {
        var openTime = $('#monday_open').val();
        var closedTime = $('#monday_closed').val();

        // Set the working days (Monday - Friday) times
        $('#monday_open').val(openTime);
        $('#monday_closed').val(closedTime);
        $('#tuesday_open').val(openTime);
        $('#tuesday_closed').val(closedTime);
        $('#wednesday_open').val(openTime);
        $('#wednesday_closed').val(closedTime);
        $('#thursday_open').val(openTime);
        $('#thursday_closed').val(closedTime);
        $('#friday_open').val(openTime);
        $('#friday_closed').val(closedTime);
    });

    // Handle enabling/disabling of open/closed times based on radio buttons
    $('input[data-day="check"]').change(function(e) {
        e.preventDefault();
        var selected = $(this).val(); // Get the value of the checked radio button
        var day = $(this).data('day-name'); // Get the day from the data attribute

        var openTimeInput = $('#' + day + '_open');
        var closedTimeInput = $('#' + day + '_closed');

        if (selected == "closed" || selected == "24/7") {
            openTimeInput.prop('disabled', true);
            closedTimeInput.prop('disabled', true);
        } else {
            openTimeInput.prop('disabled', false);
            closedTimeInput.prop('disabled', false);
        }
    });
});


    </script>

    <script>
        function validateForm() {
            const businessName = document.getElementById('business_name').value;
            const businessNumber = document.getElementById('business_number').value;
            const email = document.getElementById('basic-default-email').value;
            const websiteUrl = document.getElementsByName('website_url')[0].value;
            const clientName = document.getElementsByName('client_name')[0].value;
            // const socialLinkElement = document.querySelector(".social_link")[0];
            // console.log(socialLinkElement);

            // if (!socialLinkElement) {
            //     alert('Social link element not found!');
            //     return false;
            // }
            // const socialLink = socialLinkElement.value;


            const urlPattern = /^(https?:\/\/)?([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?$/;

            // for (let i = 0; i < socialLinks.length; i++) {
            //     const socialLink = socialLinks[i].value; // Get the value of the current input

            //     // Check if the value matches the URL pattern
            //     if (socialLink && !urlPattern.test(socialLink)) {
            //         alert(`Invalid Social URL in field ${i + 1}. Please enter a valid URL (e.g., http://example.com).`);
            //         return false; // Stop if URL is invalid
            //     }
            // }

            // Validate business name (only letters and spaces)
            if (!/^[a-zA-Z\s]*$/.test(businessName)) {
                alert('Invalid Business Name. Only letters and spaces are allowed.');
                return false;
            }

            // Validate business number (only digits)
            if (!iti.isValidNumber()) {
                alert('Invalid Phone Number. Please enter a valid phone number.');
                return false;
            }
            // if (!/^\d*$/.test(businessNumber)) {
            //     alert('Invalid Business Number. Only digits are allowed.');
            //     return false;
            // }

            // const urlPattern = /^(https?:\/\/)?([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?$/;
            if (!urlPattern.test(websiteUrl)) {
                alert('Invalid Website URL. Please enter a valid URL (e.g., http://example.com).');
                return false;
            }

            // if (!urlPattern.test(socialLink)) {
            //     alert('Invalid Social  URL. Please enter a valid URL (e.g., http://example.com).');
            //     return false;
            // }

            // Validate email format
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert('Invalid Email. Please enter a valid email address.');
                return false;
            }

            // Validate client name (only letters and spaces)
            if (!/^[a-zA-Z\s]*$/.test(clientName)) {
                alert('Invalid Client Name. Only letters and spaces are allowed.');
                return false;
            }

            // Validate client address (allowing letters, digits, and certain symbols)
            // if (!/^[a-zA-Z0-9\/\-\_\:\, ]*$/.test(clientAddress)) {
            //     alert('Invalid Client Address. Only letters, numbers, and certain symbols are allowed.');
            //     return false;
            // }

            return true; // All validations passed
        }
    </script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.all.min.js"></script> --}}
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#service_table').DataTable({
                pageLength: 5
            });

            // Listen for the draw event
            table.on('draw', function() {
                // Your custom script here
                var link = document.createElement('link');
                        link.rel = 'stylesheet';
                        link.href = "{{ asset('assets/vendor/libs/select2/select2.css') }}"; // Adjust this if necessary
                        document.head.appendChild(link);

                        // Load the Select2 script
                        var script = document.createElement('script');
                        script.src = "{{ asset('assets/vendor/libs/select2/select2.js') }}"; // Adjust this if necessary
                        script.onload = function() {
                            // Initialize Select2 after the script loads
                            $('.select2').select2(); // Reinitialize Select2 for the new select elements
                        };
                        document.head.appendChild(script);
                console.log('Table redrawn, new page loaded.');
                // You can also access the current page or other data if needed
            });
        });
    </script>


    <script>
        $(document).ready(function () {
        $('#saleForm').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission
            let isValid = validateForm(); // Call custom form validation function

            if(isValid) {

            // Store current form values before submission, particularly time and select elements
            let timeInputs = {};
            let selectInputs = {};

            // Save values of input[type="time"] and select elements before form submission
            $('#saleForm').find('input[type="time"], select').each(function () {
                timeInputs[$(this).attr('name')] = $(this).val();
                selectInputs[$(this).attr('name')] = $(this).val();
            });

            // Create a FormData object to handle form data
            let formData = new FormData(this);

            // Clear previous error messages
            $('.alert-danger').remove();

            $.ajax({
                url: $(this).attr('action'), // Form action URL
                type: $(this).attr('method'), // POST method
                data: formData,
                processData: false, // Important: do not process the data
                contentType: false, // Important: content type is false
                success: function (response) {

                    // console.log(response.sale);
                    $('#sale_id').val(response.sale.id);
                    $('#sale_id2').val(response.sale.id);
                    $('#sale_id3').val(response.sale.id);
                    $('#sale_id4').val(response.sale.id);
                    $('#sale_id5').val(response.sale.id);
                    $('#sale_id6').val(response.sale.id);
                    $('#first_next').prop('disabled', false);
                    $('#preview-sale').prop('disabled', false);
                    // Handle success response (display success message using SweetAlert2)
                    Swal.fire({
                       position: 'top-end', // Position the toast at the top-right corner
                        icon: 'success', // Change this to 'error', 'warning', etc., based on your requirement
                        title: 'Sale Saved Successfully', //
                        showConfirmButton: false, // Remove the confirm button
                        timer: 1500, // Duration in milliseconds before the toast disappears
                        toast: true, // Enable the toast feature
                        // didOpen: () => {
                        // Swal.showLoading(); // Optionally show a loading indicator if needed
                        // }
                    });



                    // Optionally reset the form only on success
                    // $('#saleForm')[0].reset();
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

                    // Restore time input and select values after error
                    $('#saleForm').find('input[type="time"], select').each(function () {
                        if ($(this).attr('name') in timeInputs) {
                            $(this).val(timeInputs[$(this).attr('name')]);
                        }
                        if ($(this).attr('name') in selectInputs) {
                            $(this).val(selectInputs[$(this).attr('name')]);
                        }
                    });
                }
            });
        }
        });
    });
    </script>

    <script>
        $(document).ready(function () {
        $('#detail_form').on('submit', function (e) {
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


                    // Handle success response (display success message using SweetAlert2)
                    Swal.fire({
                       position: 'top-end', // Position the toast at the top-right corner
                        icon: 'success', // Change this to 'error', 'warning', etc., based on your requirement
                        title: 'Sale Info Saved Successfully', //
                        showConfirmButton: false, // Remove the confirm button
                        timer: 1500, // Duration in milliseconds before the toast disappears
                        toast: true, // Enable the toast feature

                    });



                    // Optionally reset the form only on success
                    // $('#saleForm')[0].reset();
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


    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function () {
            $("#service_name").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('clientServices.search_services') }}",
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                query: request.term
                            },
                            success: function(data) {
                                response($.map(data, function(item) {
                                    return {
                                        label: item.name,
                                        value: item.name
                                    };
                                }));
                            }
                        });
                    },
                    minLength: 2
                });

            $('#serviceform').on('submit', function (e) {
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
                        console.log(response);

                        $('#service_table').empty(); // Use empty() to clear the table
                        var client_service = response.client_service;
                        var company_services = response.company_services;
                        // var client_company_services = response.client_company_services;
                        console.log(client_service);


                        // Create options for the select element
                        // if(!!client_service.companyServices && client_service.companyServices.lenght > 0){
                        // var options = $.map(company_services.companyServices, function (item) {
                        //     return '<option value="' + item.id + '">' + item.name + '</option>';
                        // }).join('');
                        // } else {
                        //     var options = $.map(company_services, function (item) {
                        //     return '<option value="' + item.id + '">' + item.name + '</option>';
                        // }).join('');
                        // }



                        // Create table rows for each client service
                        var content = $.map(client_service, function (service, index) {
                        console.log(service);

                        var options = '';

                        // Check if `companyServicesForSale` exists and has services
                        if (service.company_services_for_sale && service.company_services_for_sale.length > 0) {
                            options = $.map(service.company_services_for_sale, function (item) {
                                return '<option value="' + item.id + '" selected>' + item.name + '</option>';
                            }).join('');
                        } else {
                            options = $.map(company_services, function (item) {
                                return '<option value="' + item.id + '">' + item.name + '</option>';
                            }).join('');
                        }

                        return '<tr>' +
                            '<td>' + service.name +
                                '<input type="hidden" name="client_service[' + index + ']" value="' + service.id + '" />' +
                            '</td>' +

                            '<td>' +
                                '<div class="col-md-12 select2-primary" data-select2-id="' + service.id + '">' +
                                    '<div class="form-floating form-floating-outline form-floating-select2" data-select2-id="' + service.id + '">' +
                                        '<div class="position-relative">' +
                                            '<select name="company_service[' + index + '][]" class="select2 form-select" multiple>' +
                                                options +
                                            '</select>' +
                                            '<label for="multicol-closers">FTS Services</label>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</td>' +
                            '<td>'+
                                '<a class="service_delete" data-id="'+ service.id +'"  style="font-size:20px;" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete"><i class="ri-delete-bin-5-line ri-50px"></i></a>' +
                            '</td>'+
                        '</tr>';
                    }).join(''); // Join the content into a single string

                    // Assuming you want to append this to a specific table or container
                    $('#service_table tbody').html(content);


                    // Assuming you want to append this to a specific table or container
                    // $('#service_table tbody').html(content);

                        // Append the new content to the service table
                        $('#service_table').append(content);

                        // table.page.len(5).draw();


                            // Load the Select2 stylesheet
                        var link = document.createElement('link');
                        link.rel = 'stylesheet';
                        link.href = "{{ asset('assets/vendor/libs/select2/select2.css') }}"; // Adjust this if necessary
                        document.head.appendChild(link);

                        // Load the Select2 script
                        var script = document.createElement('script');
                        script.src = "{{ asset('assets/vendor/libs/select2/select2.js') }}"; // Adjust this if necessary
                        script.onload = function() {
                            // Initialize Select2 after the script loads
                            $('.select2').select2(); // Reinitialize Select2 for the new select elements
                        };
                        document.head.appendChild(script);

                        // Handle success response (display success message using SweetAlert2)
                        Swal.fire({
                       position: 'top-end', // Position the toast at the top-right corner
                        icon: 'success', // Change this to 'error', 'warning', etc., based on your requirement
                        title: 'Service Added Successfully', //
                        showConfirmButton: false, // Remove the confirm button
                        timer: 1500, // Duration in milliseconds before the toast disappears
                        toast: true, // Enable the toast feature

                    });

                        // Optionally reset the form only on success
                        $('#serviceform')[0].reset();
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
    <script>
        $(document).ready(function () {
            $(document).on('click', '.service_delete', function() {
                var id = $(this).attr('data-id');
                var result = confirm("Want to delete?");
                // alert(id);
                // Get the service ID from the form data
                if(result){
                $.ajax({
                    type: "GET",
                    url: "{{ route('clientServices.delete') }}",
                    data: {id: id},
                    success: function (response) {

                        Swal.fire({
                       position: 'top-end', // Position the toast at the top-right corner
                        icon: 'error', // Change this to 'error', 'warning', etc., based on your requirement
                        title: 'Service Deleted Successfully', //
                        showConfirmButton: false, // Remove the confirm button
                        timer: 1500, // Duration in milliseconds before the toast disappears
                        toast: true, // Enable the toast feature

                    });
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
                                toast: true, // Auto-close after 4 seconds (optional)
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
            }

            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#sync_services').on('submit', function (e) {
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

                        var compService = response.sale.company_services;
                        $('#company_service_charge').empty();
                        if (compService && compService.length > 0) {
                            var uniqueServices = compService.filter((value, index, self) =>
                                index === self.findIndex((t) => (
                                    t.id === value.id // Check uniqueness by `id`
                                ))
                            );

                            // Generate options for the unique services
                            var options = uniqueServices.map(service => {
                                return '<option value="' + service.id + '" data-name="'+ service.name +'">' + service.name + '</option>';
                            }).join(''); // `.join('')` will concatenate the options into a single string

                        } else {
                            var options = '<option>No services available</option>'; // Default option if no services are found
                        }
                        $('#company_service_charge').append(options);

                        // Handle success response (display success message using SweetAlert2)
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false, // Remove the confirm button
                            timer: 1500, // Duration in milliseconds before the toast disappears
                            toast: true,
                        });



                        // Optionally reset the form only on success
                        // $('#saleForm')[0].reset();
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
                    $.get(`https://myfts.firmtech.biz/front/states/${countryId}`, function(data) {
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
                    $.get(`https://myfts.firmtech.biz/front/cities/${stateId}/${conrtyId}`, function(data) {
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
            $('#service_area').on('submit', function (e) {
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

                        var list = '<tr>\
                                    <td>'+response.servicearea.country+', '+ response.servicearea.state +', '+response.servicearea.city+'</td>\
                                    <td><a  type="button" id="'+response.servicearea.id +'" class="dropdown-item delete-record area_delete" data-confirm="Are you sure to delete this item?"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a></td>\
                                    </tr>'
                        $('#areas_we_serve').append(list);
                        var option = '<option value="'+response.servicearea.id+'">'+response.servicearea.country+', '+ response.servicearea.state +', '+response.servicearea.city+'</option>'
                        $('#areas_dropdown').append(option);
                        $('#countries').val(response.servicearea.country)
                        $('#states').val(response.servicearea.state)
                        $('#cities').val(response.servicearea.city)





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
                        // $('#service_area')[0].reset();
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
                            $('#service_area').prepend(errorHtml); // Add errors to the form

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

<script>
    $(document).ready(function () {
        // Use event delegation to attach the click event to the table or a parent element
        $('#areas_we_serve').on('click', '.area_delete', function() {
            var id = $(this).attr('id'); // Get the ID of the keyword to be deleted
            var row = $(this).closest('tr'); // Get the parent row of the clicked delete button

            $.ajax({
                url: "{{ route('serviceArea.delete')}}",  // Ensure this route is correct
                type: "GET",
                data: {id: id},
                success: function (response) {
                    // Show success message using SweetAlert
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Success Service Area Deleted',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                        toast: true,
                    });

                    // Remove the deleted row from the table
                    row.remove(); // Remove the <tr> containing the deleted keyword
                },
                error: function (xhr) {
                    // Handle validation errors or other issues
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        let errorHtml = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function (key, value) {
                            errorHtml += '<li>' + value + '</li>';
                        });
                        errorHtml += '</ul></div>';
                        $('#service_area').prepend(errorHtml);

                        // Display SweetAlert2 for validation errors
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorHtml, // Use the generated error HTML
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                        });
                    } else if (xhr.responseJSON.error) {
                        // Custom error message for server-side issues
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

    <script>
        $(document).ready(function () {
            $('#keywordadd').on('submit', function (e) {
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



                        var row = '<tr>';

                        row += '<td>' + response.keyword.keyword + '</td>';

                        // Check if response.area is not null before showing the area fields
                        if (response.area !== null) {
                            row += '<td>' + response.area.country + ', ' + response.area.state + ', ' + response.area.city + '</td>';
                        } else {
                            row += '<td>N/A</td>'; // Default text if area is null
                        }

                        row += '<td><a type="button" id="' + response.keyword.id + '" class="dropdown-item delete-record keyword_delete" data-confirm="Are you sure to delete this item?"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a></td>';
                        row += '</tr>';

                        $('#keyword_table').append(row);
                        $('#keyword').val('');
                        $('areas_dropdown').val(response.area ? response.area.id : '');  // Ensure the dropdown is set or left blank if area is null





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

    <script>
        $(document).ready(function () {
            // Use event delegation to attach the click event to the table or a parent element
            $('#keyword_table').on('click', '.keyword_delete', function() {
                var id = $(this).attr('id'); // Get the ID of the keyword to be deleted
                var row = $(this).closest('tr'); // Get the parent row of the clicked delete button

                $.ajax({
                    url: "{{ route('keyword.delete')}}",  // Ensure this route is correct
                    type: "GET",
                    data: {id: id},
                    success: function (response) {
                        // Show success message using SweetAlert
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Success Keyword Deleted',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                        });

                        // Remove the deleted row from the table
                        row.remove(); // Remove the <tr> containing the deleted keyword
                    },
                    error: function (xhr) {
                        // Handle validation errors or other issues
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            let errorHtml = '<div class="alert alert-danger"><ul>';
                            $.each(errors, function (key, value) {
                                errorHtml += '<li>' + value + '</li>';
                            });
                            errorHtml += '</ul></div>';
                            $('#service_area').prepend(errorHtml);

                            // Display SweetAlert2 for validation errors
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: 'Validation Error',
                                html: errorHtml, // Use the generated error HTML
                                showConfirmButton: false,
                                timer: 1500,
                                toast: true,
                            });
                        } else if (xhr.responseJSON.error) {
                            // Custom error message for server-side issues
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

    <script>
        $(document).ready(function () {
            $('#add_service').click(function () {
                var selectedOption = $('#company_service_charge option:selected');
                var service = selectedOption.val(); // Get the selected service ID
                var service_name = selectedOption.data('name'); // Get the service name
                var amount = $('#service_amount').val(); // Get the service amount
                var check = $('#complementery_check').prop('checked'); // Check if the service is complementary
                var totalAmount = parseFloat($('#invoice_amount').val()) || 0; // Start with the current total
                var serviceExists = false;

                // Check if the service is already added to the table
                $('#invoice_service_table tr').each(function() {
                    var existingServiceId = $(this).find('input[name="service_id[]"]').val();
                    if (existingServiceId == service) {
                        serviceExists = true;
                        return false; // Stop iterating once the service is found
                    }
                });

                // If the service already exists, alert and exit
                if (serviceExists) {
                    alert('This service has already been added to the invoice.');
                    return; // Exit the function without adding the service again
                }

                // Check if all fields are filled in
                if (service !== '' && service_name !== '' && amount !== '') {
                    // Prepare the new row HTML
                    var invoice_table = '<tr>' +
                        '<td>' + service_name + ' ' +
                            '<input type="hidden" name="service_id[]" value="' + service + '">' +
                        '</td>' +
                        '<td><input class="form-check-input" type="checkbox" ' + (check ? 'value="1"' : 'value="0"') + ' id="defaultCheck" ' + (check ? 'checked' : '') + ' readonly></td>' +
                        '<input type="hidden" name="is_complementary[]" value="' + (check ? '1' : '0') + '" >' +
                        '<td>' + amount + ' ' +
                            '<input type="hidden" name="amount[]" value="' + amount + '">' +
                        '</td>' +
                    '</tr>';

                    // Append the new row to the table
                    $('#invoice_service_table').append(invoice_table);
                    $('#company_service_charge').prop('selectedIndex', -1);
                    $('#service_amount').val('');
                    $('#complementery_check').prop('checked', false);

                    // If the service is NOT complementary, add the amount to the total
                    if (!check) {
                        totalAmount += parseFloat(amount); // Add the amount to the total if it's not complementary
                    }

                    // Update the total amount in the invoice input field
                    $('#invoice_amount').val(totalAmount.toFixed(2));
                } else {
                    alert('Please fill in all the required fields.');
                }
            });

            // Update the total amount when discount is applied
            $('#discount_amount').change(function() {
                var discountAmount = parseFloat($(this).val());
                var invoiceAmount = parseFloat($('#invoice_amount').val());
                var totalAmount = invoiceAmount - discountAmount;
                $('#invoice_amount').val(totalAmount.toFixed(2));
            });
        });


    </script>

    <script>
        $(document).ready(function () {
            $('#add_service_charge').on('submit', function (e) {
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
                        console.log(response);
                        var all_invoices = response.all_invoices;
                        var invoice = response.invoice;
                        // $('#genrate-invoice').attr('style', "display: none !important");
                        // $('#view-invoice').attr('style', "display: block !important; color: #fff !important");
                        $('#view-invoice').attr('href', "/front/invoice/"+ invoice.invoice_number +  "");
                        $('#invoice_number').val(invoice.invoice_number);

                        if(all_invoices){

                            var option = $.map(all_invoices, function (invoice, ) {
                                return '<option value="' + invoice.id + '">' + invoice.invoice_number +' </option>';
                            });

                            $('#invoice_number_id').append(option)
                        }



                    var invoice = response.all_invoices;
                    // console.log(invoice);

                    var table_content = ''
                    invoice.forEach(function(invoice, index) {
                        table_content += '<tr>\
                                <td>' + (index + 1) + '</td>\
                                <td>' + invoice.invoice_number + '</td>\
                                <td>' + invoice.month + '</td>\
                                <td>' + invoice.activation_date + '</td>\
                                <td>' + invoice.invoice_due_date + '</td>\
                                <td>' + invoice.total_amount + '</td>\
                            </tr>';
                        });

                        // Clear the table first and then append the new rows
                        $('#invoice_table_gen tbody').empty().append(table_content);




                    // var invoice = response.invoice;
                    // $('#invoice_id').val(invoice.id);

                    // $('#invoice_service_table tr').remove();

                    // var invoice_table = $.map(invoice.servicecharges, function (item) {
                    //     var serviceName = item.service_name ? item.service_name.name : 'N/A'; // Handle potential null
                    //     return '<tr>\
                    //                 <td>' + serviceName + '</td>\
                    //                 <td><input class="form-check-input" type="checkbox" value="1" id="defaultCheck' + item.id + '" ' + (item.is_complementary === 1 ? 'checked' : '') + ' readonly></td>\
                    //                 <td>' + item.charged_price + '</td>\
                    //             </tr>';
                    // });

                    // // Append the generated rows to the table
                    // $('#invoice_service_table').append(invoice_table);


                    // // Calculate the total discount amount
                    // var totalDiscount = invoice.servicecharges.reduce(function (sum, item) {
                    //     return sum + parseFloat(item.discount_price); // Summing up discount_price values
                    // }, 0);

                    // // Calculate the total charged amount
                    // var totalCharged = invoice.servicecharges.reduce(function (sum, item) {
                    //     return sum + parseFloat(item.charged_price); // Summing up charged_price values
                    // }, 0);

                    // // Update the discount_amount and charged amount in the invoice object
                    // invoice.discount_amount = totalDiscount;
                    // invoice.total_amount = totalCharged;

                    // // Optionally, update the UI if necessary
                    // $('#discount_amount').val(totalDiscount.toFixed(2));
                    // $('#invoice_amount').val(totalCharged.toFixed(2));
                    // $('#invoice_number').val(invoice.invoice_number);






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
                         $('#add_service_charge')[0].reset();
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
    <script>
        $(document).ready(function () {
            $('#invoice_number_id').change(function () {
                let id = $(this).val();
                // alert(id);
                $.ajax({
                    type: "GET",
                    url: "{{ route('front.invoicePrice') }}",
                    data: {id: id},
                    success: function (response) {
                        console.log(response.payment);
                        var invoice = response.invoice;
                        // console.log(invoice.total_amount);
                        var payment = response.payment;
                        if(payment){
                            $('#payment_amount').val(payment.balance);
                        }
                        else{
                            $('#payment_amount').val(invoice.total_amount);
                        }

                    }
                });
            })
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#mop').change(function () {
                var mop = this.value;
                // alert(mop);

                if (mop == "Credit Card") {
                    var input = ' <div class="form-floating form-floating-outline">\
                                    <input\
                                        type="text"\
                                        id="billings-card-num"\
                                        class="form-control billing-card-mask"\
                                        placeholder="4541 2541 2547 2577"\
                                        name="card_number"\
                                        aria-describedby="paymentCard"  />\
                                    <label for="billings-card-num">Card number</label>\
                                  </div>';

                    $('#embed_mop').html(input);
                    var script = document.createElement('script');
                        script.src = "{{ asset('assets/js/front-page-payment.js') }}"; // Adjust this if necessary
                        script.onload = function() {
                            // Initialize Select2 after the script loads
                            // $('.billing-card-mask').formCheckInputPayment(); // Reinitialize Select2 for the new select elements
                        };
                        document.head.appendChild(script);
                    // $('#billings-card-num').prop('disabled', false);  // If you want to enable it, uncomment this line
                }
                else if (mop == "PayPal") {
                    // If PayPal is selected, make sure that #billings-card-num is disabled
                    var input = ' <div class="form-floating form-floating-outline">\
                                    <input\
                                        type="email"\
                                        class="form-control \"\
                                        name="paypal_email"\
                                          />\
                                    <label for="paypal">Paypal</label>\
                                  </div>';

                    $('#embed_mop').html(input);
                }
                else if (mop == "Zelle") {
                    // Add any logic you need for Zelle here
                    $.ajax({
                        type: "GET",
                        url: "{{ route('front.getzelle') }}",
                        success: function (response) {
                            // Validate response structure
                            if (response && response.zelle && Array.isArray(response.zelle)) {
                                console.log(response);

                                // Map the response data to options
                                var options = $.map(response.zelle, function (zelle) {
                                    return '<option value="' + zelle.id + '">' + zelle.name + '</option>';
                                });

                                // Build the select input with the dynamic options
                                var input = '<div class="form-floating form-floating-outline">' +
                                                '<select id="zelle" name="zelle_id" class="select2 form-select" data-allow-clear="true">' +
                                                    '<option value="">Please Select</option>' + options + '</select>' +
                                                '<label for="mop">Zelle Accounts</label>' +
                                            '</div>';

                                // Clear previous content and inject new content
                                $('#embed_mop').html(input);

                                // Initialize or reinitialize Select2 (if needed)
                                $('#zelle').select2();
                            } else {
                                console.error('Invalid response structure:', response);
                                // Handle invalid response structure
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX request failed:', status, error);
                            // Handle the error appropriately
                        }
                    });

                }
                else if (mop == "Cash App"){
                    $.ajax({
                        type: "GET",
                        url: "{{ route('front.getcash') }}",
                        success: function (response) {
                            // Validate response structure
                            if (response && response.cash && Array.isArray(response.cash)) {
                                console.log(response);

                                // Map the response data to options
                                var options = $.map(response.cash, function (cash) {
                                    return '<option value="' + cash.id + '">' + cash.name + '</option>';
                                });

                                // Build the select input with the dynamic options
                                var input = '<div class="form-floating form-floating-outline">' +
                                                '<select id="cash" name="cashapp_id" class="select2 form-select" data-allow-clear="true">' +
                                                    '<option value="">Please Select</option>' + options + '</select>' +
                                                '<label for="mop">Cash Apps</label>' +
                                            '</div>';

                                // Clear previous content and inject new content
                                $('#embed_mop').html(input);

                                // Initialize or reinitialize Select2 (if needed)
                                $('#cash').select2();
                            } else {
                                console.error('Invalid response structure:', response);
                                // Handle invalid response structure
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX request failed:', status, error);
                            // Handle the error appropriately
                        }
                    });
                }
                else if (mop == "Bank Transfer"){
                    $.ajax({
                        type: "GET",
                        url: "{{ route('front.getbank') }}",
                        success: function (response) {
                            // Validate response structure
                            if (response && response.bank && Array.isArray(response.bank)) {
                                console.log(response);

                                // Map the response data to options
                                var options = $.map(response.bank, function (bank) {
                                    return '<option value="' + bank.id + '">' + bank.name + '</option>';
                                });

                                // Build the select input with the dynamic options
                                var input = '<div class="form-floating form-floating-outline">' +
                                                '<select id="bank" name="bank_transfer_id" class="select2 form-select" data-allow-clear="true">' +
                                                    '<option value="">Please Select</option>' + options + '</select>' +
                                                '<label for="mop">Bank Accounts</label>' +
                                            '</div>';

                                // Clear previous content and inject new content
                                $('#embed_mop').html(input);

                                // Initialize or reinitialize Select2 (if needed)
                                $('#bank').select2();
                            } else {
                                console.error('Invalid response structure:', response);
                                // Handle invalid response structure
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX request failed:', status, error);
                            // Handle the error appropriately
                        }
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#payment_type').change(function () {
                var type = this.value;
                if(type == "Full Payment"){
                    $('#payment_amount').prop('readonly', true)
                }
                else{
                    $('#payment_amount').prop('readonly', false)
                }
            })
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#make_payment').on('submit', function (e) {
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
                        var payment = response.current_payment;
                    //    console.log(payment);

                        var payments = response.payments;
                        console.log(payments);
                        var tableContent = ''; // Initialize a variable to store the rows

                        payments.forEach(function(payment, index) {
                            tableContent += '<tr>\
                                <td>' + (index + 1) + '</td>\
                                <td>' + payment.invoice_number + '</td>\
                                <td>' + payment.invoice.month + '</td>\
                                <td>' + payment.invoice.activation_date + '</td>\
                                <td>' + payment.invoice.invoice_due_date + '</td>\
                                <td>' + payment.payment_type + '</td>\
                                <td>' + payment.invoice.total_amount + '</td>\
                                <td>' + payment.amount + '</td>\
                                <td>' + payment.balance  + '</td>\
                                <td>' + payment.merchant.name + '</td>\
                            </tr>';
                        });

                        // Clear the table first and then append the new rows
                        $('#payment_table tbody').empty().append(tableContent);
                        $('#merchant').val(payment.merchant_id);
                        $('invoice_number_id').val(payment.id);


                        // alert("dskfjsdkljf");




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
                        $('#make_payment')[0].reset();
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

                        $('#addRoleModal').modal('hide');




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

{{-- Refund --}}

<script>
    $(document).ready(function () {
        $('#select-invoice').change(function (e) {
            e.preventDefault();
            let invoice_id = $(this).val();
            let refund_type = $('#refund_type').val();
            console.log(refund_type);

            if(refund_type == "")
            {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error',
                    text: 'Refund type is required.',
                    showConfirmButton: false, // Remove the confirm button
                    timer: 1500, // Duration in milliseconds before the toast disappears
                    toast: true,
                });
                return false;
            }
            else{
                $.ajax({
                    type: "GET",
                    url: "{{ route('front.getRefund') }}",
                    data: {invoice_id: invoice_id, refund_type: refund_type},
                    success: function (response) {
                        var total = response.invoice.total_amount;
                        if(refund_type == "Full"){
                            $('#refund_amount').val(total);
                            $('#refund_amount').attr('readonly', 'true');
                        }
                        else{
                            $('#refund_amount').val(total);
                            // $('#refund_amount').attr('disabled', 'false');
                            $('#refund_amount').removeAttr('readonly');
                        }
                    }
                });
            }

        });
    });
</script>




<script>
    $(document).ready(function () {
        $('#refund_form').on('submit', function (e) {
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

                    var refunds = response.refunds;
                    var table_content = ''
                    refunds.forEach(function(refund, index) {
                        table_content += '<tr>\
                                <td>' + (index + 1) + '</td>\
                                <td>' + refund.invoice.invoice_number + '</td>\
                                <td>' + refund.refund_type + '</td>\
                                <td>' + refund.refund_amount+ '</td>\
                                <td>' + refund.claim_date + '</td>\
                                <td>' + refund.merchant.name + '</td>\
                            </tr>';
                        });
                        // Clear the table first and then append the new rows
                        $('#refund_table tbody').empty().append(table_content);





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
                    $('#refund_form')[0].reset();
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
                        $('#service_area').prepend(errorHtml); // Add errors to the form

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
{{-- End Refund --}}

{{-- Charge Back --}}

<script>
    $(document).ready(function () {
        $('#chargeback_form').on('submit', function (e) {
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

                    console.log(response);

                    var chargeBack = response.chargeBack;
                    var table_content = ''
                    chargeBack.forEach(function(chargeback, index) {
                        table_content += '<tr>\
                                <td>' + (index + 1) + '</td>\
                                <td>' + chargeback.invoice.invoice_number + '</td>\
                                <td>' + chargeback.claim_date + '</td>\
                                <td>' + chargeback.merchant.name + '</td>\
                            </tr>';
                        });
                        // Clear the table first and then append the new rows
                        $('#chargeBack_table tbody').empty().append(table_content);





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
                    $('#refund_form')[0].reset();
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
                        $('#service_area').prepend(errorHtml); // Add errors to the form

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


{{-- End Charge Back --}}
@endsection




