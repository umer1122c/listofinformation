<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="{{asset('public/admins/images/favicon.png')}}">

    <title>Register</title>

    <!--Core CSS -->
    <link href="{{asset('public/admins/bs3/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/admins/css/bootstrap-reset.css')}}" rel="stylesheet">
    <link href="{{asset('public/admins/font-awesome/css/font-awesome.css')}}" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="{{asset('public/admins/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('public/admins/css/style-responsive.css')}}" rel="stylesheet" />

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]>
    <script src="js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <style>
	.form-signin {margin-top:50px;}
	</style>
</head>
  <body class="login-body">

    <div class="container">
@include('common.errors')
		<? if(Session::has('error_msg')) { ?>
            <div class="alert alert-block alert-success fade in" style="margin:10px auto; width:30%;">
            <button type="button" class="close close-sm" data-dismiss="alert">
                <i class="fa fa-times"></i>
            </button>
            
             <p style="text-align:center;">  {{ Session::get('error_msg') }}  </p>
            
            </div>
        <? } ?>
      <form class="form-signin" method="post" action="">
      {!! csrf_field() !!}
        <h2 class="form-signin-heading">registration now</h2>
        <div class="login-wrap">
            <p>Enter your personal details below</p>
            <input type="text" class="form-control" placeholder="Name" name="name" autofocus>
            <input type="text" class="form-control" placeholder="Email" name="email" autofocus>
            
            <input type="text" class="form-control" placeholder="Phone" name="phone" autofocus>
            <input type="text" class="form-control" placeholder="Address" name="address" autofocus>
            <input type="password" class="form-control" placeholder="Password" name="password">
            <input type="password" class="form-control" placeholder="Re-type Password" name="confirm_password">
            <!--<label class="checkbox">
                <input type="checkbox" value="agree this condition"> I agree to the Terms of Service and Privacy Policy
            </label>-->
            <button class="btn btn-lg btn-login btn-block" type="submit">Register</button>

            <div class="registration">
                Already Registered.
                <a class="" href="{{URL::to('/login')}}">
                    Login
                </a>
            </div>

        </div>

      </form>

    </div>


    <!-- Placed js at the end of the document so the pages load faster -->

    <!--Core js-->
    <script src="{{asset('public/admins/js/jquery.js')}}"></script>
    <script src="{{asset('public/admins/bs3/js/bootstrap.min.js')}}"></script>

  </body>
</html>