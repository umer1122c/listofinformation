<!DOCTYPE html>
<html lang="en">

<head>
	<title>Global Boomerang | {{$title}}</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	 <link rel="icon" type="image/png" href="{{asset('front/assets/images/favicon.ico')}}" />
	<link rel="stylesheet" type="text/css" href="{{asset('front/assets/css/bootstrap.min.css')}}">
	<link href="{{asset('front/assets/fonts/font-awesome.min.css')}}" rel="stylesheet" media="all">
	<link rel="stylesheet" type="text/css" href="{{asset('front/assets/css/style.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('front/error.css')}}">
        <script src="{{asset('front/assets/js/jquery.min.js')}}"></script>
        <script type="text/javascript">
            var APP_URL = {!! json_encode(url('/')) !!}
        </script>
</head>

    <body>
        <main>
            <input type="hidden" id="_token" value="{{ csrf_token() }}">
            <?php if(Session::has('logout_msg')) { ?>
                <div class="alert alert-block alert-success green-text">
                    <button type="button" class="close close-sm" data-dismiss="alert">
                        <i class="fa fa-times"></i>
                    </button>
                    <p class="mb-0">  {{ Session::get('logout_msg') }}  </p>
                </div>
            <?php } ?>
            <div class="alert alert-danger alert-dismissable show_message_error red-text" style=" display:none;z-index: 999999;">
                <button type="button" class="close close-sm" data-dismiss="alert">
                    <i class="fa fa-times"></i>
                </button>
                <p class="error_message mb-0">  </p>
            </div>
            <div class="alert alert-block alert-success show_message_success green-text" style=" display:none;z-index: 999999;">
                <button type="button" class="close close-sm" data-dismiss="alert">
                    <i class="fa fa-times"></i>
                </button>
                <p class="success_message mb-0">   </p>
            </div>
            @include('front.header')   
            <!--header end-->
            <!--main content start-->
            @yield('content')
            <!--main content end-->
            <!-- Footer START -->
            @include('front.footer')
            <!-- Footer END -->
        </main>
            
        <script src="{{asset('front/assets/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('front/assets/js/scripts.js')}}"></script>
        <script src="{{asset('front/assets/js/popper.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('front/js/common_script.js')}}"></script>
        <script>
            $(".alert-success").delay(3000).fadeOut();
            $(".alert-danger").delay(3000).fadeOut();
        </script> 
    </body>
</html>