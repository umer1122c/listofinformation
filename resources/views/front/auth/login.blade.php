@extends('front.template')
@section('content')

<input type="hidden" id="_token" value="{{csrf_token()}}">
<div class="alert alert-danger alert-dismissable show_alert_error red-text" style=" display:none">
    <button type="button" class="close close-sm" data-dismiss="alert">
        <i class="fa fa-times"></i>
    </button>
    <p class="error-message mb-0">Email or password is invalid! </p>
</div>
<div class="alert alert-block alert-success show_alert_success_login green-text" style=" display:none">
    <button type="button" class="close close-sm" data-dismiss="alert">
        <i class="fa fa-times"></i>
    </button>
    <p class="mb-0">  Login Successfully! </p>
</div>
<div class="alert alert-block alert-success show_alert_success_f green-text" style=" display:none">
    <button type="button" class="close close-sm" data-dismiss="alert">
        <i class="fa fa-times"></i>
    </button>
    <p class="success_message mb-0">  Please check your email to reset password. </p>
</div>
<!------ SIGNUP-SECTION-START ------>
    <div class="dash-profile">
        <h3 class="text-center text-white signup-text">Welcome To Hush'D</h3>
    </div>
    <section class="signup-wrapper login-bg">
        <div class="container">
            <div class="dash-inner">
                <div class="row">
                    <div class="col-md-12">
<!--                        <ul class="list-unstyled text-center m-auto ">
                            <li class="d-inline-block">
                                <a href="{{URL::to('redirect/facebook')}}" class="btn btn-social btn-facebook">
                                    <span><i class="fa fa-facebook fa-fw"></i> </span>
                                    <span>Sign in with Facebook</span>
                                </a>
                            </li>
                            <li class="d-inline-block">
                                <a href="{{URL::to('redirect/google')}}" class="btn  btn-social btn-google">
                                    <span><i class="fa fa-google-plus" aria-hidden="true"></i>
                                       </span> 
                                    <span>Sign in with Google</span>
                                </a>
                            </li>
                        </ul>-->
                        <h3 class="text-center pink-clr font-weight-bold mb-4">Login</h3>
                        <form class="contact-form">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" name="redirect_url" id="redirect_url" class="form-control press-btn" value="{{$redirect_url}}" placeholder="">
                                        <input  class="form-control press-btn" name="email" id="email" placeholder="Email Address">
                                      </div>
                                </div> 

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input class="form-control press-btn"  type="password" name="password" id="password" placeholder="Password">
                                        <p class="pink-clr text-right pt-2 forget-phr" data-toggle="modal" data-target="#forgetpassword">Forgot Password</p>
                                      </div>
                                </div> 

                            </div>
                            <div class="text-center">
                                <button type="button" class="btn profile-btn" onclick="userLogin()">Login</button>
                                <img src="{{asset('front/loader.gif')}}" alt="" class="img-fluid loaders" style="display:none;width: 50px;"/>
                                <p>Don't have an account ?  <a href="{{ url('/signup?redirect_url='.$redirect_url) }}" class="pink-clr">Signup</a></p>
                            </div>
                          </form>
                    </div>

                </div>

            </div>
        </div>
    </section>
    <!------ SINGUP-SECTION-END ------>
<!---- FORGOT-PASSWORD-START ----->
    <div class="modal fade forget-wrapper" id="forgetpassword">
        <div class="modal-dialog">
            <div class="modal-content">
              <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Forget Password</h4>
                    <span type="button" class="close" data-dismiss="modal">&times;</span>
                </div>
                <!-- Modal body -->
                <div class="modal-body forget-inner">
                    <form class="contact-form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text"  class="form-control"  name="f_email" id="f_email" placeholder="Email Address">
                                </div>
                            </div> 
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn profile-btn" onclick="forgotPassword();">Submit</button>
                            <img src="{{asset('front/loader.gif')}}" alt="" class="img-fluid loaders2" style="display:none;width: 50px;"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<!---- FORGOT-PASSWORD-END ----->
<script>
    $(".press-btn").keypress(function(e) {
        if(e.which == 13) {
            userLogin();
        }
    });
    $("input[type='radio']").click(function(){
        var radioValue = $("input[name='user_type']:checked").val();
        if(radioValue == 2){
            $('.business_field').show();
        }else{
            $('.business_field').hide();
        }
    });
    
    function userLogin() {
        var _token = $('input#_token').val();
        var email   = $("#email").val();
        var password = $("#password").val();
        var login_type_value = $("#login_type_value").val();
        var login_id_value = $("#login_id_value").val();
        var slug = $("#slug").val();
        var redirect_url = $("#redirect_url").val();
        // append data
        var data = new FormData();
        data.append('_token',_token);
        data.append('email',email);
        data.append('password',password);
        data.append('login_type_value',login_type_value);
        var count = 0;
        if (email !=""){
            $("#email").css({"border-color": "transparent", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#email").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if (password !=""){
            $("#password").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#password").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if (count == 0){
            $('.loaders').show();
            $.ajax({
                type: 'POST',
                url: APP_URL+'/login',
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                data: data,
                processData: false,
                contentType: false,
                // async:false,
                success: function (msg) {
                    console.log(msg);
                    $('.loaders').hide();
                    if(msg.status === 'success'){
                        $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
                        $('.show_alert_success_login').show();
                        $(".alert-success").delay(2000).fadeOut();
                        setTimeout(function() {
                            window.location.href=redirect_url;
                        }, 1000);
                    }else{
                        $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
                        $('.show_alert_error').show();
                        $(".alert-danger").delay(1000).fadeOut();
                    }
                }
            });
        }
    }
    
    function forgotPassword() {
        var _token = $('input#_token').val();
        var email   = $("#f_email").val();
        // append data
        var data = new FormData();
        data.append('_token',_token);
        data.append('email',email);
        var count = 0;
        if (email !=""){
            $("#f_email").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#f_email").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if (count == 0){
            $('.loaders2').show();
            $.ajax({
                type: 'POST',
                url: APP_URL+'/forgetPassword',
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                data: data,
                processData: false,
                contentType: false,
                // async:false,
                success: function (msg) {
                    $('.loaders2').hide();
                    
                    if(msg.status === 'success'){
                        $('#forgetpassword').modal('hide');
                        $('.success_message').html(msg.message);
                        $('.show_alert_success_f').show();
                        $(".alert-success").delay(3000).fadeOut();
                        $('#pallyId').modal('hide');
                        $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
                        $("#f_email").val('');
                        //setTimeout(function() {
                            //window.location.href=APP_URL+"/web";
                        //}, 3000);
                    }else if(msg.status === 'notexist'){
                        $('.error-message').html(msg.message);
                        $('.show_alert_error').show();
                        $(".alert-danger").delay(3000).fadeOut();
                    }else{
                        $('.error-message').html(msg.message);
                        $('.show_alert_error').show();
                        $(".alert-danger").delay(3000).fadeOut();
                    }
                }
            }); 
        }
    }
</script>
@endsection 
 