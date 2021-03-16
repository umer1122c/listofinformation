<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Global Boomerang | {{$title}}</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <!-- plugins css -->
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/bootstrap/dist/css/bootstrap.css')}}" />
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/PACE/themes/blue/pace-theme-minimal.css')}}" />
    <link rel="stylesheet" href="{{asset('admins/assets/vendors/perfect-scrollbar/css/perfect-scrollbar.min.css')}}" />
    <!-- core css -->
    <link href="{{asset('admins/assets/css/ei-icon.css')}}" rel="stylesheet">
    <link href="{{asset('admins/assets/css/themify-icons.css')}}" rel="stylesheet">
    <link href="{{asset('admins/assets/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('admins/assets/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('admins/assets/css/app.css')}}" rel="stylesheet">
    <style>
        p{
            margin-bottom: 5px !important;
        }
    </style>
</head>
<body>
    <div class="app">
        <div class="authentication">
            <div class="sign-in-2">
                <div class="container-fluid no-pdd-horizon bg" style="background-image: url('admins/assets/images/others/img-30.png')">
                    <div class="row">
                        <div class="col-md-10 mr-auto ml-auto">
                            <div class="row">
                                <div class="mr-auto ml-auto full-height height-100 d-flex align-items-center">
                                    <div class="vertical-align full-height">
                                        <div class="table-cell">
                                            <div class="card">
                                                <div class="card-body">
                                                    @include('common.errors')
                                                    <?php if(Session::has('error_msg')) { ?>
                                                        <div class="alert alert-block alert-success fade in" style="margin:10px auto; width:100%;">
                                                            <button type="button" class="close close-sm" data-dismiss="alert">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                            <p style="text-align:center;">  {{ Session::get('error_msg') }}  </p>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="pdd-horizon-30 pdd-vertical-30">
                                                        <div class="mrg-btm-30">
                                                            <h1 class=" text-center"><img class="img-responsive" src="{{asset('admins/assets/images/logo/logo.png')}}" alt=""></h1>
                                                            <h2 class="no-mrg-vertical pdd-top-15 text-center">Login</h2>
                                                        </div>
                                                        <p class="mrg-btm-15 font-size-13">Please enter your user name and password to login</p>
                                                        <form class="form-signin" action="" method="post">
                                                            {!! csrf_field() !!}
                                                            <div class="form-group">
                                                                <input type="email" name="email" class="form-control" placeholder="User name" value="<?php if(isset($_COOKIE["username"])) { echo $_COOKIE["username"]; } ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="password" name="password" class="form-control" placeholder="Password" value="<?php if(isset($_COOKIE["password"])) { echo $_COOKIE["password"]; } ?>">
                                                            </div>
                                                            <div class="checkbox font-size-13 inline-block no-mrg-vertical no-pdd-vertical">
                                                                <input id="agreement" name="agreement" type="checkbox" <?php if(isset($_COOKIE["username"])) {  ?> checked=""  <?php } ?>>
                                                                <label for="agreement">Keep Me Signed In</label>
                                                            </div>
                                                            <div class="pull-right">
                                                                <a href="{{URL::to('admin/forget/password')}}">Forgot Password?</a>
                                                            </div>
                                                            <div class="mrg-top-20 text-right">
                                                                <button type="submit" class="btn btn-info">Login</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- build:js assets/js/vendor.js -->
    <!-- plugins js -->
    <script src="{{asset('admins/assets/vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('admins/assets/vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('admins/assets/vendors/bootstrap/dist/js/bootstrap.js')}}"></script>
    <script src="{{asset('admins/assets/vendors/PACE/pace.min.js')}}"></script>
    <script src="{{asset('admins/assets/vendors/perfect-scrollbar/js/perfect-scrollbar.jquery.js')}}"></script>
    <!-- endbuild -->
    <!-- build:js assets/js/app.min.js -->
    <!-- core js -->
    <script src="{{asset('admins/assets/js/app.js')}}"></script>
    <!-- endbuild -->
    <!-- page js -->
</body>
</html>