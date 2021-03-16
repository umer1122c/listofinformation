@extends('admin.template')
@section('content')
<style>
    .rating-stars ul {
    list-style-type: none;
    padding: 0;
    -moz-user-select: none;
    -webkit-user-select: none;
    margin-bottom: 0;
}

.rating-stars ul>li.star {
    display: inline-block;
    cursor: pointer;
}


/* Idle State of the stars */

.rating-stars ul>li.star>i.fa {
    font-size: 16px;
    /* Change the size of the stars */
    color: #ccc;
    /* Color on idle state */
}


/* Hover state of the stars */

.rating-stars ul>li.star.hover>i.fa {
    color: #FFCC36;
}


/* Selected state of the stars */

.rating-stars ul>li.star.selected>i.fa {
    color: #FF912C;
}
.success-box {
    margin: auto;
    color: #ff912c;
    position: absolute;
    top: 0;
    left: 55%;
}
.reviews-bg section.rating-widget {
    position: relative;
    width: 100%;
    margin: 0 0 0 0;
}
section.rating-widget {
    position: relative;
    width: 100%;
    margin: 0 0 0 0;
}
li.start-clr {
    width: 16%;
}
.products-inner .success-box {
    left: 70%;
}
.reviews-bg .reviews-inner .success-box {
    left: 72%;
}
.reviews-from .pink-btn {
    background: #ff117b;
    padding: 10px 30px;
    border: none;
    color: #fff;
    font-size: 14px;
    margin: 0 12px 0 0;
}
/* ----- FIGURES-SECTION-CSS-END ------  */
</style>
<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        
        <div class="row">
            @include('common.errors')
            <div class="col-md-12">
                <div class="card">
                    
                    <div class="card-heading border bottom">
                        <h4 class="card-title">{{$table}}</h4>
                    </div>
                    <div class="card-block">
                        <div class="card-block">
                            <div class="row">
                                <div class="col-md-8 ml-auto mr-auto">
                                    <form role="form" id="form-validation" action="" method="post" enctype="multipart/form-data">
                                        {!! csrf_field() !!}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <!--<p class="mrg-top-10 text-dark"> <b>Home Slider Image dimension: 2000x500</b></p>-->
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="img-upload" class="pointer">
                                                        <img id="uploadPreview" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" width="118" alt="">
                                                        <span class="btn btn-default display-block no-mrg-btm" style="border-radius: 0px 0px 5px 5px;">Choose file</span>
                                                        <input class="d-none" type="file" name="image" id="img-upload" onchange="PreviewImage();">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark" style=" color: #ff3c7e;" id="upload_logo_error"></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <ul class="list-unstyled">
                                                        <li class="mb-2"> <strong>Overall Rating</strong></li>
                                                        <section class='rating-widget'>

                                                            <!-- Rating Stars Box -->
                                                            <div class='rating-stars'>
                                                                <ul class='stars'>
                                                                    <li class='star' title='Poor' data-value='1'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                    <li class='star' title='Fair' data-value='2'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                    <li class='star' title='Good' data-value='3'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                    <li class='star' title='Excellent' data-value='4'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                    <li class='star' title='WOW!!!' data-value='5'>
                                                                        <i class='fa fa-star fa-fw'></i>
                                                                    </li>
                                                                </ul>
                                                            </div>
        <!--                                                    <div class='success-box'>
                                                                <div class='text-message'></div>
                                                            </div>-->
                                                        </section>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Member Name</label>
                                                    <input type="hidden" class="form-control" name="rating" id="rating" value="{{Request::old('rating')}}"  required>
                                                    <input type="text" class="form-control" name="name" id="name" value="{{Request::old('name')}}" placeholder="Member Name" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Review</label>
                                                    <textarea class="form-control" rows="3" name="review" placeholder="Review" required>{{Request::old('review')}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <button type="button" class="btn btn-default" onclick="clearFields();">Clear</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Content Wrapper END -->
<script>
    function clearFields(){
        $('#form-1-5').val('');
        $('#title').val('');
        
    }
    
    // ****** RATING-JS-START ********//
$(document).ready(function() {
    $('.stars li').on('mouseover', function() {
        var onStar = parseInt($(this).data('value'), 10); 
        $(this).parent().children('li.star').each(function(e) {
            if (e < onStar) {
                $(this).addClass('hover');
            } else {
                $(this).removeClass('hover');
            }
        });

    }).on('mouseout', function() {
        $(this).parent().children('li.star').each(function(e) {
            $(this).removeClass('hover');
        });
    });


    /* 2. Action to perform on click */
    $('.stars li').on('click', function() {
        var onStar = parseInt($(this).data('value'), 10); // The star currently selected
        var stars = $(this).parent().children('li.star');

        for (i = 0; i < stars.length; i++) {
            $(stars[i]).removeClass('selected');
        }

        for (i = 0; i < onStar; i++) {
            $(stars[i]).addClass('selected');
        }

        // JUST RESPONSE (Not needed)
        var ratingValue = parseInt($('.stars li.selected').last().data('value'), 10);
        $('#rating').val(ratingValue);
        var msg = "";
        if (ratingValue > 1) {
            msg = "" + ratingValue + " (5).";
        } else {
            msg = "" + ratingValue + " (5)";
        }
        responseMessage(msg);

    });
});
function responseMessage(msg) {
    $('.success-box').fadeIn(200);
    $('.success-box div.text-message').html("<span>" + msg + "</span>");
}
// ****** RATING-JS-ENDS ********//
</script>
@endsection