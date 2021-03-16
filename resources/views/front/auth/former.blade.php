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

<!---- RESET-PASSWORD-SECTION-START ----->
<div class="heading-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-12 text-center">
                <div class="main-heading">
                    <h3 class="font-weight-bold mb-0 text-uppercase">Farm supply registration</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="signup-wrapper login-bg registration-bg">
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col-12 col-md-8 col-lg-8">
                <form class="form-wrapper" id="former-signup">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Your Name</label>
                                <input type="text" class="form-control press-btn" name="name" id="name" placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <ul class="list-unstyled phone-list">
                                    <li class="d-inline-block"> 
                                        <select name="country_code" id="country_code">
                                                <option value="+234">+234</option>
                                        </select>
                                    </li>
                                    <li class="d-inline-block"><input type="text" class="form-control press-btn" name="phone_number" id="phone_number" placeholder="e.g(123456789)"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control press-btn" name="email" id="email" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" class="form-control" name="location" id="location" placeholder="Location">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Location of farm</label>
                                <input type="text" class="form-control press-btn" name="form_location" id="form_location" placeholder="Location of farm">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group quantity-shadow">
                                <label>What do you produce mainly on your farm and in which quantity per month</label>
                                <textarea class="form-control press-btn" rows="5" name="form_information" id="form_information" placeholder="What do you produce mainly on your farm and in which quantity per month"></textarea>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Do you have a greenhouse on your farm site</label><br>
                                <input type="radio" id="test1" name="is_greenhouse" value="Yes" checked>
                                <label for="test1">Yes</label>
                                <input type="radio" id="test2"  role="button" value="No" aria-expanded="false"  name="is_greenhouse">
                                <label for="test2">No</label>
                            </div>
                        </div>
                        <div class="btn-bg text-center m-auto">
                            <button type="button" class="yellow-bg signupFormer" onclick="signupFormer()">Submit</button>
                            <img src="{{asset('front/loader.gif')}}" alt="" class="img-fluid loaders" style="display:none;width: 50px;"/>
                        </div>
                    </div>


                </form>
            </div>
        </div>
    </div>
</section>
<!---- RESET-PASSWORD-SECTION-END----->
<script>
    $(".press-btn").keypress(function(e) {
        if(e.which == 13) {
            signupFormer();
        }
    });
    
    function signupFormer(){
        var _token = $('input#_token').val();
        var name = $("#name").val();
        var country_code   = $("#country_code").val();
        var phone_number = $("#phone_number").val();
        var email = $("#email").val();
        var location   = $("#location").val();
        var form_location   = $("#form_location").val();
        var form_information   = $("#form_information").val();
        
        var is_greenhouse = $('input[name=is_greenhouse]:checked').val();
        
        var count = 0;
        if (name !=""){
            $("#name").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#name").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
             $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
            count++;
        }
        if (phone_number !=""){
            $(".phone-list").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $(".phone-list").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
            count++;
        }

        if (email !=""){
            $("#email").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else{
            $("#email").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
            count++;
        }
        if(location !=""){
            $("#location").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#location").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
            count++;
        }
        if(form_location !=""){
            $("#form_location").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#form_location").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
            count++;
        }
        
        if(form_information !=""){
            $("#form_information").css({"border-color": "#9a9a9a", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#form_information").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
            count++;
        }

        if(count == 0){
            $('.loaders').show();
            var data = new FormData();
            data.append('_token',_token);
            data.append('name',name);
            data.append('country_code',country_code);
            data.append('phone_number',phone_number);
            data.append('form_location',form_location);
            data.append('email',email);
            data.append('form_information',form_information);
            data.append('location',location);
            data.append('is_greenhouse',is_greenhouse);
            $.ajax({
                type: 'POST',
                url: APP_URL+'/formers/signup',
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
                            window.location.href=APP_URL;
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
    }
</script>
@endsection 
