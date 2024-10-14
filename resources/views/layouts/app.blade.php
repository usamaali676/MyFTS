<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template">
  <head>
  @include('layouts.partials.head')
  </head>
  <body>
    <!-- Content -->

    @yield('content')

    <!-- / Content -->

    @include('layouts.partials.scripts')
  </body>
</html>
