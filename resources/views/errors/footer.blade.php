<div class="alert alert-danger alert-dismissable show_message_error_news red-text" style=" display:none">
    <button type="button" class="close close-sm" data-dismiss="alert">
        <i class="fa fa-times"></i>
    </button>
    <p class="error_message mb-0"> You already sign up for our newsletter! </p>
</div>
<div class="alert alert-block alert-success show_message_success_news green-text" style=" display:none">
    <button type="button" class="close close-sm" data-dismiss="alert">
        <i class="fa fa-times"></i>
    </button>
    <p class="success_message mb-0"> You are successfully sign up for our newsletter!</p>
</div>
<input type="hidden" id="_token" value="{{ csrf_token() }}">
    <?php $footer_categories = commonHelper::getFooterCategories(); ?>
@if($class == 'products')
    <!---- footer-mobile -->
        <footer class="bottomappfooter">
            <div class="bottomApplogo">  
                <a href="{{ url('/')}}">
                    <img src="{{asset('front/assets/images/footer-logo.png')}}" class="img-fluid">
                </a>
            </div>
            <div class="cartbottom-bg">
                <section class="bottomAppBarSection bottomAppBarSectionAlignStart">
                    <span class="bottomAppBarActionItem">
                        <div class="searchIcon">
                            <a id="two" class="mobilesearch" href="#">
                                <i class="fa fa-search" aria-hidden="true"></i></a>
                        </div>
                    </span>
                    <span class="bottomAppBarNavIcon">
                            <div class="menuFilterIcon">
                                <a href="#" id="three" class="mobilefilter" >
                                    <i class="fa fa-filter" aria-hidden="true"></i>
                                </a>    
                            </div>
                    </span>
                </section>
                <div  class="bottomAppBarSection bottomAppBarAlignEnd">
                        <span class="cartAmount">
                            <span class="amount">₦ <span class="cartAmount">{!!  commonHelper::getCartTotalAmount() !!}</span></span>
                        </span>
                        <span class="bottomAppBarActionItem">
                            <a href="{{url('my/cart')}}" class="white-clr">
                                <div class="cartIcon">
                                    <i class="fa fa-cart-plus" aria-hidden="true"></i>
                                    <span class="fcart-text cartCount">{!!  commonHelper::getCartCount() !!}</span>
                                </div>
                            </a>
                        </span>
                </div>
            </div>
        </footer>
<!--        <footer class="mobile-footer ">
            <div class="container-fluid">
                <ul class="list-inline mb-0 d-flex">
                    <li class="list-inline-item w-20">
                        <a id="two" class="mobilesearch" href="#"><i class="fa fa-search" aria-hidden="true"></i></a>
                        <div id="modal-container">
                            <div class="modal-background">
                                <div class="search-modal">
                                    <ul class="list-unstyled modal-head">
                                        <li class="d-inline-block">
                                            <span id="back_arrow"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>
                                        </li>
                                        <li class="d-inline-block">
                                             Actual search box 
                                            <div class="form-group has-search mb-0">
                                                <input type="text" class="form-control search-box-mobile" placeholder="Search for menu items">
                                            </div>
                                        </li>
                                        <li class="d-inline-block float-right footer-search"><i class="fa fa-search"></i></li>
                                    </ul>
                                    <div class="search-list">
                                        <p>No products found.</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </li>
                    <li class="list-inline-item w-20">
                        <a href="#" id="three" class="mobilefilter" data-toggle="modal" data-target="#exampleModal">
                            <i class="fa fa-filter" aria-hidden="true"></i></a>
                        
                    </li>
                    <li class="list-inline-item w-20">
                        <div id="logo" class="d-lg-none d-block"><img src="{{asset('front/assets/images/footer-logo.png')}}" class="img-fluid"></div>
                    </li>
                    <li class="list-inline-item text-right">
                        <a href="{{ url('my/cart')}}" class="skin-cart"><span class="cart-text">₦ <span class="cartAmount">{!!  commonHelper::getCartTotalAmount() !!}</span> </span> <i class="fa fa-cart-plus" aria-hidden="true"></i>
                            <span class="fcart-text">3</span>
                        </a>
                    </li>
                </ul>
            </div>
        </footer>-->
    <!---- footer-mobile -->
    <div id="modal-container">
        <div class="modal-background">
            <div class="search-modal">
                <ul class="list-unstyled modal-head">
                    <li class="d-inline-block">
                        <span id="back_arrow"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>
                    </li>
                    <li class="d-inline-block">
                        <!-- Actual search box -->
                        <div class="form-group has-search mb-0">
                            <input type="text" class="form-control search-box-mobile" placeholder="Search for menu items">
                        </div>
                    </li>
