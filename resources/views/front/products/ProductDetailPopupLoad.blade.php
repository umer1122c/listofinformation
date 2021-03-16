<div class="modal-body quickview-inner">
    <div class="row">
        <div class="col-md-6 col-lg-6">
            <div class="shopproducts-img">
                <?php 
                    $product_images = json_decode($product->product_images);
                    if(count($product_images) > 0){
                        $product_images = $product_images[0]->imagePath;
                    }else{
                        $product_images = asset('front/placeholder.png');
                    }
                ?>
                <img src="@if(file_exists(public_path('/products/').$product_images)) {{$prodImageUrl.$product_images}} @else {{asset('front/placeholder.png')}} @endif" alt="shop-img1">
            </div>
            @if(Session::get('user_id') != '')
                <div class="heart-icon  prod_faverty_{{$product->product_id}}">
                    <?php $carFaviorty = commonHelper::checkCarFaviorty($product->product_id);  ?>
                    @if($carFaviorty == 1)
                        <i onclick="saveProdFaviorty('0' , {{$product->product_id}})" class="fa fa-heart" aria-hidden="true"></i>
                    @else
                        <i onclick="saveProdFaviorty('1' , {{$product->product_id}})" class="fa fa-heart-o" aria-hidden="true"></i>
                    @endif
                </div>
            @else
                <div class="heart-icon">
                    <?php $carFaviorty = commonHelper::checkCarFaviorty($product->product_id);  ?>
                    @if($carFaviorty == 1)
                        <i class="fa fa-heart list-btn-login" aria-hidden="true"></i>
                    @else
                        <i class="fa fa-heart-o list-btn-login" aria-hidden="true"></i>
                    @endif
                </div>
            @endif
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="quick-content">
                <h4>{{$product->product_title }}</h4>
                <h5 class="mb-2">{!!  commonHelper::textLimit($category_title , 20) !!}</h5> 
                
                <h5 class="mb-1">₦{{number_format($product->product_price,2)}}</h5>
                <section class='rating-widget'>
                    <!-- Rating Stars Box -->
                    <div class='rating-stars'>
                        {!! commonHelper::rating($product->product_id) !!}
                    </div>
                    <div class='success-box'>
                        <div class='text-message'></div>
                    </div>
                </section>
                <ul class="list-unstyled">
                    
                    <li class="d-inline-block">
                        <div class='main'>
                            <button class='down_count  btn-black' title='Down'><i class="fa fa-minus" aria-hidden="true"></i></button>
                            <input class='counter product_qty' type="text" value='1' />    
                            <button class='up_count  btn-black' title='Up'><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                    </li>
                    <li class="d-inline-block"><span class="pink-btn @if(session('user_id') == '') list-btn-login @endif" @if(session('user_id') != '') onclick="itemAddToCart('{{$product->product_id}}','{{$product->product_price}}','{{str_replace("'", "", $product->product_title)}}','{{$product->weight}}','Product','1')" @endif>Add to Cart</span></li>
                </ul>
                <?php echo commonHelper::textLimit($product->product_description,200); ?>
            </div>
        </div>
    </div>
  </div>
<script>
    $(document).ready(function(){
        $('button').click(function(e){
            var button_classes, value = +$('.counter').val();
            button_classes = $(e.currentTarget).prop('class');        
            if(button_classes.indexOf('up_count') !== -1){
                value = (value) + 1;            
            } else {
                value = (value) - 1;            
            }
            value = value < 1 ? 1 : value;
            $('.counter').val(value);
        });  
        $('.counter').click(function(){
            $(this).focus().select();
        });
    });
</script>