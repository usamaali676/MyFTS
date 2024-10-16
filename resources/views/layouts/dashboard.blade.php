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
        const deleteButtons = document.querySelectorAll('.delete-record');
        const deleteConfirmButton = document.getElementById('deleteConfirmButton');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');

                const routeName = this.getAttribute('data-route');
                const deleteUrl = `{{ url('${routeName}/delete', 'id') }}`.replace('id', itemId);
                deleteConfirmButton.setAttribute('href', deleteUrl);
            });
        });
    });
    </script>


{{-- <script>
  $(function () {
    // DOM ready functions
    window.Helpers.initSidebarToggle();

    var searchToggler = $('.search-toggler'),
      searchInputWrapper = $('.search-input-wrapper'),
      searchInput = $('.search-input'),
      contentBackdrop = $('.content-backdrop');

    // Open search input on click of search icon
    if (searchToggler.length) {
      searchToggler.on('click', function () {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      });
    }

    // Open search on 'CTRL+/'
    $(document).on('keydown', function (event) {
      let ctrlKey = event.ctrlKey,
        slashKey = event.which === 191;

      if (ctrlKey && slashKey) {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      }
    });

    // Search functionality
    if (searchInput.length) {
      // Filter function for search
      var filterConfig = function (data) {
        return function findMatches(q, cb) {
          let matches = [];
          data.filter(function (i) {
            if (i.name.toLowerCase().startsWith(q.toLowerCase())) {
              matches.push(i);
            } else if (i.name.toLowerCase().includes(q.toLowerCase())) {
              matches.push(i);
              matches.sort(function (a, b) {
                return b.name < a.name ? 1 : -1;
              });
            }
          });
          cb(matches);
        };
      };

      // Search API AJAX call
      searchInput.on('keyup', function () {
        let query = $(this).val();

        $.ajax({
          url: "{{ route('front.global_search') }}",
          type: 'GET',
          data: { query: query },
          success: function (searchData) {
            // Init typeahead on searchInput
            searchInput.typeahead(
              {
                hint: false,
                classNames: {
                  menu: 'tt-menu navbar-search-suggestion',
                  cursor: 'active',
                  suggestion: 'suggestion d-flex justify-content-between px-3 py-2 w-100'
                }
              },
              // Clients
              {
                name: 'clients',
                display: 'name',
                limit: 5,
                source: filterConfig(searchData.clients),
                templates: {
                  header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Clients</h6>',
                  suggestion: function ({ name }) {
                    return '<a href="#"><div><span class="align-middle">' + name + '</span></div></a>';
                  },
                  notFound:
                    '<div class="not-found px-3 py-2">' +
                    '<h6 class="suggestions-header text-primary mb-2">Clients</h6>' +
                    '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                    '</div>'
                }
              },
              // Services
              {
                name: 'services',
                display: 'name',
                limit: 5,
                source: filterConfig(searchData.services),
                templates: {
                  header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Services</h6>',
                  suggestion: function ({ name }) {
                    return '<a href="#"><div><span class="align-middle">' + name + '</span></div></a>';
                  },
                  notFound:
                    '<div class="not-found px-3 py-2">' +
                    '<h6 class="suggestions-header text-primary mb-2">Services</h6>' +
                    '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                    '</div>'
                }
              },
              // Keywords
              {
                name: 'keywords',
                display: 'word',
                limit: 5,
                source: filterConfig(searchData.keywords),
                templates: {
                  header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Keywords</h6>',
                  suggestion: function ({ word }) {
                    return '<a href="#"><div><span class="align-middle">' + word + '</span></div></a>';
                  },
                  notFound:
                    '<div class="not-found px-3 py-2">' +
                    '<h6 class="suggestions-header text-primary mb-2">Keywords</h6>' +
                    '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                    '</div>'
                }
              },
              // Service Areas
              {
                name: 'serviceAreas',
                display: 'area_name',
                limit: 5,
                source: filterConfig(searchData.serviceAreas),
                templates: {
                  header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Service Areas</h6>',
                  suggestion: function ({ area_name }) {
                    return '<a href="#"><div><span class="align-middle">' + area_name + '</span></div></a>';
                  },
                  notFound:
                    '<div class="not-found px-3 py-2">' +
                    '<h6 class="suggestions-header text-primary mb-2">Service Areas</h6>' +
                    '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                    '</div>'
                }
              }
            )
            .bind('typeahead:render', function () {
              contentBackdrop.addClass('show').removeClass('fade');
            })
            .bind('typeahead:select', function (ev, suggestion) {
              if (suggestion.url) {
                window.location = suggestion.url;
              }
            })
            .bind('typeahead:close', function () {
              searchInput.val('');
              searchInputWrapper.addClass('d-none');
              contentBackdrop.addClass('fade').removeClass('show');
            });
          }
        });
      });

      // Init PerfectScrollbar in search result
      var psSearch;
      $('.navbar-search-suggestion').each(function () {
        psSearch = new PerfectScrollbar($(this)[0], {
          wheelPropagation: false,
          suppressScrollX: true
        });
      });

      searchInput.on('keyup', function () {
        psSearch.update();
      });
    }
  });
</script> --}}


    </script>
    <script>
          $(function () {
    // ! TODO: Required to load after DOM is ready, did this now with jQuery ready.
    window.Helpers.initSidebarToggle();
    // Toggle Universal Sidebar

    // Navbar Search with autosuggest (typeahead)
    // ? You can remove the following JS if you don't want to use search functionality.
    //----------------------------------------------------------------------------------

    var searchToggler = $('.search-toggler'),
      searchInputWrapper = $('.search-input-wrapper'),
      searchInput = $('.search-input'),
      contentBackdrop = $('.content-backdrop');

    // Open search input on click of search icon
    if (searchToggler.length) {
      searchToggler.on('click', function () {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      });
    }
    // Open search on 'CTRL+/'
    $(document).on('keydown', function (event) {
      let ctrlKey = event.ctrlKey,
        slashKey = event.which === 191;

      if (ctrlKey && slashKey) {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      }
    });
    // Note: Following code is required to update container class of typeahead dropdown width on focus of search input. setTimeout is required to allow time to initiate Typeahead UI.
    setTimeout(function () {
      var twitterTypeahead = $('.twitter-typeahead');
      searchInput.on('focus', function () {
        if (searchInputWrapper.hasClass('container-xxl')) {
          searchInputWrapper.find(twitterTypeahead).addClass('container-xxl');
          twitterTypeahead.removeClass('container-fluid');
        } else if (searchInputWrapper.hasClass('container-fluid')) {
          searchInputWrapper.find(twitterTypeahead).addClass('container-fluid');
          twitterTypeahead.removeClass('container-xxl');
        }
      });
    }, 10);

    if (searchInput.length) {
       var valuesearch = searchInput.val();
    //    alert(valuesearch);
      // Filter config
      var filterConfig = function (data) {
        return function findMatches(q, cb) {
          let matches;
          matches = [];
          data.filter(function (i) {
            if (i.name.toLowerCase().startsWith(q.toLowerCase())) {
              matches.push(i);
            } else if (
              !i.name.toLowerCase().startsWith(q.toLowerCase()) &&
              i.name.toLowerCase().includes(q.toLowerCase())
            ) {
              matches.push(i);
              matches.sort(function (a, b) {
                return b.name < a.name ? 1 : -1;
              });
            } else {
              return [];
            }
          });
          cb(matches);
        };
      };

      // Search JSON
      var searchJson = 'search-vertical.json'; // For vertical layout
      if ($('#layout-menu').hasClass('menu-horizontal')) {
        var searchJson = 'search-horizontal.json'; // For vertical layout
      }
      // Search API AJAX call
      var  searchData = $.ajax({
          url: "{{ route('front.global_search') }}",
          type: 'GET',
          data: { query: query },
      }).responseJSON;
      // Init typeahead on searchInput
      searchInput.each(function () {
        var $this = $(this);
        // alert($this);
        searchInput.typeahead(
              {
                hint: false,
                classNames: {
                  menu: 'tt-menu navbar-search-suggestion',
                  cursor: 'active',
                  suggestion: 'suggestion d-flex justify-content-between px-3 py-2 w-100'
                }
              },
              // Clients
              {
                name: 'clients',
                display: 'name',
                limit: 5,
                source: filterConfig(searchData.clients),
                templates: {
                  header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Clients</h6>',
                  suggestion: function ({ name }) {
                    return '<a href="#"><div><span class="align-middle">' + name + '</span></div></a>';
                  },
                  notFound:
                    '<div class="not-found px-3 py-2">' +
                    '<h6 class="suggestions-header text-primary mb-2">Clients</h6>' +
                    '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                    '</div>'
                }
              },
              // Services
              {
                name: 'services',
                display: 'name',
                limit: 5,
                source: filterConfig(searchData.services),
                templates: {
                  header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Services</h6>',
                  suggestion: function ({ name }) {
                    return '<a href="#"><div><span class="align-middle">' + name + '</span></div></a>';
                  },
                  notFound:
                    '<div class="not-found px-3 py-2">' +
                    '<h6 class="suggestions-header text-primary mb-2">Services</h6>' +
                    '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                    '</div>'
                }
              },
              // Keywords
              {
                name: 'keywords',
                display: 'word',
                limit: 5,
                source: filterConfig(searchData.keywords),
                templates: {
                  header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Keywords</h6>',
                  suggestion: function ({ word }) {
                    return '<a href="#"><div><span class="align-middle">' + word + '</span></div></a>';
                  },
                  notFound:
                    '<div class="not-found px-3 py-2">' +
                    '<h6 class="suggestions-header text-primary mb-2">Keywords</h6>' +
                    '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                    '</div>'
                }
              },
              // Service Areas
              {
                name: 'serviceAreas',
                display: 'area_name',
                limit: 5,
                source: filterConfig(searchData.serviceAreas),
                templates: {
                  header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Service Areas</h6>',
                  suggestion: function ({ area_name }) {
                    return '<a href="#"><div><span class="align-middle">' + area_name + '</span></div></a>';
                  },
                  notFound:
                    '<div class="not-found px-3 py-2">' +
                    '<h6 class="suggestions-header text-primary mb-2">Service Areas</h6>' +
                    '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                    '</div>'
                }
              }
            )

          //On typeahead result render.
          .bind('typeahead:render', function () {
            // Show content backdrop,
            contentBackdrop.addClass('show').removeClass('fade');
          })
          // On typeahead select
          .bind('typeahead:select', function (ev, suggestion) {
            // Open selected page
            if (suggestion.url) {
              window.location = suggestion.url;
            }
          })
          // On typeahead close
          .bind('typeahead:close', function () {
            // Clear search
            searchInput.val('');
            $this.typeahead('val', '');
            // Hide search input wrapper
            searchInputWrapper.addClass('d-none');
            // Fade content backdrop
            contentBackdrop.addClass('fade').removeClass('show');
          });

        // On searchInput keyup, Fade content backdrop if search input is blank
        searchInput.on('keyup', function () {
          if (searchInput.val() == '') {
            contentBackdrop.addClass('fade').removeClass('show');
          }
        });
      });

      // Init PerfectScrollbar in search result
      var psSearch;
      $('.navbar-search-suggestion').each(function () {
        psSearch = new PerfectScrollbar($(this)[0], {
          wheelPropagation: false,
          suppressScrollX: true
        });
      });

      searchInput.on('keyup', function () {
        psSearch.update();
      });
    }
  });
    </script>
  </body>
</html>
