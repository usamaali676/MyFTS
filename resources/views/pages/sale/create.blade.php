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
                                                            <option value="America/New_York">Eastern Time (ET)</option>
                                                            <option value="America/Chicago">Central Time (CT)</option>
                                                            <option value="America/Denver">Mountain Time (MT)</option>
                                                            <option value="America/Los_Angeles">Pacific Time (PT)</option>
                                                            <option value="America/Anchorage">Alaska Time (AKT)</option>
                                                            <option value="Pacific/Honolulu">Hawaii-Aleutian Time (HAST)</option>
                                                        </optgroup>

                                                        <optgroup label="Global Time Zones">
                                                            <option value="Africa/Abidjan">GMT (Abidjan)</option>
                                                            <option value="Africa/Accra">GMT (Accra)</option>
                                                            <option value="Africa/Addis_Ababa">EAT (Addis Ababa)</option>
                                                            <option value="Africa/Algiers">CET (Algiers)</option>
                                                            <option value="Africa/Asmara">EAT (Asmara)</option>
                                                            <option value="Africa/Bangui">WAT (Bangui)</option>
                                                            <option value="Africa/Banjul">GMT (Banjul)</option>
                                                            <option value="Africa/Bissau">GMT (Bissau)</option>
                                                            <option value="Africa/Blantyre">CAT (Blantyre)</option>
                                                            <option value="Africa/Brazzaville">WAT (Brazzaville)</option>
                                                            <option value="Africa/Bujumbura">CAT (Bujumbura)</option>
                                                            <option value="Africa/Cairo">EET (Cairo)</option>
                                                            <option value="Africa/Casablanca">WET (Casablanca)</option>
                                                            <option value="Africa/Ceuta">CET (Ceuta)</option>
                                                            <option value="Africa/Conakry">GMT (Conakry)</option>
                                                            <option value="Africa/Dakar">GMT (Dakar)</option>
                                                            <option value="Africa/Dar_es_Salaam">EAT (Dar es Salaam)</option>
                                                            <option value="Africa/Djibouti">EAT (Djibouti)</option>
                                                            <option value="Africa/El_Aaiun">WET (El Aaiun)</option>
                                                            <option value="Africa/Freetown">GMT (Freetown)</option>
                                                            <option value="Africa/Gaborone">CAT (Gaborone)</option>
                                                            <option value="Africa/Harare">CAT (Harare)</option>
                                                            <option value="Africa/Johannesburg">SAST (Johannesburg)</option>
                                                            <option value="Africa/Juba">CAT (Juba)</option>
                                                            <option value="Africa/Kampala">EAT (Kampala)</option>
                                                            <option value="Africa/Khartoum">CAT (Khartoum)</option>
                                                            <option value="Africa/Kigali">CAT (Kigali)</option>
                                                            <option value="Africa/Kinshasa">WAT (Kinshasa)</option>
                                                            <option value="Africa/Lagos">WAT (Lagos)</option>
                                                            <option value="Africa/Libreville">WAT (Libreville)</option>
                                                            <option value="Africa/Lome">GMT (Lome)</option>
                                                            <option value="Africa/Luanda">WAT (Luanda)</option>
                                                            <option value="Africa/Lubumbashi">CAT (Lubumbashi)</option>
                                                            <option value="Africa/Lusaka">CAT (Lusaka)</option>
                                                            <option value="Africa/Malabo">WAT (Malabo)</option>
                                                            <option value="Africa/Maputo">CAT (Maputo)</option>
                                                            <option value="Africa/Maseru">CAT (Maseru)</option>
                                                            <option value="Africa/Mbabane">SAST (Mbabane)</option>
                                                            <option value="Africa/Mogadishu">EAT (Mogadishu)</option>
                                                            <option value="Africa/Nairobi">EAT (Nairobi)</option>
                                                            <option value="Africa/Niamey">WAT (Niamey)</option>
                                                            <option value="Africa/Nouakchott">GMT (Nouakchott)</option>
                                                            <option value="Africa/Ouagadougou">GMT (Ouagadougou)</option>
                                                            <option value="Africa/Porto-Novo">WAT (Porto-Novo)</option>
                                                            <option value="Africa/Sao_Tome">GMT (Sao Tome)</option>
                                                            <option value="Africa/Tunis">CET (Tunis)</option>
                                                            <option value="Africa/Tripoli">EET (Tripoli)</option>
                                                            <option value="America/Adak">HAST (Adak)</option>
                                                            <option value="America/Anchorage">AKST (Anchorage)</option>
                                                            <option value="America/Anguilla">AST (Anguilla)</option>
                                                            <option value="America/Antigua">AST (Antigua)</option>
                                                            <option value="America/Argentina/Buenos_Aires">ART (Buenos Aires)</option>
                                                            <option value="America/Argentina/Catamarca">ART (Catamarca)</option>
                                                            <option value="America/Argentina/ComodRivadavia">ART (ComodRivadavia)</option>
                                                            <option value="America/Argentina/Cordoba">ART (Cordoba)</option>
                                                            <option value="America/Argentina/Jujuy">ART (Jujuy)</option>
                                                            <option value="America/Argentina/La_Rioja">ART (La Rioja)</option>
                                                            <option value="America/Argentina/Mendoza">ART (Mendoza)</option>
                                                            <option value="America/Argentina/Rosario">ART (Rosario)</option>
                                                            <option value="America/Argentina/Tucuman">ART (Tucuman)</option>
                                                            <option value="America/Argentina/Ushuaia">ART (Ushuaia)</option>
                                                            <option value="America/Aruba">AST (Aruba)</option>
                                                            <option value="America/Asuncion">PYT (Asuncion)</option>
                                                            <option value="America/Atikokan">EST (Atikokan)</option>
                                                            <option value="America/Barbados">AST (Barbados)</option>
                                                            <option value="America/Belize">CST (Belize)</option>
                                                            <option value="America/Blanc-Sablon">AST (Blanc-Sablon)</option>
                                                            <option value="America/Boa_Vista">AMT (Boa Vista)</option>
                                                            <option value="America/Bogota">COT (Bogota)</option>
                                                            <option value="America/Boise">MDT (Boise)</option>
                                                            <option value="America/Cambridge_Bay">MDT (Cambridge Bay)</option>
                                                            <option value="America/Cancun">EST (Cancun)</option>
                                                            <option value="America/Caracas">VET (Caracas)</option>
                                                            <option value="America/Cayman">EST (Cayman)</option>
                                                            <option value="America/Chicago">CST (Chicago)</option>
                                                            <option value="America/Chihuahua">MDT (Chihuahua)</option>
                                                            <option value="America/Costa_Rica">CST (Costa Rica)</option>
                                                            <option value="America/Creston">MST (Creston)</option>
                                                            <option value="America/Cuiaba">AMT (Cuiaba)</option>
                                                            <option value="America/Curacao">AST (Curacao)</option>
                                                            <option value="America/Dawson">PST (Dawson)</option>
                                                            <option value="America/Dawson_Creek">MST (Dawson Creek)</option>
                                                            <option value="America/Denver">MDT (Denver)</option>
                                                            <option value="America/Detroit">EDT (Detroit)</option>
                                                            <option value="America/Dominica">AST (Dominica)</option>
                                                            <option value="America/Edmonton">MDT (Edmonton)</option>
                                                            <option value="America/Eirunepe">ACT (Eirunepe)</option>
                                                            <option value="America/El_Salvador">CST (El Salvador)</option>
                                                            <option value="America/Fortaleza">BRT (Fortaleza)</option>
                                                            <option value="America/Fort_Wayne">EST (Fort Wayne)</option>
                                                            <option value="America/Glace_Bay">AST (Glace Bay)</option>
                                                            <option value="America/Godthab">WGT (Godthab)</option>
                                                            <option value="America/Goose_Bay">AST (Goose Bay)</option>
                                                            <option value="America/Grand_Turk">EST (Grand Turk)</option>
                                                            <option value="America/Grenada">AST (Grenada)</option>
                                                            <option value="America/Guadeloupe">AST (Guadeloupe)</option>
                                                            <option value="America/Guatemala">CST (Guatemala)</option>
                                                            <option value="America/Guyana">GYT (Guyana)</option>
                                                            <option value="America/Halifax">AST (Halifax)</option>
                                                            <option value="America/Havana">CST (Havana)</option>
                                                            <option value="America/Hermosillo">MST (Hermosillo)</option>
                                                            <option value="America/Indiana/Indianapolis">EST (Indianapolis)</option>
                                                            <option value="America/Indiana/Knox">CST (Knox)</option>
                                                            <option value="America/Indiana/Marengo">EST (Marengo)</option>
                                                            <option value="America/Indiana/Petersburg">EST (Petersburg)</option>
                                                            <option value="America/Indiana/Tell_City">CST (Tell City)</option>
                                                            <option value="America/Indiana/Vevay">EST (Vevay)</option>
                                                            <option value="America/Indiana/Winamac">EST (Winamac)</option>
                                                            <option value="America/Indianapolis">EDT (Indianapolis)</option>
                                                            <option value="America/Inuvik">MDT (Inuvik)</option>
                                                            <option value="America/Iqaluit">EDT (Iqaluit)</option>
                                                            <option value="America/Jamaica">EST (Jamaica)</option>
                                                            <option value="America/Juneau">AKDT (Juneau)</option>
                                                            <option value="America/Kentucky/Louisville">EDT (Louisville)</option>
                                                            <option value="America/Kentucky/Monticello">EDT (Monticello)</option>
                                                            <option value="America/Kralendijk">AST (Kralendijk)</option>
                                                            <option value="America/La_Paz">BOT (La Paz)</option>
                                                            <option value="America/Lima">PET (Lima)</option>
                                                            <option value="America/Los_Angeles">PDT (Los Angeles)</option>
                                                            <option value="America/Maceio">BRT (Maceio)</option>
                                                            <option value="America/Managua">CST (Managua)</option>
                                                            <option value="America/Manaus">AMT (Manaus)</option>
                                                            <option value="America/Marigot">AST (Marigot)</option>
                                                            <option value="America/Martinique">AST (Martinique)</option>
                                                            <option value="America/Matamoros">CST (Matamoros)</option>
                                                            <option value="America/Mexico_City">CST (Mexico City)</option>
                                                            <option value="America/Miquelon">PMST (Miquelon)</option>
                                                            <option value="America/Moncton">AST (Moncton)</option>
                                                            <option value="America/Montreal">EDT (Montreal)</option>
                                                            <option value="America/Montserrat">AST (Montserrat)</option>
                                                            <option value="America/Nassau">EST (Nassau)</option>
                                                            <option value="America/New_York">EDT (New York)</option>
                                                            <option value="America/Nipigon">EST (Nipigon)</option>
                                                            <option value="America/Nome">AKDT (Nome)</option>
                                                            <option value="America/Noronha">FNT (Noronha)</option>
                                                            <option value="America/North_Dakota/Beulah">CST (Beulah)</option>
                                                            <option value="America/North_Dakota/Center">CST (Center)</option>
                                                            <option value="America/North_Dakota/New_Salem">CST (New Salem)</option>
                                                            <option value="America/Ojinaga">MDT (Ojinaga)</option>
                                                            <option value="America/Panama">EST (Panama)</option>
                                                            <option value="America/Phoenix">MST (Phoenix)</option>
                                                            <option value="America/Port-au-Prince">EST (Port-au-Prince)</option>
                                                            <option value="America/Porto_Velho">AMT (Porto Velho)</option>
                                                            <option value="America/Puerto_Rico">AST (Puerto Rico)</option>
                                                            <option value="America/Punta_Arenas">CLT (Punta Arenas)</option>
                                                            <option value="America/Rainy_River">CST (Rainy River)</option>
                                                            <option value="America/Ramallah">EET (Ramallah)</option>
                                                            <option value="America/Rankin_Inlet">CST (Rankin Inlet)</option>
                                                            <option value="America/Recife">BRT (Recife)</option>
                                                            <option value="America/Regina">CST (Regina)</option>
                                                            <option value="America/Resolute">CST (Resolute)</option>
                                                            <option value="America/Rio_Branco">ACT (Rio Branco)</option>
                                                            <option value="America/Santarem">AMT (Santarem)</option>
                                                            <option value="America/Santiago">CLT (Santiago)</option>
                                                            <option value="America/Santo_Domingo">AST (Santo Domingo)</option>
                                                            <option value="America/Sao_Paulo">BRT (Sao Paulo)</option>
                                                            <option value="America/Scoresbysund">EGT (Scoresbysund)</option>
                                                            <option value="America/Sitka">AKDT (Sitka)</option>
                                                            <option value="America/St_Barthelemy">AST (St Barthelemy)</option>
                                                            <option value="America/St_Johns">NST (St Johns)</option>
                                                            <option value="America/St_Kitts">AST (St Kitts)</option>
                                                            <option value="America/St_Lucia">AST (St Lucia)</option>
                                                            <option value="America/St_Thomas">AST (St Thomas)</option>
                                                            <option value="America/St_Vincent">AST (St Vincent)</option>
                                                            <option value="America/Swift_Current">CST (Swift Current)</option>
                                                            <option value="America/Tegucigalpa">CST (Tegucigalpa)</option>
                                                            <option value="America/Thule">WGT (Thule)</option>
                                                            <option value="America/Thunder_Bay">EST (Thunder Bay)</option>
                                                            <option value="America/Toronto">EDT (Toronto)</option>
                                                            <option value="America/Tortola">AST (Tortola)</option>
                                                            <option value="America/Vancouver">PDT (Vancouver)</option>
                                                            <option value="America/Winnipeg">CST (Winnipeg)</option>
                                                            <option value="America/Yakutat">AKDT (Yakutat)</option>
                                                            <option value="America/Yellowknife">MDT (Yellowknife)</option>
                                                            <option value="Antarctica/Casey">CAST (Casey)</option>
                                                            <option value="Antarctica/Davis">DAVT (Davis)</option>
                                                            <option value="Antarctica/DumontDUrville">DDT (Dumont d'Urville)</option>
                                                            <option value="Antarctica/Macquarie">MIST (Macquarie)</option>
                                                            <option value="Antarctica/McMurdo">NZDT (McMurdo)</option>
                                                            <option value="Antarctica/Palmer">CLT (Palmer)</option>
                                                            <option value="Antarctica/Syowa">SYOT (Syowa)</option>
                                                            <option value="Antarctica/Troll">UTC (Troll)</option>
                                                            <option value="Antarctica/Vostok">VOST (Vostok)</option>
                                                            <option value="Arctic/Longyearbyen">CET (Longyearbyen)</option>
                                                            <option value="Asia/Aden">AST (Aden)</option>
                                                            <option value="Asia/Almaty">ALMT (Almaty)</option>
                                                            <option value="Asia/Amman">EET (Amman)</option>
                                                            <option value="Asia/Aqtau">AQTT (Aqtau)</option>
                                                            <option value="Asia/Aqtobe">AQTT (Aqtobe)</option>
                                                            <option value="Asia/Ashgabat">TMT (Ashgabat)</option>
                                                            <option value="Asia/Ashkhabad">TMT (Ashkhabad)</option>
                                                            <option value="Asia/Bahrain">AST (Bahrain)</option>
                                                            <option value="Asia/Bangkok">ICT (Bangkok)</option>
                                                            <option value="Asia/Barnaul">ALMT (Barnaul)</option>
                                                            <option value="Asia/Beirut">EET (Beirut)</option>
                                                            <option value="Asia/Bishkek">KGT (Bishkek)</option>
                                                            <option value="Asia/Brunei">BNT (Brunei)</option>
                                                            <option value="Asia/Chita">IRKT (Chita)</option>
                                                            <option value="Asia/Choibalsan">ULAT (Choibalsan)</option>
                                                            <option value="Asia/Colombo">IST (Colombo)</option>
                                                            <option value="Asia/Damascus">EET (Damascus)</option>
                                                            <option value="Asia/Dhaka">BST (Dhaka)</option>
                                                            <option value="Asia/Dili">TLT (Dili)</option>
                                                            <option value="Asia/Dubai">GST (Dubai)</option>
                                                            <option value="Asia/Dushanbe">TJT (Dushanbe)</option>
                                                            <option value="Asia/Famagusta">EET (Famagusta)</option>
                                                            <option value="Asia/Gaza">EET (Gaza)</option>
                                                            <option value="Asia/Hebron">EET (Hebron)</option>
                                                            <option value="Asia/Ho_Chi_Minh">ICT (Ho Chi Minh)</option>
                                                            <option value="Asia/Hong_Kong">HKT (Hong Kong)</option>
                                                            <option value="Asia/Hovd">HOVT (Hovd)</option>
                                                            <option value="Asia/Irkutsk">IRKT (Irkutsk)</option>
                                                            <option value="Asia/Jakarta">WIB (Jakarta)</option>
                                                            <option value="Asia/Jayapura">WIT (Jayapura)</option>
                                                            <option value="Asia/Jerusalem">IST (Jerusalem)</option>
                                                            <option value="Asia/Kabul">AFT (Kabul)</option>
                                                            <option value="Asia/Kamchatka">PETT (Kamchatka)</option>
                                                            <option value="Asia/Karachi">PKT (Karachi)</option>
                                                            <option value="Asia/Katmandu">NPT (Kathmandu)</option>
                                                            <option value="Asia/Kolkata">IST (Kolkata)</option>
                                                            <option value="Asia/Krasnoyarsk">KRAT (Krasnoyarsk)</option>
                                                            <option value="Asia/Kuala_Lumpur">MYT (Kuala Lumpur)</option>
                                                            <option value="Asia/Kuching">MYT (Kuching)</option>
                                                            <option value="Asia/Macau">CST (Macau)</option>
                                                            <option value="Asia/Magadan">MAGT (Magadan)</option>
                                                            <option value="Asia/Makassar">WITA (Makassar)</option>
                                                            <option value="Asia/Manila">PHT (Manila)</option>
                                                            <option value="Asia/Muscat">GST (Muscat)</option>
                                                            <option value="Asia/Nicosia">EET (Nicosia)</option>
                                                            <option value="Asia/Novokuznetsk">KRAST (Novokuznetsk)</option>
                                                            <option value="Asia/Novosibirsk">NOVT (Novosibirsk)</option>
                                                            <option value="Asia/Omsk">OMST (Omsk)</option>
                                                            <option value="Asia/Oral">ORAT (Oral)</option>
                                                            <option value="Asia/Phnom_Penh">ICT (Phnom Penh)</option>
                                                            <option value="Asia/Pontianak">WIB (Pontianak)</option>
                                                            <option value="Asia/Pyongyang">KST (Pyongyang)</option>
                                                            <option value="Asia/Qatar">AST (Qatar)</option>
                                                            <option value="Asia/Qyzylorda">QYZT (Qyzylorda)</option>
                                                            <option value="Asia/Riyadh">AST (Riyadh)</option>
                                                            <option value="Asia/Sakhalin">PETT (Sakhalin)</option>
                                                            <option value="Asia/Samarkand">UZT (Samarkand)</option>
                                                            <option value="Asia/Taipei">CST (Taipei)</option>
                                                            <option value="Asia/Tashkent">UZT (Tashkent)</option>
                                                            <option value="Asia/Tbilisi">GET (Tbilisi)</option>
                                                            <option value="Asia/Tehran">IRST (Tehran)</option>
                                                            <option value="Asia/Thimphu">BTT (Thimphu)</option>
                                                            <option value="Asia/Tokyo">JST (Tokyo)</option>
                                                            <option value="Asia/Ulaanbaatar">ULAT (Ulaanbaatar)</option>
                                                            <option value="Asia/Urumqi">XJT (Urumqi)</option>
                                                            <option value="Asia/Vientiane">ICT (Vientiane)</option>
                                                            <option value="Asia/Vladivostok">VLAT (Vladivostok)</option>
                                                            <option value="Asia/Yakutsk">YAKT (Yakutsk)</option>
                                                            <option value="Asia/Yerevan">AMT (Yerevan)</option>
                                                            <option value="Atlantic/Azores">AZOT (Azores)</option>
                                                            <option value="Atlantic/Bermuda">AST (Bermuda)</option>
                                                            <option value="Atlantic/Canary">WET (Canary)</option>
                                                            <option value="Atlantic/Cape_Verde">CVT (Cape Verde)</option>
                                                            <option value="Atlantic/Faeroe">WET (Faeroe)</option>
                                                            <option value="Atlantic/Jan_Mayen">CET (Jan Mayen)</option>
                                                            <option value="Atlantic/Madeira">WET (Madeira)</option>
                                                            <option value="Atlantic/Reykjavik">GMT (Reykjavik)</option>
                                                            <option value="Atlantic/South_Georgia">GST (South Georgia)</option>
                                                            <option value="Atlantic/Stanley">FKT (Stanley)</option>
                                                            <option value="Australia/Adelaide">ACDT (Adelaide)</option>
                                                            <option value="Australia/Brisbane">AEST (Brisbane)</option>
                                                            <option value="Australia/Broken_Hill">ACDT (Broken Hill)</option>
                                                            <option value="Australia/Currie">AEDT (Currie)</option>
                                                            <option value="Australia/Darwin">ACST (Darwin)</option>
                                                            <option value="Australia/Eucla">ACWST (Eucla)</option>
                                                            <option value="Australia/Hobart">AEDT (Hobart)</option>
                                                            <option value="Australia/Lindeman">AEST (Lindeman)</option>
                                                            <option value="Australia/Melbourne">AEDT (Melbourne)</option>
                                                            <option value="Australia/Perth">AWST (Perth)</option>
                                                            <option value="Australia/Sydney">AEDT (Sydney)</option>
                                                            <option value="Australia/Tasmania">AEDT (Tasmania)</option>
                                                            <option value="Australia/Brisbane">AEST (Brisbane)</option>
                                                            <option value="Australia/Currie">AEDT (Currie)</option>
                                                            <option value="Australia/Darwin">ACST (Darwin)</option>
                                                            <option value="Australia/Eucla">ACWST (Eucla)</option>
                                                            <option value="Australia/Hobart">AEDT (Hobart)</option>
                                                            <option value="Australia/Lindeman">AEST (Lindeman)</option>
                                                            <option value="Australia/Melbourne">AEDT (Melbourne)</option>
                                                            <option value="Australia/Perth">AWST (Perth)</option>
                                                            <option value="Australia/Sydney">AEDT (Sydney)</option>
                                                            <option value="Australia/Tasmania">AEDT (Tasmania)</option>
                                                            <option value="Etc/GMT+12">GMT-12:00</option>
                                                            <option value="Etc/GMT+11">GMT-11:00</option>
                                                            <option value="Etc/GMT+10">GMT-10:00</option>
                                                            <option value="Etc/GMT+9">GMT-9:00</option>
                                                            <option value="Etc/GMT+8">GMT-8:00</option>
                                                            <option value="Etc/GMT+7">GMT-7:00</option>
                                                            <option value="Etc/GMT+6">GMT-6:00</option>
                                                            <option value="Etc/GMT+5">GMT-5:00</option>
                                                            <option value="Etc/GMT+4">GMT-4:00</option>
                                                            <option value="Etc/GMT+3">GMT-3:00</option>
                                                            <option value="Etc/GMT+2">GMT-2:00</option>
                                                            <option value="Etc/GMT+1">GMT-1:00</option>
                                                            <option value="Etc/GMT">GMT</option>
                                                            <option value="Etc/GMT-1">GMT+1:00</option>
                                                            <option value="Etc/GMT-2">GMT+2:00</option>
                                                            <option value="Etc/GMT-3">GMT+3:00</option>
                                                            <option value="Etc/GMT-4">GMT+4:00</option>
                                                            <option value="Etc/GMT-5">GMT+5:00</option>
                                                            <option value="Etc/GMT-6">GMT+6:00</option>
                                                            <option value="Etc/GMT-7">GMT+7:00</option>
                                                            <option value="Etc/GMT-8">GMT+8:00</option>
                                                            <option value="Etc/GMT-9">GMT+9:00</option>
                                                            <option value="Etc/GMT-10">GMT+10:00</option>
                                                            <option value="Etc/GMT-11">GMT+11:00</option>
                                                            <option value="Etc/GMT-12">GMT+12:00</option>
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
                                            {{-- <div class="col-md-12" style="display: flex; flex-direction: column; gap: 25px;">
                                                <!-- Day -->
                                                <div class="row opening-day">
                                                    <div class="col-md-2">
                                                        <h5>Monday <input type="hidden" name="day[]" value="Monday"></h5>
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input" value="@if(isset($sale && $sale->business_hours))  @endif" name="open[]" id="monday_open">
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input" value="@if(isset($sale && $sale->business_hours))  @endif" name="closed[]" id="monday_closed">
                                                    </div>
                                                    <div class="col-md-2 d-flex" style="gap: 20px;">
                                                        <div class="form-check custom-option custom-option-basic checked">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp1">
                                                                <input  data-day="check" data-day-name="monday" name="monday_check" class="form-check-input" type="radio" value="open" id="customRadioTemp1"
                                                                    checked="">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Open</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp2">
                                                                <input  data-day="check" data-day-name="monday" name="monday_check" class="form-check-input" type="radio" value="closed"
                                                                    id="customRadioTemp2">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Closed</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp3">
                                                                <input  data-day="check" data-day-name="monday" name="monday_check" class="form-check-input" type="radio" value="24/7"
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
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="open[]" id="tuesday_open">
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="closed[]" id="tuesday_closed">
                                                    </div>
                                                    <div class="col-md-2 d-flex" style="gap: 20px;">
                                                        <div class="form-check custom-option custom-option-basic checked">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp4">
                                                                <input  data-day="check" data-day-name="tuesday" name="tuesday_check" class="form-check-input" type="radio" value="open" id="customRadioTemp4"
                                                                    checked="">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Open</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp5">
                                                                <input  data-day="check" data-day-name="tuesday" name="tuesday_check" class="form-check-input" type="radio" value="closed"
                                                                    id="customRadioTemp5">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Closed</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp6">
                                                                <input  data-day="check" data-day-name="tuesday" name="tuesday_check" class="form-check-input" type="radio" value="24/7"
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
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="open[]" id="wednesday_open">
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="closed[]" id="wednesday_closed">
                                                    </div>
                                                    <div class="col-md-2 d-flex" style="gap: 20px;">
                                                        <div class="form-check custom-option custom-option-basic checked">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp7">
                                                                <input  data-day="check" data-day-name="wednesday" name="wednesday_check" class="form-check-input" type="radio" value="open" id="customRadioTemp7"
                                                                    checked="">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Open</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp8">
                                                                <input  data-day="check" data-day-name="wednesday" name="wednesday_check" class="form-check-input" type="radio" value="closed"
                                                                    id="customRadioTemp8">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Closed</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp9">
                                                                <input  data-day="check" data-day-name="wednesday" name="wednesday_check" class="form-check-input" type="radio" value="24/7"
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
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="open[]" id="thursday_open">
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="closed[]" id="thursday_closed">
                                                    </div>
                                                    <div class="col-md-2 d-flex" style="gap: 20px;">
                                                        <div class="form-check custom-option custom-option-basic checked">
                                                            <label   class="form-check-label custom-option-content" for="customRadioTemp10">
                                                                <input data-day="check" data-day-name="thursday" name="thursday_check" class="form-check-input" type="radio" value="open" id="customRadioTemp10"
                                                                    checked="">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Open</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp11">
                                                                <input data-day="check" data-day-name="thursday" name="thursday_check" class="form-check-input" type="radio" value="closed"
                                                                    id="customRadioTemp11">
                                                                <span class="custom-option-header">
                                                                    <span  class="h6 mb-0">Closed</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp12">
                                                                <input data-day="check" data-day-name="thursday" name="thursday_check" class="form-check-input" type="radio" value="24/7"
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
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="open[]" id="friday_open">
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="closed[]" id="friday_closed">
                                                    </div>
                                                    <div class="col-md-2 d-flex" style="gap: 20px;">
                                                        <div class="form-check custom-option custom-option-basic checked">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp13">
                                                                <input  data-day="check" data-day-name="friday" name="friday_check" class="form-check-input" type="radio" value="open" id="customRadioTemp13"
                                                                    checked="">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Open</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp14">
                                                                <input  data-day="check" data-day-name="friday" name="friday_check" class="form-check-input" type="radio" value="closed"
                                                                    id="customRadioTemp14">
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">Closed</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content" for="customRadioTemp15">
                                                                <input  data-day="check" data-day-name="friday" name="friday_check" class="form-check-input" type="radio" value="24/7"
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
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="open[]" id="saturday_open">
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="closed[]" id="saturday_closed">
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
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="open[]" id="sunday_open">
                                                    </div>
                                                    <div class="col-md-3 form-floating form-floating-outline">
                                                        <input type="time" class="form-control flatpickr-input " value="@if(isset($sale && $sale->business_hours))  @endif" name="closed[]" id="sunday_closed">
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
                                            </div> --}}

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
                                                                <td>{{ $keyword->area->country }}, {{ $keyword->area->state }}, {{ $keyword->area->city }}</td>
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
                                                        @if(isset($invoice) && isset($invoice->activation_date)) value="{{ $invoice->activation_date }}"
                                                    @endif
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

                                                            @if(isset($invoice) && count($invoice->servicecharges) > 0)
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
                                                            @endif

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row g-4 pt-3">
                                            <div class="col-md-6 col-12 mb-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="month" class="form-control flatpickr-input active" format="YYYY" name="month"
                                                    @if(isset($invoice) && isset($invoice->month))
                                                        value="{{ $invoice->month }}"
                                                    @endif
                                                    placeholder="YYYY-MM-DD" id="flatpickr-date" readonly="readonly">
                                                    <label for="flatpickr-date">Month</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="discount" name="discount_type" class="select2 form-select" data-allow-clear="true">
                                                        <option value="">Please Select</option>
                                                        @if(isset($invoice) && isset($invoice->discount_type))
                                                        <option value="{{$invoice->discount_type}}" selected>{{ $invoice->discount_type }}</option>
                                                        @endif
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
                                                            <input type="number" id="discount_amount" name="discount_amount" @if(isset($invoice) &&
                                                                isset($invoice->discount_amount)) value="{{ $invoice->discount_amount }}" @endif
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
                                                        @if(isset($invoice) && isset($invoice->invoice_due_date)) value="{{ $invoice->invoice_due_date }}"
                                                    @endif
                                                    placeholder="YYYY-MM-DD" id="flatpickr-date" readonly="readonly">
                                                    <label for="flatpickr-date">Invoice Due Date</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="invoice_freq" name="invoice_freq" class="select2 form-select" data-allow-clear="true">
                                                        <option value="">Please Select</option>
                                                        @if(isset($invoice) && isset($invoice->invoice_frequency))
                                                        <option value="{{$invoice->invoice_frequency}}" selected>{{ $invoice->invoice_frequency }}</option>
                                                        @endif
                                                        <option value="Monthly">Monthly</option>
                                                        <option value="Bi-annually">Bi-annually</option>
                                                        <option value="Annually">Annually</option>
                                                    </select>
                                                    <label for="multicol-country">Invoice Frequency</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mb-6">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="invoice_number" name="invoice_no" class="form-control" @php
                                                        $date=Carbon\Carbon::now()->format('M Y');
                                                    // dd($date);
                                                    @endphp

                                                    @if((isset($invoice)) && $invoice->month == $date)
                                                    @if(isset($invoice)) value="{{ $invoice->invoice_number }}" @endif
                                                    @endif
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
                                                                placeholder="499" @if(isset($invoice) && isset($invoice->total_amount)) value="{{
                                                            $invoice->total_amount }}" @endif
                                                            aria-label="Amount (to the nearest dollar)" readonly>
                                                            <label>Invoice Amount </label>
                                                        </div>
                                                        <span class="input-group-text">.00</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 text-right">
                                                <div class="invoice-button d-flex " style="gap: 0px 20px;">

                                                        <a id="view-invoice" @if (isset($invoice) && isset($invoice->invoice_number)) style="display: block !important;"  href="{{ route('front.invoiceView', $invoice->invoice_number) }}" @else style="display: none !important;" @endif  target="_blank" class="btn btn-success" style="color: #fff">View Invoice</a>
                                                        <button id="genrate-invoice" @if (isset($invoice) && isset($invoice->invoice_number)) style="display: none !important;" @else style="display: block !important;" @endif  type="submit" class="btn btn-primary">Generate Invoice</button>

                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                    {{-- <div class="row py-4">
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
                                    </div> --}}
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
    <script src="{{ asset('assets/js/repeater.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/front-page-payment.js') }}"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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



                        var row = '<tr>\
                                    <td>'+response.keyword.keyword+'</td>\
                                    <td>'+response.area.country+', '+response.area.state+', '+response.area.city+'</td>\
                                    <td><a  type="button" id="'+ response.keyword.id+'" class="dropdown-item delete-record keyword_delete" data-confirm="Are you sure to delete this item?"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a></td>\
                                  </tr>'
                        $('#keyword_table').append(row);
                        $('#keyword').val('');
                        $('areas_dropdown').val(response.area.id)




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
                        $('#genrate-invoice').attr('style', "display: none !important");
                        $('#view-invoice').attr('style', "display: block !important; color: #fff !important");
                        $('#view-invoice').attr('href', "/front/invoice/"+ invoice.invoice_number +  "");
                        $('#invoice_number').val(invoice.invoice_number);

                        if(all_invoices){

                            var option = $.map(all_invoices, function (invoice, ) {
                                return '<option value="' + invoice.id + '">' + invoice.invoice_number +' </option>';
                            });

                            $('#invoice_number_id').append(option)
                        }



                    // var invoice = response.invoice;
                    // console.log(invoice);

                    // var table_content = ''
                    // invoice.forEach(function(invoice, index) {
                    //         tableContent += '<tr>\
                    //             <td>' + (index + 1) + '</td>\
                    //             <td>' + invoice.invoice_number + '</td>\
                    //             <td>' + invoice.month + '</td>\
                    //             <td>' + invoice.activation_date + '</td>\
                    //             <td>' + invoice.invoice_due_date + '</td>\
                    //             <td>' + invoice.amount + '</td>\
                    //         </tr>';
                    //     });

                    //     // Clear the table first and then append the new rows
                    //     $('#invoice_table_gen tbody').empty().append(tableContent);




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
                        // $('#make_payment')[0].reset();
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




