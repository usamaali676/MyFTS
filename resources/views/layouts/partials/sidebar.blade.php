<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span style="color: var(--bs-primary)">
                    <img style="height: 40px; width: 60px" src="{{ asset('assets/img/favicon/fts-logo.svg') }}" alt="">
                    {{-- <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                        y="0px" viewBox="0 0 132.17 103.02" style="enable-background:new 0 0 132.17 103.02;" xml:space="preserve">
                        <style type="text/css">
                            .st0 {
                                fill: #335FAB;
                            }

                            .st1 {
                                fill: #282A74;
                            }
                        </style>
                        <g>
                            <polygon class="st0" points="85.37,70.15 70.69,70.15 53.93,87.07 18.46,51.58 53.93,16.1 70.69,32.87 85.37,32.87 53.93,1.3
                                           3.64,51.58 53.93,101.73 	" />
                            <path class="st1" d="M120.43,43.48c-3.63,0-6.84,2.37-7.83,5.86H80.19l-9.09-9.09h21.8c1.26,4.32,5.73,6.71,10.05,5.58
                                           c4.32-1.26,6.71-5.73,5.58-10.05c-1.26-4.32-5.73-6.71-10.05-5.58c-2.65,0.84-4.75,2.8-5.58,5.58H66.78l-5.86-5.86v6.29
                                           l15.36,15.38L60.92,66.94v6.29l5.86-5.86h26.27c1.26,4.32,5.73,6.71,10.05,5.58c4.32-1.26,6.71-5.73,5.58-10.05
                                           c-1.26-4.32-5.73-6.71-10.05-5.58c-2.65,0.84-4.75,2.8-5.58,5.58h-21.8l9.09-9.09h32.28c1.26,4.32,5.73,6.71,10.05,5.58
                                           c4.32-1.26,6.71-5.73,5.58-10.05C127.14,45.85,124.06,43.48,120.43,43.48L120.43,43.48z M100.73,34.39c1.95,0,3.63,1.67,3.63,3.63
                                           s-1.67,3.63-3.63,3.63c-1.96,0-3.63-1.67-3.63-3.63l0,0C97.11,36.08,98.78,34.39,100.73,34.39z M100.73,61.36
                                           c1.95,0,3.63,1.67,3.63,3.63c0,1.95-1.67,3.63-3.63,3.63c-1.96,0-3.63-1.67-3.63-3.63l0,0C97.11,63.03,98.78,61.36,100.73,61.36
                                           L100.73,61.36L100.73,61.36z M120.43,55.22c-1.95,0-3.63-1.67-3.63-3.63s1.67-3.63,3.63-3.63c1.96,0,3.63,1.67,3.63,3.63l0,0
                                           C124.06,53.53,122.39,55.22,120.43,55.22z" />
                            <path class="st1"
                                d="M56.87,49.9v2.93h-9.64c-0.28,0-0.56,0-0.84,0c-0.15-0.56-0.56-1.11-1.26-1.11c-0.43,0-0.84-0.15-1.26-0.15
                                           c-0.56-0.7-0.98-1.54-0.98-2.37c0-0.15,0-0.28,0-0.43c0-0.56,0-1.11,0-1.67c0-0.15,0-0.28,0.28-0.28h2.65c0.15,0,0.28,0,0.28,0.28
                                           s0,0.7,0,0.98c0,0.28,0,0.43,0.15,0.7c0.28,0.7,0.98,1.26,1.67,1.11h0.98L56.87,49.9L56.87,49.9z" />
                            <path class="st0" d="M55.06,40.83h7.96c0.28,0,0.43,0,0.43,0.43c0,1.54,0,3.21,0,4.75c0,0.28,0,0.28-0.28,0.28
                                           c-0.7,0-1.39,0-2.24,0c-0.28,0-0.43,0-0.43-0.43c0-0.84,0-1.54,0-2.37c0-0.28,0-0.43-0.43-0.43c-1.67,0-3.36,0-5.03,0
                                           c-0.28,0-0.43,0.15-0.43,0.43c0,1.82,0,3.63,0,5.45c0,0.28-0.15,0.43-0.43,0.43c-0.7,0-1.39,0-2.24,0c-0.28,0-0.43-0.15-0.43-0.43
                                           c0-1.82,0-3.63,0-5.45c0-0.28-0.15-0.43-0.43-0.43c-1.67,0-3.36,0-5.03,0c-0.28,0-0.43,0.15-0.43,0.43c0,0.84,0,1.67,0,2.37
                                           c0,0.28,0,0.28-0.28,0.28h-2.37c-0.15,0-0.28,0-0.28-0.15c-0.15-1.11,0-2.1,0.43-3.21c0.56-1.26,1.67-2.1,3.08-2.24
                                           c2.65-0.15,5.31,0,7.96,0L55.06,40.83z" />
                            <path class="st0" d="M53.24,62.34h-2.93c-0.28,0-0.43,0-0.43-0.28c0-0.56,0-1.26,0-1.82c0-0.28,0.15-0.28,0.28-0.28s0.7,0,1.11,0
                                           s0.43,0,0.43-0.43c0-1.95,0-3.77,0-5.73c0-0.28,0-0.43,0.43-0.43c0.7,0,1.39,0,2.24,0c0.28,0,0.43,0,0.43,0.43
                                           c0,1.95,0,3.77,0,5.73c0,0.28,0,0.43,0.43,0.43s0.7,0,1.11,0s0.28,0,0.28,0.28c0,0.56,0,1.26,0,1.82c0,0.28-0.15,0.28-0.28,0.28
                                           H53.24z" />
                            <path class="st1"
                                d="M49.6,57.31c0,1.82-1.54,3.36-3.36,3.49c-0.98,0.15-1.95,0.15-2.93,0.15c-0.15,0-0.28,0-0.28-0.28
                                           c0-0.7,0-1.39,0-1.95c0-0.15,0-0.28,0.28-0.28c0.56,0,1.11,0,1.67,0c0.84,0,1.39-0.56,1.54-1.39c0.15-1.39,0.15-2.8,0-4.19
                                           c0,0,0,0,0-0.15c-0.15-0.56-0.56-1.11-1.26-1.11c-0.43,0-0.84-0.15-1.26-0.15h-0.98v-2.93l0,0h2.8h0.28
                                           c0.28,0.7,0.98,1.26,1.67,1.11h0.98c0.56,0.7,0.84,1.67,0.84,2.52c0,0.15,0,0.28,0,0.43C49.75,54.37,49.75,55.78,49.6,57.31z" />
                            <polygon class="st1" points="47.36,36.21 32.15,51.58 47.36,66.81 47.36,73.23 25.71,51.58 47.36,29.92 	" />
                        </g>
                    </svg> --}}
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">MY FTS</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.4854 4.88844C11.0081 4.41121 10.2344 4.41121 9.75715 4.88844L4.51028 10.1353C4.03297 10.6126 4.03297 11.3865 4.51028 11.8638L9.75715 17.1107C10.2344 17.5879 11.0081 17.5879 11.4854 17.1107C11.9626 16.6334 11.9626 15.8597 11.4854 15.3824L7.96672 11.8638C7.48942 11.3865 7.48942 10.6126 7.96672 10.1353L11.4854 6.61667C11.9626 6.13943 11.9626 5.36568 11.4854 4.88844Z"
                    fill="currentColor" fill-opacity="0.6" />
                <path
                    d="M15.8683 4.88844L10.6214 10.1353C10.1441 10.6126 10.1441 11.3865 10.6214 11.8638L15.8683 17.1107C16.3455 17.5879 17.1192 17.5879 17.5965 17.1107C18.0737 16.6334 18.0737 15.8597 17.5965 15.3824L14.0778 11.8638C13.6005 11.3865 13.6005 10.6126 14.0778 10.1353L17.5965 6.61667C18.0737 6.13943 18.0737 5.36568 17.5965 4.88844C17.1192 4.41121 16.3455 4.41121 15.8683 4.88844Z"
                    fill="currentColor" fill-opacity="0.38" />
            </svg>
        </a>
        @php
            $user = Auth::user();
        @endphp
    </div>
    <span class="badge rounded-pill bg-primary" style="text-align: center; width: fit-content; margin: auto; padding: 7px 10px">{{ $user->name }}</span>


    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item active ">
            <a href="{{ route('home') }}" class="menu-link ">
                <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                <div data-i18n="Dashboards">Dashboards</div>
                {{-- <div class="badge bg-primary rounded-pill ms-auto">Main</div> --}}
            </a>
        </li>



        <!-- Apps & Pages -->
        <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text" data-i18n="Apps & Pages">Apps &amp; Pages</span>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-shield-outline"></i>
                <div data-i18n="Roles & Permissions">Roles & Permissions</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('role.index') }}" class="menu-link">
                        <div data-i18n="Roles">Roles</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="app-access-permission.html" class="menu-link">
                        <div data-i18n="Permission">Permission</div>
                    </a>
                </li> --}}
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-table"></i>
                <div data-i18n="Leads">Leads</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('lead.index') }}" class="menu-link">
                        <div data-i18n="View">View</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('lead.create') }}" class="menu-link">
                        <div data-i18n="Create">Create</div>
                    </a>
                </li>
            </ul>
        </li>




    </ul>
</aside>
