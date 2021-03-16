<div class="row">
    @if(count($products) > 0)
        @foreach($products as $row)
            <?php 
                $product_images = json_decode($row->product_images);
                if(count($product_images) > 0){
                    $product_images = $product_images[0]->imagePath;
                }else{
                    $product_images = asset('front/placeholder.png');
                }
            ?>
            <div class="col-md-4 col-lg-4 mb-3">
                <div class="listing-inner">
                    <img class="img-fluid shape" src="@if(file_exists(public_path('/products/').$product_images)) {{$prodImageUrl.$product_images}} @else {{asset('front/placeholder.png')}} @endif" alt="fruit1">
                    <div class="feature">
                        <p>{!!  commonHelper::getProductOff($row->bulk_price , $row->product_price) !!}</p>
                    </div>
                    <div class="heart-icon">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                    </div>
                    <div class="listing-content pl-4 pr-4">
                        <p class="mb-1">{!!  commonHelper::textLimit($row->product_title , 15) !!}</p>
                        <h6>₦{{number_format($row->product_price,2)}} per {{$row->product_unit}}</h6>
                        <ul class="list-unstyled">
                            <li class="start-clr">
                                {!! commonHelper::rating($row->product_id) !!}
                                <span>({!! commonHelper::ratingCount($row->product_id) !!}/5.0)</span>
                            </li>
                        </ul>
                        <button  class="list-btn" data-toggle="modal" data-target="#openpallyModal">Select Order Type</button>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
{{ $products->links("pagination1") }}