<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template">
  <head>

    @include('layouts.partials.head')

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-statistics.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-analytics.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    {{-- <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script> --}}
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    @yield('css')
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        @include('layouts.partials.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          @include('layouts.partials.nav')

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            @yield('content')
            <!-- / Content -->

            <!-- Footer -->
            @include('layouts.partials.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      {{-- <div class="layout-overlay layout-menu-toggle"></div> --}}

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      {{-- <div class="drag-target"></div> --}}
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">Are You Sure You Want to delete</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-4 mt-2">
                            <p>Deleting this item is a permanent action and cannot be undone. Please confirm if you wish to continue, as all associated data will be lost.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="deleteConfirmButton" href="#" type="button" class="btn btn-primary">Yes Proceed!</a>
                </div>
            </div>
        </div>
    </div>


    @include('layouts.partials.scripts')
    @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])


    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>

    <!-- Main JS -->

    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    @yield('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteConfirmButton = document.getElementById('deleteConfirmButton');

            // Use event delegation to listen for clicks on dynamically added .delete-record elements
            $('#recodetable').on('click', '.delete-record', function() {
                const itemId = $(this).data('id');  // Get the data-id from the clicked button
                const routeName = $(this).data('route');  // Get the data-route from the clicked button

                // Construct the delete URL dynamically
                const deleteUrl = `{{ url('${routeName}/delete', 'id') }}`.replace('id', itemId);

                // Set the URL in the confirmation button's href
                deleteConfirmButton.setAttribute('href', deleteUrl);


            });
        });
    </script>

    <script>
        $('.navbar-nav').click(function (e) {
            e.preventDefault();
            $('.navbar-search-wrapper').removeClass('d-none')
            // $('#global_search').onkeypress(function (e) {

            // });
            $('#global_search').keyup(function (e) {
                let query = $(this).val().toLowerCase();
                // alert(searchValue);
                $.ajax({
                    url: "{{ route('front.global_search') }}",
                    type: 'GET',
                    data: { query: query },
                    dataType: "JSON",
                    success: function (response) {
                        // alert("Success")  ;
                        // let searchData = JSON.parse(response);
                        let searchData = response;

                        console.log(searchData.leads);
                        if(query.length > 0){
                        $('.navbar-search-suggestion').addClass('d-block');
                        }
                        else {
                            $('.navbar-search-suggestion').removeClass('d-block');
                        }
                        let usersearchResultHtml = '';
                        let userappendvalue = '';
                        if(searchData.users.length > 0) {

                            searchData.users.forEach(item => {
                                userappendvalue += '<a href="/user/index"><div><span class="align-middle">'+ item.name + '</span></div></a>';
                            });

                        }
                        else{
                            userappendvalue += '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>'
                            }
                            usersearchResultHtml = '<div class="not-found px-3 py-2">\
                                                    <h6 class="suggestions-header text-primary mb-2">Users</h6>\
                                                    ' + userappendvalue + '\
                                                </div>'

                            $('.tt-dataset-users').html(usersearchResultHtml);

                            let searchResultHtml = '';
                            let appendvalue = '';
                        if(searchData.role.length > 0){

                            searchData.role.forEach(item => {
                                appendvalue += '<a href="#"><div><span class="align-middle">'+ item.name + '</span></div></a>';
                            });
                        }
                        else{
                            appendvalue += '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>'
                            }

                            searchResultHtml = '<div class="not-found px-3 py-2">\
                                                    <h6 class="suggestions-header text-primary mb-2">Roles</h6>\
                                                    ' + appendvalue + '\
                                                </div>'
                            $('.tt-dataset-roles').html(searchResultHtml);


                            let leadsearchResultHtml = '';
                            let leadappendvalue = '';

                        if(searchData.leads.length > 0){
                            searchData.leads.forEach(item => {
                                leadappendvalue += '<a href="#"><div><span class="align-middle">'+ item.business_name_adv + '</span></div></a>';
                            });
                        }
                        else{
                            leadappendvalue += '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>'
                            }

                            leadsearchResultHtml = '<div class="not-found px-3 py-2">\
                                                    <h6 class="suggestions-header text-primary mb-2">Leads</h6>\
                                                    ' + leadappendvalue + '\
                                                </div>'
                            $('.tt-dataset-leads').html(leadsearchResultHtml);


                            let invoicesearchResultHtml = '';
                            let invoiceappendvalue = '';

                        if(searchData.invoice.length > 0){
                            searchData.invoice.forEach(item => {
                                invoiceappendvalue += '<a href="#"><div><span class="align-middle">'+ item.invoice_number + '</span></div></a>';
                            });
                        }
                        else{
                            invoiceappendvalue += '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>'
                            }

                            invoicesearchResultHtml = '<div class="not-found px-3 py-2">\
                                                    <h6 class="suggestions-header text-primary mb-2">Invoices</h6>\
                                                    ' + invoiceappendvalue + '\
                                                </div>'
                            $('.tt-dataset-invoice').html(invoicesearchResultHtml);

                            let categorysearchResultHtml = '';
                            let invappendvalue = '';

                        if(searchData.invoice.length > 0){
                            searchData.invoice.forEach(item => {
                                invoiceappendvalue += '<a href="#"><div><span class="align-middle">'+ item.invoice_number + '</span></div></a>';
                            });
                        }
                        else{
                            invoiceappendvalue += '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>'
                            }

                            invoicesearchResultHtml = '<div class="not-found px-3 py-2">\
                                                    <h6 class="suggestions-header text-primary mb-2">Invoices</h6>\
                                                    ' + invoiceappendvalue + '\
                                                </div>'
                            $('.tt-dataset-invoice').html(invoicesearchResultHtml);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error occurred: " + error);
                    }
                });
            });
            // alert("sdfjsdbf");
        });

//         $(document).click(function (e) {
//     // Check if the click happened outside the .navbar-nav
//     if (!$(e.target).closest('.navbar-nav').length) {
//         $('.navbar-search-wrapper').addClass('d-none');
//     }
// });
    </script>


  </body>
</html>
