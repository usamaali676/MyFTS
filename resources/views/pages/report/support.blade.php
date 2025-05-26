@extends('layouts.dashboard')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/plugins/monthSelect/style.min.css"  />


<style>
    @media print {
        .layout-navbar{
            display: none !important;
        }
        .no-print{
            display: none !important;
        }
        #layout-menu{
            display: none !important;
        }
        .layout-menu-fixed:not(.layout-menu-collapsed) .layout-page, .layout-menu-fixed-offcanvas:not(.layout-menu-collapsed) .layout-page{
            padding-left: 0px !important;
        }
    }
    .flatpickr-monthSelect-theme-dark .flatpickr-current-month input.cur-year{
    color: #000 !important;
}
.flatpickr-monthSelect-theme-dark .flatpickr-monthSelect-month{
    color: #000 !important;
}

</style>

@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="py-3 mb-4"><span class="text-muted fw-light">Support /</span> Report</h4>

      <!-- Product List Widget -->

      {{-- <div class="card mb-4">
        <div class="card-widget-separator-wrapper">
          <div class="card-body card-widget-separator">
            <div class="row gy-4 gy-sm-1">
              <div class="col-sm-6 col-lg-3">
                <div
                  class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                  <div>
                    <p class="mb-2">Development Revenue</p>
                    <h4 class="mb-2" id="dev_rev">--</h4>

                  </div>
                  <div class="avatar me-sm-4">
                    <span class="avatar-initial rounded bg-label-secondary">
                        <i class="mdi mdi-laptop mdi-24px"></i>
                    </span>
                  </div>
                </div>
                <hr class="d-none d-sm-block d-lg-none me-4" />
              </div>
              <div class="col-sm-6 col-lg-3">
                <div
                  class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                  <div>
                    <p class="mb-2">Marketing Revenue</p>
                    <h4 class="mb-2" id="mark_rev">--</h4>

                  </div>
                  <div class="avatar me-lg-4">
                    <span class="avatar-initial rounded bg-label-secondary">
                        <i class="mdi mdi-home-outline mdi-24px"></i>

                    </span>
                  </div>
                </div>
                <hr class="d-none d-sm-block d-lg-none" />
              </div>
              <div class="col-sm-6 col-lg-3">
                <div
                  class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                  <div>
                    <p class="mb-2">Charge Back / Refund</p>
                    <h4 class="mb-2" id="charge_back">--</h4>
                  </div>
                  <div class="avatar me-sm-4">
                    <span class="avatar-initial rounded bg-label-secondary">
                      <i class="mdi ri:arrow-down-double-fill mdi-24px"></i>

                    </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <p class="mb-2">Total Revenue</p>
                    <h4 class="mb-2" id="total_rev">--</h4>

                  </div>
                  <div class="avatar">
                    <span class="avatar-initial rounded bg-label-secondary">
                      <i class="mdi mdi-currency-usd mdi-24px"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> --}}

      <!-- Product List Table -->
      <div class="card">
        <div class="card-header no-print">
            <div class="d-flex" style="justify-content: space-between;">
                <h5 class="card-title" style="margin: auto 0px;">Filter</h5>
                <button id="print" type="btn" onclick="(function() { window.print(); })()" class="btn btn-primary waves-effect waves-light ">Print</button>
            </div>
          <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0 no-print">
            <div class="col-md-3 product_status">
                          <div class="form-floating form-floating-outline">
                            <input type="text" id="month" class="form-control flatpickr-input active" format="YYYY" name="month">
                            <label for="month">Month</label>
                          </div>
            </div>
            {{-- <div class="col-md-3 product_category">
                <div class="form-floating form-floating-outline">
                    <select id="agnet_select" name="agent" class="select2 form-select"
                        data-allow-clear="true">
                        @if(isset($agent))
                        <option value="">Please Select</option>
                        @foreach($agent as $user)
                            <option value="{{ $user->id }}">{{ explode(' -', $user->name )[0] }}</option>
                        @endforeach
                        @endif

                    </select>
                    <label for="multicol-country">Agent</label>
                </div>
            </div>
            <div class="col-md-3 product_stock">
                <div class="form-floating form-floating-outline">
                    <select id="closer" name="agent" class="select2 form-select"
                        data-allow-clear="true">
                        @if(isset($closer))
                        <option value="">Please Select</option>
                        @foreach($closer as $user)
                            <option value="{{ $user->id }}">{{ explode(' -', $user->name )[0] }}</option>
                        @endforeach
                        @endif

                    </select>
                    <label for="multicol-country">Closer</label>
                </div>
            </div>
            <div class="col-md-3 product_stock">
                <div class="form-floating form-floating-outline">
                    <select id="type" name="agent" class="select2 form-select"
                        data-allow-clear="true">
                        <option value="">Please Select</option>
                        <option value="Development">Development</option>
                        <option value="Marketing">Marketing</option>

                    </select>
                    <label for="multicol-country">Type</label>
                </div>

            </div> --}}
          </div>
        </div>
        <div class="card-datatable table-responsive">

            <table id="recodetable" class="table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Support Person</th>
                        <th>Total Clients</th>
                        <th>No. of Charged</th>
                        <th>Pending Payments</th>
                        <th>Stopped Working</th>
                        <th>Total Revenue (Support)</th>
                        <th>UpSell</th>
                        <th>Upsell Revenue</th>
                        <th>Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach($data as $person )
                    <tr>
                      <td></td>
                      <td>{{$person['user_name']}}</td>
                      <td>{{$person['total_sale']}}</td>
                      <td>{{$person['charged_payment_sales']}}</td>
                      <td>{{$person['pending_payment_sales']}}</td>
                      <td>{{$person['inactive_sales']}}</td>
                      <td>{{$person['total_revenue']}}</td>
                      <td>{{ $person['upsellcount'] }}</td>
                      <td>{{ $person['upsellamount'] }}</td>
                      <td>{{ $person['grandtotal'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- / Content -->


  </div>
@endsection
@section('js')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

<script>
    $('#recodetable').DataTable();
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
    $("#month").change(function (e) {
        // e.preventDefault();
        month = $(this).val();
        // alert(month);
        $.ajax({
            type: 'GET',
            url: '{{ route('salereport.reportsupport') }}',
            data: {month: month},
            success: function (response) {
                console.log(response);
                // $("#recodetable").empty();
                var data = response.data;
                var newdata = $.map(data, function (support, index) {
                    return  '<tr>\
                      <td></td>\
                      <td>'+ support.user_name +'</td>\
                      <td>'+ support.total_sale +'</td>\
                      <td>'+ support.charged_payment_sales +'</td>\
                      <td>'+ support.pending_payment_sales +'</td>\
                      <td>' + support.inactive_sales +'</td>\
                      <td>' + support.total_revenue +'</td>\
                    </tr>'
                }).join('');

                $('#recodetable tbody').empty().append(newdata);

            }
        });
    });
</script>



    {{-- <script>
            $(function () {
        let borderColor, bodyBg, headingColor;

        if (isDarkStyle) {
            borderColor = config.colors_dark.borderColor;
            bodyBg = config.colors_dark.bodyBg;
            headingColor = config.colors_dark.headingColor;
        } else {
            borderColor = config.colors.borderColor;
            bodyBg = config.colors.bodyBg;
            headingColor = config.colors.headingColor;
        }

        const dt_product_table = $('.datatables-products');

        if (dt_product_table.length > 0) {
            dt_product_table.DataTable({
            ajax: '{{ route('salereport.show') }}', // <-- Replace this with your controller JSON URL
            columns: [
                { data: 'sr_no' },      // Sr#
                { data: 'date' },       // Date
                { data: 'agent' },      // Agent
                { data: 'closer' },     // Closer
                { data: 'type' },       // Type
                { data: 'comment' },    // Comment
                { data: 'price' }       // Price
            ],
            columnDefs: [
                {
                targets: 0,
                title: 'Sr#'
                },
                {
                targets: 1,
                title: 'Date',
                //   render: function (data) {
                //     return moment(data).format('YYYY-MM-DD'); // Format as needed
                //   }
                },
                {
                targets: 2,
                title: 'Agent'
                },
                {
                targets: 3,
                title: 'Closer'
                },
                {
                targets: 4,
                title: 'Type'
                },
                {
                targets: 5,
                title: 'Comment'
                },
                {
                targets: 6,
                title: 'Price',
                render: function (data) {
                    return '$' + parseFloat(data).toFixed(2); // Optional formatting
                }
                }
            ],
            order: [[1, 'desc']], // Default sort by Date
            responsive: true,
            language: {
                search: '',
                searchPlaceholder: 'Search...',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries'
            }
            });
        }
        });

    </script> --}}

{{-- <script>
    $(document).ready(function () {
        function fetchFilteredData() {
            $.ajax({
                url: '{{ route("salereport.reportfilter") }}', // Update to match your route
                type: 'GET',
                data: {
                date: $('#flatpickr-range').val(),
                agent: $('#agnet_select').val(),
                closer: $('#closer').val(),
                type: $('#type').val()
            },


            success: function (response) {
                console.log(response);

                const table = $('#report-table');
                const tableBody = $('#report-table').find('tbody');

                // Destroy previous DataTable instance (if exists)
                if ($.fn.DataTable.isDataTable(table)) {
                    table.DataTable().clear().destroy();
                }

                tableBody.empty(); // Clear previous rows

                if (response.data.length === 0) {
                    $('#dev_rev').html("$"+ response.summary.development);
                    $('#mark_rev').html("$"+ response.summary.marketing);
                    $('#charge_back').html("$"+ response.summary.chargeback);
                    $('#total_rev').html("$"+ response.summary.total);
                    // alert("No Data Found");
                    // tableBody.append('<tr><td colspan="7" class="text-center">No data found</td></tr>');
                } else {
                    response.data.forEach(row => {
                        const date = Array.isArray(row.date) ? row.date.join(', ') : row.date;
                        const closer = Array.isArray(row.closer) ? row.closer.join(', ') : row.closer;
                        const type = Array.isArray(row.type) ? row.type.join(', ') : row.type;
                        const price = parseFloat(row.price).toFixed(2);

                        tableBody.append(`
                            <tr>
                                <td>${row.sr_no}</td>
                                <td>${date}</td>
                                <td>${row.agent}</td>
                                <td>${closer}</td>
                                <td>${type}</td>
                                <td>${row.comment}</td>
                                <td>$${price}</td>
                            </tr>
                        `);
                    });

                    $('#dev_rev').html("$"+ response.summary.development);
                    $('#mark_rev').html("$"+ response.summary.marketing);
                    $('#charge_back').html("$"+ response.summary.chargeback);
                    $('#total_rev').html("$"+ response.summary.total);
                }

                // ✅ Reinitialize DataTable
                table.DataTable({
                order: [[1, 'desc']],
                responsive: true,
                language: {
                    search: '',
                    searchPlaceholder: 'Search...',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries'
                },
                // dom: 'Bfrtip',
                // buttons: ['excel', 'pdf', 'print'],
                // Add bootstrap styling class if required
                drawCallback: function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                }
                });

            },
            error: function (err) {
                console.error("Filter fetch error:", err);
            }
        });
    }

    // Bind the change/input/select events to all filter fields
    $('#flatpickr-range, #agnet_select, #closer, #type').on('change input', function () {
        fetchFilteredData();
    });

    // If you're using Select2, this ensures it triggers as well
    $('#agnet_select, #closer, #type').on('select2:select select2:unselect', function () {
        fetchFilteredData();
    });
});
</script> --}}



@endsection
