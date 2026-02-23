@extends('layouts.dashboard')
@section('css')

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />

@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sale /</span> Report</h4>
    <div class="card">
        <div class="card-header">
          <h5 class="card-title">Filter</h5>
          <form action="{{ route('salereport.store') }}" method="POST" >
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
            <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                <div class="col-md-3 product_status">
                    <div class="form-floating form-floating-outline">
                        <input type="text" id="flatpickr-range"  name="date" class="form-control" />
                        <label for="flatpickr-range">Date</label>
                    </div>
                </div>
                <div class="col-md-3 product_category">
                    <div class="form-floating form-floating-outline">
                        <select id="agnet_select" name="agent" class="select2 form-select" data-allow-clear="true">
                            @if(isset($agent))
                            <option value="">Please Select</option>
                            @foreach($agent as $user)
                            <option value="{{ $user->id }}">{{ explode(' -', $user->name )[0] }}</option>
                            @endforeach
                            @endif

                        </select>
                        <label for="multicol-country">Agent</label>
                    </div>
                    {{-- <div class="filters-agent"></div> --}}
                </div>
                <div class="col-md-3 product_stock">
                    <div class="form-floating form-floating-outline">
                        <select id="type" name="type" class="select2 form-select" data-allow-clear="true">
                            <option value="">Please Select</option>
                            <option value="Development">Development</option>
                            <option value="Marketing">Marketing</option>

                        </select>
                        <label for="multicol-country">Type</label>
                    </div>

                </div>
                <div class="col-md-3 product_stock" style="text-align: center">
                    <button type="submit" class="dt-button add-new btn btn-primary waves-effect waves-light" tabindex="0"
                         style="color: #fff"><span><i
                                class="mdi mdi-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Get
                                Stats</span></span></button>
                </div>
            </div>
          </form>
        </div>
    </div>
</div>
@endsection
@section('js')
    {{-- <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/app-ecommerce-product-list.js') }}"></script> --}}
       {{-- <script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script> --}}
       {{-- <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script> --}}
       <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
       <script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets/js/forms-pickers.js') }}"></script>
@endsection