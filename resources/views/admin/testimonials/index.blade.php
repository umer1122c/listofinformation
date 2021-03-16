@extends('admin.template')
@section('content')
<style>
    .list-info img.thumb-img {
        height: 80px;
        width: 80px;
        border-radius: 50%;
    }
    .list-info .thumb-img {
        line-height: 40px;
        width: 40px;
        text-align: center;
        font-size: 17px;
        border-radius: 0px;
        float: left;
    }
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
        <?php if(Session::has('success_msg')) { ?>
            <div class="alert alert-block alert-success fade in" style="margin:10px 15px;">
                <button type="button" class="close close-sm" data-dismiss="alert">
                    <i class="fa fa-times"></i>
                </button>
                 <p style="text-align:center; margin-bottom:0px; ">  {{ Session::get('success_msg') }}  </p>
            </div>
        <?php } ?>
        <div class="container-fluid">
            <div class="page-title">
                <h4><?php echo $table; ?></h4>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-block">
                            <div class="clearfix">
                                <div class="btn-group">
                                    <a href="{{URL::to('admin/testimonial/add')}}"><button id="editable-sample_new" class="btn btn-primary">Add New <i class="fa fa-plus"></i></button></a>
                                </div>
                            </div>
                            <div class="table-overflow">
                                <table id="dt-opt" class="table table-lg table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Review</th>
                                            <th>Image</th>
                                            <th>Rating</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($testimonials) > 0)
                                            @foreach($testimonials as $row)
                                                <tr>
                                                    <td width="20%">
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->name}}</span>
                                                        </div>
                                                    </td>
                                                    <td width="20%">
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->review}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="list-info mrg-top-10">
                                                            @if($row->image != '')
                                                                <img class="thumb-img" src="{{asset('/testimonials/'.$row->image)}}" alt="">
                                                            @else
                                                                <img class="thumb-img" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="">
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td width="20%">
                                                        <div class="relative mrg-top-15">
                                                            <section class='rating-widget'>
                                                                <div class='rating-stars'>
                                                                    {!! commonHelper::clientTestimonialReview($row->id) !!}
                                                                </div>
                                                                <div class='success-box'>
                                                                    <div class='text-message'></div>
                                                                </div>
                                                            </section>
                                                        </div>
                                                    </td>
                                                    <td width='25%'>
                                                        <a href="{{URL::to('admin/testimonial/edit/'.$row->id)}}" class="btn btn-info">
                                                            <i class="ti-export pdd-right-5"></i>
                                                            <span>Edit</span>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn btn-danger" onclick="delete_record({{$row->id}})">
                                                            <i class="ti-trash pdd-right-5"></i>
                                                            <span>Delete</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Wrapper END -->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="confirmDelete" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirm Delete</h4>
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                
            </div>
            <div class="modal-body">
    
                Are you sure you want to delete this testimonial?
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="delete_confirm()"> Confirm</button>
            </div>
        </div>
    </div>
</div> 
<script type="text/javascript"> 
    var delID = 0;
    function delete_confirm(){
        if(delID != 0){
            window.location = APP_URL+'/admin/testimonial/delete/'  + delID;
        }
    } 

    function delete_record(del_id){
        delID = del_id;
        $("#confirmDelete").modal("show");
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
 