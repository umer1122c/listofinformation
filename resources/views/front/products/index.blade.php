@extends('front.template')
@section('content')
<input type="hidden" id="_token" value="{{ csrf_token() }}">
<script>
    var APP_URL = {!! json_encode(url('/')) !!};
    var category = [];
    var subCategory = [];
    var filterPrice = 'DESC';
</script>
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
<!------ SHOP-BANNER-SECTION-START ------>
    <section class="shop-banner">
        @if(!empty($category_name))
            <img class="shop-img" src="@if(file_exists(public_path().'/categories/'.$category->banner_image)) {{asset('categories/'.$category->banner_image)}} @else {{asset('front/placeholder.png')}} @endif">
            <div class="container">
                <?php
                    $catExp = explode(' ' , $category_name);
                    if(isset($catExp[1])){
                        $catName = $catExp[1];
                    }else{
                        $catName = '';
                    }
                ?>
                <h2 class="text-white "><span class="pink-clr ">{{$catExp[0]}}</span> {{$catName}}</h2>
            </div>
        @else
            <img class="shop-img" src="{{asset('front/assets/images/all-products.jpg')}}">
            <div class="container">
                <h2 class="text-white "><span class="pink-clr ">Shop</span> Now</h2>
            </div>
        @endif
        
    </section>
    <!------ SHOP-BANNER-SECTION-END ------>
    <!------ MAIN-SHOP-SECTION-START ------>
        <section class="mainshop-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-3 mobile-top">
                        <div class="shop-catergory">
                            <h5 class="font-weight-bold mb-2">Category</h5>
                            <ul class="list-unstyled checkbox-bg">
                                <input type="hidden" name="left_category" id="left_category" value="{{$top_category}}">
                                <input type="hidden" name="left_sub_category" id="left_sub_category" value="{{$top_sub_category}}">
                                @if(count($categories) > 0)
                                    @foreach($categories as $cat)
                                        <li>
                                            <span class="d-inline-block checktext-clr">{{$cat->title}}</span>
                                            <span class="d-inline-block float-right">
                                                <label class="custom-control fill-checkbox">
                                                    <input type="checkbox" class="fill-control-input category" <?php if(!empty($category_name) && $category_name == $cat->slug ) { echo "checked"; } ?> value ="{{ $cat->id }}">
                                                    <span class="fill-control-indicator"></span>
                                                </label>
                                            </span>
            <!--                                <span data-toggle="collapse" href="#multiCollapsecheckbox" role="button" aria-expanded="false" aria-controls="multiCollapsecheckbox">
                                                <i class="fa fa-angle-down list-arrow" aria-hidden="true"></i>
                                            </span>-->
                                        </li>
                                    @endforeach
                                @endif
    <!--                            <div class="collapse multi-collapse ml-3" id="multiCollapsecheckbox">

                                    <ul class="list-unstyled">
                                        <li>
                                            <span class="d-inline-block  checktext-clr">Product 1</span>
                                            <span class="d-inline-block float-right">
                                                <label class="custom-control fill-checkbox">
                                                <input type="checkbox" class="fill-control-input">
                                                <span class="fill-control-indicator"></span>
                                            </label>
                                            </span>
                                        </li>
                                        <li>
                                            <span class="d-inline-block  checktext-clr">Product 2</span>
                                            <span class="d-inline-block float-right"><label class="custom-control fill-checkbox">
                                                <input type="checkbox" class="fill-control-input">
                                                <span class="fill-control-indicator"></span>
                                            </label>
                                            </span>
                                        </li>

                                    </ul>
                                </div>-->
                            </ul>
                        </div>
                    </div>
                    <!------ LISTING-PRODUC-START  ------>
                        <div class="col-md-12 col-lg-9">
                            <div class="loaders" style="display:none; margin: auto; width: 100%;text-align: center;">
                                <img src="{{asset('front/loader.gif')}}" alt="" class="img-fluid" style="width: 50px;"/>
                            </div>
                            <div class="productlist">
                                @include('front.products.productLoad')
                            </div>

                        </div>
                    <!------ LISTING-PRODUC-END  ------>
                </div>
            </div>
        </section>
    <!------ MAIN-SHOP-SECTION-END ------>
    <!------ ARRIVALS-SECTION-START -->
        @include('front.home.sectionLoad3')
    <!------ ARRIVALS-SECTION-END -->
    
<script>
    $('.category:checkbox').on('change', function() {
        var _token = $('input#_token').val();
        $('.loaders').show();
        var left_category = $('#left_category').val();
        if(left_category != ''){
            category.push(left_category);
            $('#left_category').val('');
        }
        filterPrice = $(".filterPrice").attr("value");
        if (this.checked){
            category.push($(this).val());
        }
        else{
            category.splice($.inArray($(this).val(), category),1);
        }
        console.log(category);
        var url = window.location.href;
        $.ajax({
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                data: {
                    '_token': _token,
                    'filterPrice': '',
                    'category': category,
                    'subCategory': subCategory
                },
            success: function(response) {
                $('.loaders').hide();
                $('.productlist').html(response);
            }
        });
    });
    
    $('.subCategory:checkbox').on('change', function() {
        var _token = $('input#_token').val();
        var left_category = $('#left_category').val();
        if(left_category != ''){
            category.push(left_category);
            $('#left_category').val('');
        }
        var left_sub_category = $('#left_sub_category').val();
        if(left_sub_category != ''){
            subCategory.push(left_sub_category);
            $('#left_sub_category').val('');
        }
        $('.loaders').show();
        filterPrice = $(".filterPrice").attr("value");
        if (this.checked){
            subCategory.push($(this).val());
        }
        else{
            subCategory.splice($.inArray($(this).val(), subCategory),1);
        }
        console.log(subCategory);
        var url = window.location.href;
        $.ajax({
                url: url,
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
    
    $(function() {
        $('body').on('click', '.pagination a', function(e) {
            $('html,body').animate({
                scrollTop: $("body").offset().top},
                'slow');
            e.preventDefault();
            $('#load a').css('color', '#dfecf6');
            var url = $(this).attr('href');
            var left_category = $('#left_category').val();
            if(left_category != ''){
                category.push(left_category);
                $('#left_category').val('');
            }
            var left_sub_category = $('#left_sub_category').val();
            if(left_sub_category != ''){
                subCategory.push(left_sub_category);
                $('#left_sub_category').val('');
            }
            getProducts(url,filterPrice,category,subCategory);
            window.history.pushState("", "", url);
        });
        function getProducts(url,filterPrice,category,subCategory){
            $('.loaders').show();
            $.ajax({
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'GET',
                data: {
                    'filterPrice': filterPrice,
                    'category': category,
                    'subCategory': subCategory
                },
            }).done(function(data) {
                $('.loaders').hide();
                $('.productlist').html(data);
            }).fail(function() {
                $('.productlist').html('Products could not be loaded.');
            });
        }
    });
</script>
@endsection 
 
