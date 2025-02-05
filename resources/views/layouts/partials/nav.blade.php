


<nav
class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
id="layout-navbar">
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
  <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
    <i class="mdi mdi-menu mdi-24px"></i>
  </a>
</div>

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
  <!-- Search -->
  <div class="navbar-nav align-items-center">
    <div class="nav-item navbar-search-wrapper mb-0">
      <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">
        <i class="mdi mdi-magnify mdi-24px scaleX-n1-rtl"></i>
        <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
      </a>
    </div>
  </div>
  <!-- /Search -->

  <div class="navbar-search-wrapper search-input-wrapper d-none">
<span class="twitter-typeahead" style="position: relative; display: inline-block;">
    <input type="text" id="global_search"
            class="form-control search-input container-xxl border-0 tt-input" placeholder="Search..." aria-label="Search..."
            autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top;">
        <pre aria-hidden="true"
            style="position: absolute; visibility: hidden; white-space: pre; font-family: Inter, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Oxygen, Ubuntu, Cantarell, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 15px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre>
        <div class="tt-menu navbar-search-suggestion"
            style="position: absolute; top: 100%; left: 0px; z-index: 100; ">
            <div class="tt-dataset tt-dataset-users">

            </div>
            <div class="tt-dataset tt-dataset-roles"></div>
            <div class="tt-dataset tt-dataset-leads"></div>
            <div class="tt-dataset tt-dataset-invoice"></div>
        </div>
        {{-- <div class="tt-menu navbar-search-suggestion"
            style="position: absolute; top: 100%; left: 0px; z-index: 100; ">
            <div class="tt-dataset tt-dataset-users">
                <div class="not-found px-3 py-2">
                    <h6 class="suggestions-header text-primary mb-2">Users</h6>
                    <p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>
                </div>
            </div>
            <div class="tt-dataset tt-dataset-services">
                <div class="not-found px-3 py-2">
                    <h6 class="suggestions-header text-primary mb-2">Roles</h6>
                    <p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>
                </div>
            </div>
        </div> --}}
    </span>  </div>



  <ul class="navbar-nav flex-row align-items-center ms-auto">
    <!-- Language -->
    {{-- <li class="nav-item dropdown-language dropdown me-1 me-xl-0">
      <a
        class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
        href="javascript:void(0);"
        data-bs-toggle="dropdown">
        <i class="mdi mdi-translate mdi-24px"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-language="en" data-text-direction="ltr">
            <span class="align-middle">English</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-language="fr" data-text-direction="ltr">
            <span class="align-middle">French</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-language="ar" data-text-direction="rtl">
            <span class="align-middle">Arabic</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-language="de" data-text-direction="ltr">
            <span class="align-middle">German</span>
          </a>
        </li>
      </ul>
    </li> --}}
    <!--/ Language -->

    <!-- Style Switcher -->
    <li class="nav-item dropdown-style-switcher dropdown me-1 me-xl-0">
      <a
        class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
        href="javascript:void(0);"
        data-bs-toggle="dropdown">
        <i class="mdi mdi-24px"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
            <span class="align-middle"><i class="mdi mdi-weather-sunny me-2"></i>Light</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
            <span class="align-middle"><i class="mdi mdi-weather-night me-2"></i>Dark</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
            <span class="align-middle"><i class="mdi mdi-monitor me-2"></i>System</span>
          </a>
        </li>
      </ul>
    </li>
    <!-- / Style Switcher-->

    <!-- Quick links  -->
    {{-- <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-1 me-xl-0">
      <a
        class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
        href="javascript:void(0);"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-expanded="false">
        <i class="mdi mdi-view-grid-plus-outline mdi-24px"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end py-0">
        <div class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
            <a
              href="javascript:void(0)"
              class="dropdown-shortcuts-add text-muted"
              data-bs-toggle="tooltip"
              data-bs-placement="top"
              title="Add shortcuts"
              ><i class="mdi mdi-view-grid-plus-outline mdi-24px"></i
            ></a>
          </div>
        </div>
        <div class="dropdown-shortcuts-list scrollable-container">
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                <i class="mdi mdi-calendar fs-4"></i>
              </span>
              <a href="app-calendar.html" class="stretched-link">Calendar</a>
              <small class="text-muted mb-0">Appointments</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                <i class="mdi mdi-file-document-outline fs-4"></i>
              </span>
              <a href="app-invoice-list.html" class="stretched-link">Invoice App</a>
              <small class="text-muted mb-0">Manage Accounts</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                <i class="mdi mdi-account-outline fs-4"></i>
              </span>
              <a href="app-user-list.html" class="stretched-link">User App</a>
              <small class="text-muted mb-0">Manage Users</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                <i class="mdi mdi-shield-check-outline fs-4"></i>
              </span>
              <a href="app-access-roles.html" class="stretched-link">Role Management</a>
              <small class="text-muted mb-0">Permission</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                <i class="mdi mdi-chart-pie-outline fs-4"></i>
              </span>
              <a href="index.html" class="stretched-link">Dashboard</a>
              <small class="text-muted mb-0">Analytics</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                <i class="mdi mdi-cog-outline fs-4"></i>
              </span>
              <a href="pages-account-settings-account.html" class="stretched-link">Setting</a>
              <small class="text-muted mb-0">Account Settings</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                <i class="mdi mdi-help-circle-outline fs-4"></i>
              </span>
              <a href="pages-faq.html" class="stretched-link">FAQs</a>
              <small class="text-muted mb-0">FAQs & Articles</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                <i class="mdi mdi-dock-window fs-4"></i>
              </span>
              <a href="modal-examples.html" class="stretched-link">Modals</a>
              <small class="text-muted mb-0">Useful Popups</small>
            </div>
          </div>
        </div>
      </div>
    </li> --}}
    <!-- Quick links -->

    <!-- Notification -->
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
      <a
        class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
        href="javascript:void(0);"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-expanded="false">
        <i class="mdi mdi-bell-outline mdi-24px"></i>
        @if(isset(auth()->user()->unreadNotifications ) && count(auth()->user()->unreadNotifications) > 0)
            <span
            class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
        @endif
      </a>
      <ul class="dropdown-menu dropdown-menu-end py-0">
        <li class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">Notification</h6>
            <span class="badge rounded-pill bg-label-primary">{{ count(auth()->user()->unreadNotifications) }} New</span>
          </div>
        </li>

                    <li class="dropdown-notifications-list scrollable-container">
                        <ul class="list-group list-group-flush">
                            @foreach (auth()->user()->unreadNotifications as $notification)
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex gap-2">
                                    <div class="flex-shrink-0">
                                        <div class="avatar me-1">
                                            <img src="../../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                        {{-- <h6 class="mb-1 text-truncate">New Lead Added: <br>{{ $notification->data['lead_name'] }}
                                            🎉</h6> --}}
                                        <h6 class="mb-1 text-truncate">{{ $notification->data['title'] ?? 'New Notification' }} 🎉</h6>
                                        <small class="text-truncate text-body">Added by: {{
                                            \App\Models\User::find($notification->data['added_by'])->name ?? 'Unknown User' }}</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <small class="text-muted">{{ \App\Helpers\GlobalHelper::timeAgoShort($notification->created_at)
                                            }}</small>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </li>

        <li class="dropdown-menu-footer border-top p-2">
          <a href="javascript:void(0);" class="btn btn-primary d-flex justify-content-center">
            View all notifications
          </a>
        </li>
      </ul>
    </li>
    <!--/ Notification -->

    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="../../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="#">
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="../../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                </div>
              </div>
              <div class="flex-grow-1">
                @auth
                <span class="fw-medium d-block">{{ Auth::user()->name; }}</span>
                @endauth

                <small class="text-muted">Admin</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="#">
            <i class="mdi mdi-account-outline me-2"></i>
            <span class="align-middle">My Profile</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="#">
            <i class="mdi mdi-cog-outline me-2"></i>
            <span class="align-middle">Settings</span>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="#">
            <i class="mdi mdi-help-circle-outline me-2"></i>
            <span class="align-middle">FAQ</span>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <form  action="{{ route('logout') }}" method="POST">
            @csrf
        <li>
          <a class="dropdown-item" href="javascript:void(0)" onclick="document.querySelector('form').submit()">
            <i class="mdi mdi-logout me-2"></i>
            <span class="align-middle">Log Out</span>
          </a>
        </li>
    </form>
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>

<!-- Search Small Screens -->
{{-- <div class="navbar-search-wrapper search-input-wrapper d-none">
  <input
    type="text"
    class="form-control search-input container-xxl border-0"
    placeholder="Search..."
    aria-label="Search..." />
  <i class="mdi mdi-close search-toggler cursor-pointer"></i>
</div> --}}
</nav>
