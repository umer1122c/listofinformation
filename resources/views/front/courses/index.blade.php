@extends('front.template')
@section('content')
<script>
    var APP_URL = {!! json_encode(url('/')) !!};
    var category = [];
    var keyword = '';
</script>
<!------ HEADER-END ------>
<section class="course_bg">
    <div class="container">
        <div class="text-center">
            <h5>Courses</h5>
            <h1>Details</h1>
        </div>
    </div>
</section>
<nav aria-label="breadcrumb" class="breadcrumbs-custom ">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Mentorship Details</a></li>
        <li class="breadcrumb-item active" aria-current="page">Courses</li>
    </ol>
</nav>
<section class="main_courses">
    <div class="container">
        <div class="main_head text-center mt-2">
            <h2>Courses</h2>
        </div>
        <div class="search-bg">
            <div class="form-group mb-0">
                <input type="text" id="dSuggest" placeholder="Search Courses"><i class="fa fa-search"></i>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-3">
                <div class="left_courses">
                    <h4 class="mb-3">Course Categories</h4>
                    <div class="coursesmobile_view">
                        <span data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" id="categoery">
                            <i class="fa fa-align-left" aria-hidden="true"></i>
                        </span>
                        <ul class="list-unstyled checkbox-bg collapse " id="collapseOne"  aria-labelledby="headingOne" data-parent="#categoery">
                            <input type="hidden" name="left_category" id="left_category" value="{{$top_category}}">
                            @if(count($categories) > 0)
                                @foreach($categories as $row)
                                    <li>
                                        <span class="d-inline-block checktext-clr">{{$row->title}}</span>
                                        <span class="d-inline-block float-right">
                                            <label class="custom-control fill-checkbox">
                                                <input type="checkbox" class="fill-control-input category" <?php if(!empty($category_name) && $category_name == $row->slug ) { echo "checked"; } ?> value ="{{ $row->id }}">
                                                <span class="fill-control-indicator"></span>
                                            </label>
                                        </span>
                                    </li>
                                @endforeach
                            @else
                                <li>
                                    <span class="d-inline-block checktext-clr">No category found.</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <ul class="list-unstyled checkbox-bg coursesweb_view">
                        @if(count($categories) > 0)
                            @foreach($categories as $row)
                                <li>
                                    <span class="d-inline-block checktext-clr">{{$row->title}}</span>
                                    <span class="d-inline-block float-right">
                                        <label class="custom-control fill-checkbox">
                                            <input type="checkbox" class="fill-control-input category" <?php if(!empty($category_name) && $category_name == $row->slug ) { echo "checked"; } ?> value ="{{ $row->id }}">
                                            <span class="fill-control-indicator"></span>
                                        </label>
                                    </span>
                                </li>
                            @endforeach
                        @else
                            <li>
                                <span class="d-inline-block checktext-clr">No category found.</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-md-12 col-lg-9 courselist">
                @include('front.courses.courseLoad')
            </div>
        </div>
    </div>
</section>
<!------ FOOTER_SECTION_START ------>  
<script>
    $('#dSuggest').keypress(function() {
        var _token = $('input#_token').val();
        $('.loaders').show();
        var keyword = $('#dSuggest').val();
        var url = window.location.href;
        $.ajax({
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                data: {
                    '_token': _token,
                    'category': category,
                    'keyword':keyword
                },
            success: function(response) {
                $('.loaders').hide();
                $('.courselist').html(response);
            }
        });
    });
    
    $('.category:checkbox').on('change', function() {
        var _token = $('input#_token').val();
        $('.loaders').show();
        var left_category = $('#left_category').val();
        if(left_category != ''){
            category.push(left_category);
            $('#left_category').val('');
        }
        if (this.checked){
            category.push($(this).val());
        }else{
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
                    'category': category,
                    'keyword':keyword
                },
            success: function(response) {
                $('.loaders').hide();
                $('.courselist').html(response);
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
            keyword = $('#dSuggest').val();
            getProducts(url,category,keyword);
            window.history.pushState("", "", url);
        });
        function getProducts(url,category,keyword){
            $('.loaders').show();
            $.ajax({
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'GET',
                data: {
                    'category': category,
                    'keyword':keyword
                },
            }).done(function(data) {
                $('.loaders').hide();
                $('.courselist').html(data);
            }).fail(function() {
                $('.courselist').html('Products could not be loaded.');
            });
        }
    });
</script>
@endsection 
 
