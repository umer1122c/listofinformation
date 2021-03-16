@extends('front.template')
@section('content')
<input type="hidden" id="_token" value="{{csrf_token()}}">

<div class="alert alert-danger alert-dismissable show_message_error red-text" style=" display:none">
    <button type="button" class="close close-sm" data-dismiss="alert">
        <i class="fa fa-times"></i>
    </button>
    <p class="error_message mb-0">  </p>
</div>
<div class="alert alert-block alert-success show_message_success green-text" style=" display:none">
    <button type="button" class="close close-sm" data-dismiss="alert">
        <i class="fa fa-times"></i>
    </button>
    <p class="success_message mb-0">   </p>
</div>
<img src="{{asset('front/images/loader.gif')}}" alt="" class="img-fluid loaders" style="display:none;width: 50px;"/>

<!---- SIGNUP-CSS-START ----->
    <div class="dash-profile">
        <h3 class="text-center text-white signup-text">Welcome To Hush'D</h3>
    </div>
    <section class="signup-wrapper">
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
                        <h3 class="text-center pink-clr font-weight-bold mb-4">Create an Account</h3>
                        <form class="contact-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="hidden" name="redirect_url" id="redirect_url" class="form-control press-btn" value="{{$redirect_url}}" placeholder="">
                                        <input  class="form-control"  name="first_name" id="first_name" value="" placeholder="First Name">
                                      </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input  class="form-control" name="last_name" id="last_name" value="" placeholder="Last Name">
                                      </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input  class="form-control" name="r_email" id="r_email" value="" placeholder="Email Address">
                                      </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input  class="form-control" id="phone" maxlength="10" name="phone" placeholder="Phone Number">
                                      </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="password" class="form-control" name="r_password" id="r_password" value="" placeholder="Password">
                                      </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" value="" class="form-control" placeholder="Confirm Password">
                                        <input type="hidden" class="form-control" name="referral_code" id="referral_code" value="" placeholder="">
                                      </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn profile-btn signupUser">Signup</button>
                                <img src="{{asset('front/loader.gif')}}" alt="" class="img-fluid loaders" style="display:none;width: 50px;"/>
                                <p>Already have an account ? <a href="{{ url('/login') }}" class="pink-clr">Login</a></p>
                            </div>
                          </form>
                    </div>

                </div>

            </div>
        </div>
    </section>
<!---- SIGNUP-CSS-END ----->
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
    
    $(".signupUser").click(function(){
        var _token = $('input#_token').val();
        var first_name = $("#first_name").val();
        var last_name = $("#last_name").val();
        //var referral_code = $("#referral_code").val();
        
        var email   = $("#r_email").val();
        var phone   = $("#phone").val();
        var phoneLength = $("#phone").val().length; 
        var password = $("#r_password").val();
        var confirm_password = $("#confirm_password").val();
        var redirect_url = $("#redirect_url").val();
        
        var count = 0;
        if (first_name !=""){
            $("#first_name").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#first_name").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if (last_name !=""){
            $("#last_name").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#last_name").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }

        if (email !=""){
            $("#r_email").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else{
            $("#r_email").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if (phone !="" && phoneLength == 10){
            $("#phone").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else{
            $("#phone").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if(password !=""){
            $("#r_password").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#r_password").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if(confirm_password !=""){
            $("#confirm_password").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#confirm_password").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if(password == confirm_password){
            $("#confirm_password").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#r_password").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            $("#confirm_password").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }

        if(count == 0){
            $('.loaders').show();
            var data = new FormData();
            data.append('_token',_token);
            data.append('first_name',first_name);
            data.append('last_name',last_name);
            data.append('email',email);
            data.append('phone',phone);
            data.append('password',password);
            //data.append('referral_code',referral_code);
            $.ajax({
                type: 'POST',
                url: APP_URL+'/checkEmail',
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                data: data,
                processData: false,
                contentType: false,
                // async:false,
                success: function (msg) {
                    $('.loaders').hide();
                    if(msg.status == 'success'){
                        $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
                        $('.success_message').html(msg.message);
                        $('.show_message_success').show();
                        $(".alert-success").delay(3000).fadeOut();
                        setTimeout(function() {
                            window.location.href=redirect_url;
                        }, 1000);
                    }else if(msg.status == 'failed'){
                        $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
                        $('.error_message').html(msg.message);
                        $('.show_message_error').show();
                        $(".alert-danger").delay(3000).fadeOut();
                    }
                }
            });

        }
    });
</script>
@endsection 
