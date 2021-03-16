<div class="categery-ryt">
    @if(count($courses) > 0)
        @foreach($courses as $row)
            <div class="row mb-4">
                <div class="col-12 col-sm-5  col-md-5 col-lg-4">
                    <div class="categery-img">
                        <a href="{{url('course/detail/'.$row->slug)}}">
                            <img src="@if(file_exists(public_path('/courses/').$row->course_image)) {{$courseImageUrl.$row->course_image}} @else {{asset('front/placeholder.png')}} @endif" alt="course-list">
                            <div class="price-text">
                                <p>${{$row->price}}</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-sm-7 col-md-7 col-lg-8">
                    <div class="categery-content">
                        <h5>
                            <a href="{{url('course/detail/'.$row->slug)}}">
                                {!!  commonHelper::textLimit($row->course_title , 40) !!}
                            </a>
                        </h5>
                        <div class="cour-text">
                            <p>{!!  commonHelper::textLimit($row->course_description , 200) !!}</p>
                            <ul class="list-unstyled btn_bg">
                                <li class="d-inline-block"><a href="javascript:void(0)" onclick="itemAddToCartSession('{{$row->course_id}}','{{$row->price}}','{{$row->course_title}}','1','0')" class="add-btn">Add to Cart</a></li>
                                <li class="d-inline-block"><a href="javascript:void(0)" onclick="itemAddToCartSession('{{$row->course_id}}','{{$row->price}}','{{$row->course_title}}','1','1')" class="blakc-btn">Buy Now</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>  
        @endforeach
    @else
        <div class="col-12 col-sm-7 col-md-7 col-lg-8"><p>No record found.</p></div>
    @endif
</div>
{{ $courses->onEachSide(1)->links("pagination1") }}
<!--<nav aria-label="Page navigation example pagination-wrapper">
    <ul class=" pagination justify-content-center">
        <li class="page-item active">
            <a class="page-link" href="#" aria-label="Previous">
                <span aria-hidden="true">«</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        <li class="page-item "><a class="page-link " href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item">
            <a class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">»</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</nav>-->
