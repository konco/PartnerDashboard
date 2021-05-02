<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{ env('APP_NAME') }} | {{ $title }}</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ URL::asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/vendors/css/vendor.bundle.base.css') }}">
  <!-- endinject -->
  {{-- @yield('css') --}}
  {{-- <link rel="stylesheet" href="{{ URL::asset('assets/vendors/quill/quill.snow.css') }}"> --}}
  {{-- <link rel="stylesheet" href="{{ URL::asset('assets/vendors/simplemde/simplemde.min.css') }}"> --}}
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="{{ URL::asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/css/bulgad.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/vendors/jquery-toast-plugin/jquery.toast.min.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
  @yield('css')
  <!-- endinject -->
  <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo-icon.png') }}?v={{ File::lastModified('assets/images/logo-icon.png')}}" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style type="text/css">
    .datatablelist thead tr {
      background-color: #aeb1b7;
    }
    .button-top{margin-bottom: 10px;}
    .modal-lgs {max-width: 45%;}
    .modal-lgss {max-width: 75%;}
  </style>
</head>
<body>
  <div class="container-scroller">
    @include('partials.top-navbar')

    <div class="container-fluid page-body-wrapper">
      @include('partials.left-sidebar')
      <div class="main-panel">
        @yield('content')
        @include('partials.footer')
      </div>      
    </div>
  </div>



<!-- plugins:js -->
  <script src="{{ URL::asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <script src="{{ URL::asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
  <script src="{{ URL::asset('assets/vendors/datatables.net/jquery.dataTables.js') }}"></script>
  <script src="{{ URL::asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="{{ URL::asset('assets/js/off-canvas.js') }}"></script>
  <script src="{{ URL::asset('assets/js/hoverable-collapse.js') }}"></script>
  <script src="{{ URL::asset('assets/js/template.js') }}"></script>
  <script src="{{ URL::asset('assets/js/settings.js') }}"></script>
  
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{ URL::asset('assets/js/dashboard.js') }}"></script>
  <!-- End custom js for this page-->
  <script src="{{ URL::asset('assets/vendors/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
  <script src="{{ URL::asset('assets/js/toastDemo.js') }}"></script>
  <script src="{{ URL::asset('assets/js/custom.js') }}"></script>

  @yield('js')

</body>

</html>