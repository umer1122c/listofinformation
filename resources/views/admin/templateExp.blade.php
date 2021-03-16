<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Global Boomerang | {{$title}}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('front/assets/images/favicon-32x32.png')}}">

    <!-- plugins css -->
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/bootstrap/dist/css/bootstrap.css')}}" />
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/PACE/themes/blue/pace-theme-minimal.css')}}" />
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/perfect-scrollbar/css/perfect-scrollbar.min.css')}}" />

    <!-- page plugins css -->
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/bower-jvectormap/jquery-jvectormap-1.2.2.css')}}" />
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/nvd3/build/nv.d3.min.css')}}" />
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/datatables/media/css/jquery.dataTables.css')}}" />

    <!-- core css -->
    <link href="{{asset('admins/assets/css/ei-icon.css')}}" rel="stylesheet">
    <link href="{{asset('admins/assets/css/themify-icons.css')}}" rel="stylesheet">
    <link href="{{asset('admins/assets/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('admins/assets/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('admins/assets/css/app.css')}}" rel="stylesheet">
    <link href="{{asset('admins/assets/css/dropzone.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/selectize/dist/css/selectize.default.css')}}" />
    <script src="{{asset('admins/assets/vendors/jquery/dist/jquery.min.js')}}"></script>
    <script type="text/javascript">
        var APP_URL = {!! json_encode(url('/')) !!}
    </script>
</head>
<body>
    <div class="app">
        <div class="layout">
            <!--sidebar start-->
                @include('admin.side_menu')
            <!--sidebar end-->
            <!--header start-->
            <div class="page-container">
                @include('admin.header')   
                <!--header end-->
                <!--main content start-->
                @yield('content')
                <!--main content end-->
                <!-- Footer START -->
                @include('admin.footer')
                <!-- Footer END -->
            </div>
            <!-- Page Container END -->
        </div>
    </div>
    <!-- build:js assets/js/vendor.js -->
    <!-- plugins js -->
    
    <script src="{{asset('admins/assets/vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('admins/assets/vendors/bootstrap/dist/js/bootstrap.js')}}"></script>
    <script src="{{asset('admins/assets/vendors/PACE/pace.min.js')}}"></script>
    <script src="{{asset('admins/assets/vendors/perfect-scrollbar/js/perfect-scrollbar.jquery.js')}}"></script>
    <!-- endbuild -->
    <!-- page plugins js -->
    <script src="{{asset('admins/assets/vendors/bower-jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
    <script src="{{asset('admins/assets/vendors/jquery.sparkline/index.js')}}"></script>
    <!-- build:js assets/js/app.min.js -->
    <!-- core js -->
    <script src="{{asset('admins/assets/js/app.js')}}"></script>
    <!-- page plugins js -->
    <script src="{{asset('admins/assets/vendors/datatables/media/js/jquery.dataTables.js')}}"></script>
    <!-- endbuild -->
    <!-- page js -->
    <script src="{{asset('admins/assets/js/table/data-table.js')}}"></script>
    <script src="{{asset('admins/assets/js/common_script.js')}}"></script>
    <!--<script src="{{asset('admins/assets/js/dashboard/dashboard.js')}}"></script>-->
    <script src="{{asset('admins/assets/vendors/jquery-validation/dist/jquery.validate.min.js')}}"></script>
    <!-- page js -->
    <script src="{{asset('admins/assets/js/forms/form-validation.js')}}"></script>
    <script src="{{asset('admins/assets/vendors/selectize/dist/js/standalone/selectize.min.js')}}"></script>
    <script src="{{asset('admins/assets/js/forms/form-elements.js')}}"></script>
    <script src="{{asset('admins/assets/js/dropzone.js')}}"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script>
        $(".alert-success").delay(3000).fadeOut();
        $(".alert-danger").delay(3000).fadeOut();
        $(document).ready(function() {
            $('#example').DataTable( {
                dom: 'Bfrtip',
                "targets": 'no-sort',
                "bSort": false,
                "order": [],
                "paging": false,
                "bPaginate": false,
                "bInfo": false,
                "bFilter": false,
                buttons: [
                    //'copy', 'csv', 'excel', 'pdf', 'print'
                    'csv','excel'
                ]
            } );
            
        } );
        
        
    </script>
</body>
</html>