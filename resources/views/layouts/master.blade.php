<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/base/vendor.bundle.base.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toast-alert/jquery.toast.min.css') }}">

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">

    <link rel="shortcut icon" href="{{@asset('images/pnoc.ico')}}" />

    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('customcss')
  </head>

   <body class="sidebar-fixed">
      
      <div class="container-scroller">
        @if (Request::segment(1) != 'login' && Request::segment(1) != 'register' && Request::segment(1) != ''  && Request::segment(2) != 'reset' && Request::segment(1) != 'email' && Request::segment(1) != 'documentation')
          @include('layouts.topnav')
          <div class="container-fluid page-body-wrapper">
          @include('layouts.sidebar')
          <div class="main-panel">
            @yield('content')
            @include('layouts.footer')
          </div>
        @else
          <div class="container-fluid page-body-wrapper full-page-wrapper">
          @yield('content')
        @endif
            
        </div>
      </div>
      <div id="server-current_date_container_key" data-server_time="{{Utility::get_server_datetime()}}">
      </div>
      <script src="{{ asset('js/vendors/base/vendor.bundle.base.js') }}"></script>
      <script src="{{ asset('js/vendors/datatables.net/jquery.dataTables.js') }}"></script>
      <script src="{{ asset('js/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
      <script src="{{ asset('js/off-canvas.js') }}"></script>
      <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
      <script src="{{ asset('js/template.js') }}"></script>
      <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
      <!-- Toast Alert -->
      <script src="{{ asset('plugins/toast-alert/jquery.toast.min.js') }}"></script>
      <script src="{{ asset('plugins/toast-alert/toastDemo.js') }}"></script>
      <script type="text/javascript">
        @if($message = Session::get('success'))
          showSuccessToast('{{$message}}');
        @endif
        @if($message = Session::get('error'))
          showDangerToast('{{$message}}');
        @endif
      </script>
    @yield('customjs')
    </body>
</html>