<section class="after-before-product position-relative mb-5 listing-slider mt-5 realtedproducts-bg">
    <div class="container">
        <div class="row justify-content-center left-head">
            <div class="col-md-12 col-lg-12">
                <div class="main-heading ">
                    <h3 class="font-weight-bold mb-0 text-uppercase">RELATED PRODUCTS</h3>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                @if(count($related_products) > 0)
                    <div id="openpallyslider1">
                        <div class="arrows">
                            <button class="carousel-control-prev MS-left" href="#" data-slide="prev">
                                <i class="fa fa-angle-left" aria-hidden="true"></i>
                            </button>
                            <button class="carousel-control-next MS-right" href="#" data-slide="next">
                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="MS-content">
                            @foreach($related_products as $row_rec)
                                <?php 
                                    $product_images = json_decode($row_rec->product_images);
                                    if(count($product_images) > 0){
                                        $product_images = $product_images[0]->imagePath;
                                    }else{
                                        $product_images = asset('front/placeholder.png');
                                    }
                                ?>
                                <div class="item">
                                    <div class="openpally-inner2 pally-inner">
                                        <a href="{{url('product/detail/'.$row_rec->slug)}}">
                                            <img class="img-fluid product-width shape " src="@if(file_exists(public_path('/products/').$product_images)) {{$prodImageUrl.$product_images}} @else {{asset('front/placeholder.png')}} @endif" alt="recentblog1">
                                        </a>
                                        <div class="feature">
                                            <p>{!!  commonHelper::getProductOff($row_rec->bulk_price , $row_rec->product_price) !!}</p>
                                        </div>
                                        @if(Session::get('user_id') != '')
                                            <div class="heart-icon prod_faverty_{{$row_rec->product_id}}">
                                                <?php $carFaviorty = commonHelper::checkCarFaviorty($row_rec->product_id);  ?>
                                                @if($carFaviorty == 1)
                                                    <i onclick="saveProdFaviorty('0' , {{$row_rec->product_id}})" class="fa fa-heart" aria-hidden="true"></i>
                                                @else
                                                    <i onclick="saveProdFaviorty('1' , {{$row_rec->product_id}})" class="fa fa-heart-o" aria-hidden="true"></i>
                                                @endif
                                            </div>
                                        @else
                                            <div class="heart-icon">
                                                <?php $carFaviorty = commonHelper::checkCarFaviorty($row_rec->product_id);  ?>
                                                @if($carFaviorty == 1)
                                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                                @else
                                                    <i class="fa fa-heart-o" aria-hidden="true"></i>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="pally-content">
                                            <div class="pally-text">
                                                <p class="mb-1">
                                                    <a href="{{url('product/detail/'.$row_rec->slug)}}">{!!  commonHelper::textLimit($row_rec->product_title , 20) !!}</a>
                                                </p>
                                                <h6>₦{{number_format($row_rec->product_price,2)}}</h6>
                                            </div>
                                            <ul class="list-unstyled mb-2">

                                                <li class="mb-2 li-h">
                                                    @if($row_rec->is_season == 0)
                                                        <a href="javascript:void(0);" class="grey-btn">In Season</a>
                                                    @elseif($row_rec->is_season == 1)
                                                        <a href="javascript:void(0);" class="grey-btn">Off Season</a>  
                                                    @endif
                                                </li>
                                                <li class="star-clr ">
                                                    {!! commonHelper::rating($row_rec->product_id) !!}
                                                    <span>({!! commonHelper::ratingCount($row_rec->product_id) !!}/5.0)</span>
                                                </li>
                                            </ul>
                                            <button class="list-btn1 yellow-bg @if(session('user_id') == '') list-btn-login @else order-type-btn-popup @endif" @if(session('user_id') != '') data-toggle="modal" data-target="#openpallyModal" @endif product_id="{{$row_rec->product_id}}">ORDER NOW</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="col-md-5 col-lg-5">
                        <p>No record found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>