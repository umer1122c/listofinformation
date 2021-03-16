<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Pricepally | {{$title}}</title>
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

  <body class="login-body">

    <div class="container">
        @include('common.errors')
		<? if(Session::has('change_success_msg')) { ?>
            <div class="alert alert-danger alert-dismissable" style="margin:10px auto; width:30%;">
            <button type="button" class="close close-sm" data-dismiss="alert">
                <i class="fa fa-times"></i>
            </button>
            
             <p style="text-align:center;">  {{ Session::get('change_success_msg') }}  </p>
            
            </div>
        <? } ?>
      <form class="form-signin" action="{{URL::to('admins/reset_password/'.$email)}}" method="post">
      {!! csrf_field() !!}
        <h2 class="form-signin-heading">Reset Password</h2>
        
        <div class="login-wrap">
            <div class="user-login-info">
                <input type="password" class="form-control" placeholder="New Password" name="new_password" autofocus>
                <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" >
            </div>
            
            <button class="btn btn-lg btn-login btn-block" type="submit">Reset Password</button>
        </div>

      </form>

    </div>



    <!-- Placed js at the end of the document so the pages load faster -->

    <!--Core js-->
    <script src="{{asset('admins/js/jquery.js')}}"></script>
    <script src="{{asset('admins/bs3/js/bootstrap.min.js')}}"></script>

  </body>
</html>
