    var usersArray = [];
    var servicePrice = 0;
    var CartID = 0;
    function PreviewImage() {
        var ext = $('#img-upload').val().split('.').pop().toLowerCase();
        if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
                $('#img-upload').val('');
                $('#upload_logo_error').html('gif , png , jpg , jpeg are allowed.');
        } else {
            $('#upload_logo_error').html('');
            var oFReader = new FileReader();
            oFReader.readAsDataURL(document.getElementById("img-upload").files[0]);
            oFReader.onload = function (oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result;
            };
        }
    }

    function PreviewImage1() {
        var ext = $('#img-upload1').val().split('.').pop().toLowerCase();
        if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
                $('#img-upload1').val('');
                $('#upload_logo_error1').html('gif , png , jpg , jpeg are allowed.');
        } else {
            $('#upload_logo_error1').html('');
            var oFReader = new FileReader();
            oFReader.readAsDataURL(document.getElementById("img-upload1").files[0]);
            oFReader.onload = function (oFREvent) {
            document.getElementById("uploadPreview1").src = oFREvent.target.result;
            };
        }
    }
    
    $(document).ready(function() {
        var readURL = function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.profile-pic').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $(".file-upload").on('change', function() {
            
            readURL(this);
        });
        $(".upload-button").on('click', function() {
            $(".file-upload").click();
        });
    });

    function itemAddToCartSession(id,price,name,qty,type){
        //fbq('track', 'AddToCart', {value: price,currency: 'NGN'});
        var _token = $('input#_token').val();
        var data = new FormData();
        data.append('_token',_token);
        data.append('price',price);
        data.append('id',id);
        data.append('name',name);
        data.append('type',type);
        data.append('qty',qty);
        $.ajax({
            type: 'POST',
            url: APP_URL+'/item/session/addtocart',
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            data: data,
            processData: false,
            contentType: false,
            //async:false,
            success: function (msg) {
                if(msg.status === "success"){
                    $('#openpallyModal').modal('hide');
                    $('.btn-save-cart').prop('disabled', false);
                    $('#cartCount').val(msg.cartCount);
                    $('.cartCount').html(msg.cartCount);
                    $('.cartAmount').html(msg.cartTotal);
                    $('.success_message').html(msg.message);
                    $('.show_message_success').show();
                    if(type == 1){
                        setTimeout(function() {
                            window.location.href=APP_URL+'/checkout';
                        }, 1000);
                    }
                }else{
                    $('.btn-save-cart').prop('disabled', false);
                    $('.success_message').html(msg.message);
                    $('.show_message_success').show();
                }
                $(".alert-success").delay(3000).fadeOut();
            }
        });
    } 
    
    function increase(id , rowId , title,price){
        $('.loaders').show();
        $('.count_'+rowId).val(parseInt($('.count_'+rowId).val()) + 1 );
        
        itemAddToCartSession(id,price,title,1,1)
    }

    function decrease(id , rowId){
        var initCount = $('.count_'+rowId).val();
        $('.count_'+rowId).val(parseInt($('.count_'+rowId).val()) - 1 );
        if ($('.count_'+rowId).val() == 0) {
            $('.count_'+rowId).val(1);
        }
        CartID = rowId;
        deleteCartItemConfirm();
    }
    
    
    function deleteCartItemConfirm(){
        $.ajax({
            type: 'GET',
            url: APP_URL+'/item/session/cart/deleteItem/'+CartID,
            data: '',
            processData: false,
            contentType: false,
            success: function (msg) {
                if(msg.status === "success"){
                    $('.rowCount'+CartID).remove();
                    $('.price-total').html(msg.total);
                    $('#cartCount').val(msg.cartCount);
                    $('.cartCount').html(msg.cartCount);
                    $('.success_message').html(msg.message);
                    $('.show_message_success').show();
                    $(".alert-success").delay(3000).fadeOut();
                    setTimeout(function() {
                        window.location.href=APP_URL+'/checkout';
                    }, 1000);
                }else{
                    $('.success_message').html(msg.message);
                    $('.show_message_success').show();
                    $(".alert-success").delay(3000).fadeOut();
                }
            }
        });
    }
    
    function deleteCartItem(id){
        CartID = id;
        $('#removePayment').modal('show');
    }
    
    
    
    $("body").on("click",".list-btn-login",function(e){
        var url  = window.location.href;
        window.location.href=APP_URL+'/login?redirect_url='+url;
    });
    
    
    
    $('.search-box').on("keyup", function(){
        /* Get input value on change */
        $('.bmnbpY').show();
        $('.search-dropdown').show();
        var inputVal = $(this).val();
        var strCount = inputVal.length;
        if(strCount > 0){
            var resultDropdown = $('.input-group').siblings(".result");
            if(inputVal.length){
                $.get(APP_URL+'/get-products/'+inputVal, {}).done(function(data){
                    // Display the returned data in browser
                    $('.result').html(data);
                    //resultDropdown.html(data);
                });
            }
        }else{
            $('.result').html('');
        }
    });
    
    $(".press-btn-search").keypress(function(e) {
        if(e.which == 13) {
            headerSearch();
        }
    });

    $(document).on("click", ".result p", function(){
        var product_title = $(this).attr('product_title');
        var product_slug = $(this).attr('product_slug');
        $('#slug').val(product_slug);
        $('.search-box').val(product_title);
        $(this).parent(".result").empty();
        $('.search-box').focus();
    });
    
    function headerSearch(){
        var slug = $('#slug').val();
        if(slug != ''){
            window.location.href=APP_URL+'/product/detail/'+slug;
        }
    }
    
    $('.search-box-mobile').on("keyup", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
        var strCount = inputVal.length;
        if(strCount > 0){
            if(inputVal.length){
                $.get(APP_URL+'/get-products-mobile/'+inputVal, {}).done(function(data){
                    $('.search-list').html(data);
                });
            } 
        }else{
            $('.search-list').html('');
        }
    });
    
    $("body").on("click",".signupUser",function(e){
        var _token = $('input#_token').val();
        var first_name = $("#first_name").val();
        var last_name = $("#last_name").val();
        var email   = $("#email").val();
        var password = $("#password").val();
        var dob = $("#datepicker").val();
        var gender = $("#gender").val();
        var nationality   = $("#nationality").val();
        var mobile = $("#mobile").val();
        var phone = $("#phone").val();
        var skills = $("#skills").val();
        var why_join   = $("#why_join").val();
        var employee_status = $("#employee_status").val();
        
        var count = 0;
        if (first_name !=""){
            $("#first_name").css({"border-color": "#dddddd", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#first_name").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if (last_name !=""){
            $("#last_name").css({"border-color": "#dddddd", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#last_name").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }

        if (email !=""){
            $("#email").css({"border-color": "#dddddd", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else{
            $("#email").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if(password !=""){
            $("#password").css({"border-color": "#dddddd", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#password").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if(dob !=""){
            $("#dob").css({"border-color": "#dddddd", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#dob").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if(nationality !=""){
            $("#nationality").css({"border-color": "#dddddd", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#nationality").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if(mobile !=""){
            $("#mobile").css({"border-color": "#dddddd", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        } else{
            $("#mobile").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }

        if(count == 0){
            //$('.loaders').show();
            var data = new FormData();
            data.append('_token',_token);
            data.append('first_name',first_name);
            data.append('last_name',last_name);
            data.append('email',email);
            data.append('password',password);
            data.append('dob',dob);
            data.append('gender',gender);
            data.append('nationality',nationality);
            data.append('mobile',mobile);
            data.append('phone',phone);
            data.append('skills',skills);
            data.append('why_join',why_join);
            data.append('employee_status',employee_status);
            $.ajax({
                type: 'POST',
                url: APP_URL+'/checkEmail',
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                data: data,
                processData: false,
                contentType: false,
                // async:false,
                success: function (msg) {
                    $('#register-modal').modal('hide');
                    //$('.loaders').hide();
                    if(msg.status == 'success'){
                        $('html,body').animate({
                            scrollTop: $("body").offset().top},
                            'slow');
                        $('.success_message').html(msg.message);
                        $('.show_message_success').show();
                        $(".alert-success").delay(3000).fadeOut();
                        setTimeout(function() {
                            window.location.reload();
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
    
    $("body").on("click",".loginUser",function(e){
        var _token = $('input#_token').val();
        var email   = $("#l_email").val();
        var password = $("#l_password").val();
        // append data
        var data = new FormData();
        data.append('_token',_token);
        data.append('email',email);
        data.append('password',password);
        var count = 0;
        if (email !=""){
            $("#l_email").css({"border-color": "dddddd", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#l_email").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if (password !=""){
            $("#l_password").css({"border-color": "#dddddd", "border-width": "1px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
        }else {
            $("#l_password").css({"border-color": "#FD0004", "border-width": "2px", "border-style": "solid","box-shadow":"2px 4px 10px rgba(0,0,0,.2)"});
            count++;
        }
        if (count == 0){
            //$('.loaders').show();
            $.ajax({
                type: 'POST',
                url: APP_URL+'/login',
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                data: data,
                processData: false,
                contentType: false,
                // async:false,
                success: function (msg) {
                    //$('.loaders').hide();
                    if(msg.status === 'success'){
                        $('#login-modal').modal('hide');
                        $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
                        $('.success_message').html(msg.message);
                        $('.show_message_success').show();
                        $(".alert-success").delay(300000).fadeOut();
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    }else{
                        $('html,body').animate({
                                scrollTop: $("body").offset().top},
                                'slow');
                        $('.error_message').html(msg.message);
                        $('.show_message_error').show();
                        $(".alert-danger").delay(300000).fadeOut();
                    }
                }
            });
        }
    });