<!--                                        <li class="d-inline-block float-right footer-search"><i class="fa fa-search"></i></li>-->
                </ul>
                <div class="search-list">
                    <p>No products found.</p>
                </div>
            </div>

        </div>
    </div>
    
    <div id="modal-container1">
        <div class="modal-background1">
            <div class="modal">
                <div class="inner-modal">
                    <ul class="list-unstyled modal-head">
                        <li class="d-inline-block">
                            <span id="fillterback_arrow"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>
                        </li>
                        <li id="apply" class="d-inline-block float-right pink-clr font-weight-bold filter-mobile">APPLY</li>
                    </ul>
                    <div class="modal-text">
                        <div class="shop-catergory">
                            <h5 class="font-weight-bold mb-2">Category</h5>
                            <input type="hidden" name="top_category" id="top_category" value="{{$top_category}}">
                            <input type="hidden" name="top_sub_category" id="top_sub_category" value="{{$top_sub_category}}">
                            <ul class="list-unstyled checkbox-bg">
                                @if(count($categories) > 0)
                                    @foreach($categories as $row)
                                        <li>
                                            <span class="d-inline-block checktext-clr">{{$row->title}}</span>
                                            <span class="d-inline-block float-right">
                                                <label class="custom-control fill-checkbox">
                                                    <input type="checkbox" class="fill-control-input category-footer" <?php if(!empty($category_name) && $category_name == $row->slug ) { echo "checked"; } ?> value ="{{ $row->id }}">
                                                    <span class="fill-control-indicator"></span>
                                                </label>
                                            </span>
<!--                                                        <span data-toggle="collapse" href="#multiCollapsecheckbox1" role="button" aria-expanded="false" aria-controls="multiCollapsecheckbox1">
                                                <i class="fa fa-angle-down list-arrow" aria-hidden="true"></i>                    
                                            </span>-->
                                        </li>
                                    @endforeach
                                @else
                                    <li role="button">
                                        <label class="custom-control fill-checkbox">
                                            <span class="fill-control-description">No Category Found</span>
                                        </label>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
<!----- MAIN-FOOTER-START ----->
    <footer class="main-footer">
        <div class="container">
            <div class="row  mainfooter-list">
                <div class="col-4 col-md-4 text-left">
                    <a id="two" class="button"><i class="fa fa-search" aria-hidden="true"></i>
                    </a>
                    <div id="modal-container">
                        <div class="modal-background">
                            <div class="search-modal">
                                <ul class="list-unstyled modal-head">
                                    <li class="d-inline-block">
                                        <span id="back_arrow"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>
                                    </li>
                                    <li class="d-inline-block">
                                        <!-- Actual search box -->
                                        <div class="form-group has-search mb-0">
                                            <input type="text" class="form-control search-box-mobile" placeholder="Search for menu items">
                                        </div>
                                    </li>
                                    <!--<li class="d-inline-block float-right"><i class="fa fa-search"></i></li>-->
                                </ul>


                                <div class="search-list">
                                    <p>No products found.</p>
                                </div>


                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-4 col-md-4">
                    <a href="tel:+23408178706045"><i class="fa fa-phone" aria-hidden="true"></i></a>
                </div>
                <div class="col-4 col-md-4 text-right">
                    <a href="{{ url('my/cart')}}">
                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                        <span class="fcart-text  cartCount">{!!  commonHelper::getCartCount() !!}</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    <!----- MAIN-FOOTER-END ----->
