@if(count($foodItems) > 0)                                   
    @foreach($foodItems as $item)
        <?php 
            $product_images = json_decode($item->product_images);
            if(count($product_images) > 0){
                $product_images = $product_images[0]->imagePath;
            }else{
                $product_images = asset('front/placeholder.png');
            }
        ?>
        <div class="row  mb-4 order-table border-bottom pb-3">
            <div class="col-4 col-sm-4 col-md-3 col-lg-3 pr-0">
                <div class="order-img">
                    <a href="{{url('product/detail/'.$item->slug)}}"><img class="img-fluid " src="@if(file_exists(public_path('/products/').$product_images)) {{$prodImageUrl.$product_images}} @else {{asset('front/placeholder.png')}} @endif"></a>
                </div>
            </div>
            <div class="col-8 col-sm-8 col-md-9 col-lg-9 pl-0">
                <div class="order-text">
                    <a href="{{url('product/detail/'.$item->slug)}}"><h4 class="mb-0">{!!  commonHelper::textLimit($item->product_title , 15) !!}</h4></a>
                    <!--<p class="mb-0">face serum...</p>-->
                    <h6>₦{{number_format($item->product_price,2)}}</h6>
                </div>
            </div>
        </div>
<!--        <ul class="list-unstyled mb-4 border-bottom pb-3">
            <li class="d-inline-block"><a href="{{url('product/detail/'.$item->slug)}}"><img class="img-fluid" src="@if(file_exists(public_path('/products/').$product_images)) {{$prodImageUrl.$product_images}} @else {{asset('front/placeholder.png')}} @endif"></a></li>
            <li class="d-inline-block ">
                <a href="{{url('product/detail/'.$item->slug)}}"><h4>{!!  commonHelper::textLimit($item->product_title , 15) !!}</h4></a>
                <p>Face serum & moisturizer Set</p>
                <h6>₦{{number_format($item->product_price,2)}}</h6>
            </li>
        </ul>-->
    @endforeach
@else
    <p>No products found.</p>
@endif