@endif
<footer class="footer-wrapper mt-5">
    <div class="container">
        <div class="footer-inner text-center">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <h2 class="font-weight-bold">Hey, let’s Keep in Touch!</h2>
                    <p>Sign up for our Newsletter!</p>
                    <div class="navbar-form search-bg" role="search">
                        <div class="input-group newlatter_error">
                            <input type="text" class="form-control" name="email" id="email" placeholder="Email Address">
                            <div class="input-group-append">
                                <button class="btn" type="button" onclick="newslatter()">Subscribe</button>
                            </div>
                        </div>
                    </div>
                    <ul class="footer-navbar">
                        <li class="d-inline-block">
                            <a  href="{{url('terms')}}">Terms & Conditions</a>
                        </li>
                        <li class="d-inline-block">
                            <a  href="{{url('privacy/policy')}}">Privacy & Policy</a>
                        </li>
                    </ul>
                    <ul class="list-unstyled social-icons">
                        <li class="d-inline-block">
                            <a href="javascript:void(0)">
                                 <i class="fa fa-facebook rounded-circle" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="d-inline-block">
                            <a href="javascript:void(0)"><i class="fa fa-instagram rounded-circle" aria-hidden="true"></i></a>
                        </li>
                    </ul>
<!--                    <ul class="list-unstyled social-icons">
                        <li class="d-inline-block">
                            <a href="#"><img class="facebook"src="{{asset('front/assets/images/facebook-img.png')}}"></a>
                        </li>
                        <li class="d-inline-block">
                            <a href="#"><img class="instagram" src="{{asset('front/assets/images/instagram-img.png')}}"></a>
                        </li>
                    </ul>-->
                    <p>Copyright 2020 - <span class="pink-clr">Hush'D Makeover</span> </p>
                </div>
            </div>
        </div>              
    </div>
</footer>

<script>
    $('.category-footer:checkbox').on('change', function() {
        var _token = $('input#_token').val();
        var top_category = $('#top_category').val();
        if(top_category != ''){
            category.push(top_category);
            $('#top_category').val('');
        }
        $('.loaders').show();
        if (this.checked){
            category.push($(this).val());
        }else{
            category.splice($.inArray($(this).val(), category),1);
        }
        console.log(category);
        $.ajax({
                url: APP_URL+'/get/product/subcategory',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                data: {
                    '_token': _token,
                    'category': category
                },
            success: function(response) {
                $('.loaders').hide();
                $('.catList').html(response);
            }
        });
    });
    
    $('.sub-category-footer:checkbox').on('change', function() {
        var top_category = $('#top_category').val();
        if(top_category != ''){
            category.push(top_category);
            $('#top_category').val('');
        }
        var top_sub_category = $('#top_sub_category').val();
        if(top_sub_category != ''){
            subCategory.push(top_sub_category);
            $('#top_sub_category').val('');
        }
        filterPrice = $(".filterPrice").attr("value");
        if (this.checked){
            subCategory.push($(this).val());
        }
        else{
            subCategory.splice($.inArray($(this).val(), subCategory),1);
        }
        console.log(subCategory);
    });
    
    $('.filter-mobile').on('click', function() {
        var _token = $('input#_token').val();
        $.ajax({
                url: APP_URL+'/products/all',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                data: {
                    '_token': _token,
                    'filterPrice': filterPrice,
                    'category': category,
                    'subCategory': subCategory
                },
            success: function(response) {
                $('.loaders').hide();
                $('.productlist').html(response);
            }
        });
    });
    
    function newslatter() {
        var _token = $('input#_token').val();
        var email   = $("#email").val();
        // append data
        var data = new FormData();
        data.append('_token',_token);
        data.append('email',email);
        var count = 0;
        if (email !=""){
            $(".newlatter_error").css({"border": "none"});
        }else {
            $(".newlatter_error").css({"border-color": "#FD0004", "border-width": "1px", "border-style": "solid"});
            count++;
        }
        if (count == 0){
            //$('.loaders1').show();
            $.ajax({
                type: 'POST',
                url: APP_URL+'/signup/newsletter',
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                data: data,
                processData: false,
                contentType: false,
                // async:false,
                success: function (msg) {
                    
                    if(msg == 'success'){
                        $("#email").val('');
                        $('.show_message_success_news').show();
                        $(".alert-success").delay(3000).fadeOut(); 
                    }else{
                        $('.show_message_error_news').show();
                        $(".alert-danger").delay(3000).fadeOut();
                    }
                }
            });
        }
    }
</script>