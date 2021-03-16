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
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-4">
                <div class="shopproducts-inner text-center">
                    <div class="shopproducts-img">
                        <a href="{{url('product/detail/'.$row->slug)}}">
                            <img src="@if(file_exists(public_path('/products/').$product_images)) {{$prodImageUrl.$product_images}} @else {{asset('front/placeholder.png')}} @endif" alt="shop-img1">
                        </a>
                    </div>
                    
                    @if(Session::get('user_id') != '')
                        <div class="heart-icon  prod_faverty_{{$row->product_id}}">
                            <?php $carFaviorty = commonHelper::checkCarFaviorty($row->product_id);  ?>
                            @if($carFaviorty == 1)
                                <i onclick="saveProdFaviorty('0' , {{$row->product_id}})" class="fa fa-heart" aria-hidden="true"></i>
                            @else
                                <i onclick="saveProdFaviorty('1' , {{$row->product_id}})" class="fa fa-heart-o" aria-hidden="true"></i>
                            @endif
                        </div>
                    @else
                        <div class="heart-icon">
                            <?php $carFaviorty = commonHelper::checkCarFaviorty($row->product_id);  ?>
                            @if($carFaviorty == 1)
                                <i class="fa fa-heart list-btn-login" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-heart-o list-btn-login" aria-hidden="true"></i>
                            @endif
                        </div>
                    @endif
                    <a href="{{url('product/detail/'.$row->slug)}}"><h4>{!!  commonHelper::textLimit($row->product_title , 20) !!}</h4></a>
                    <p class="mb-1">{!!  commonHelper::textLimit($row->categories_title , 20) !!}</p>

                    <h5 class="mb-1">₦{{number_format($row->product_price,2)}}</h5>
                    <section class='rating-widget'>
                        <!-- Rating Stars Box -->
                        <div class='rating-stars'>
                            {!! commonHelper::rating($row->product_id) !!}
                        </div>
                        <div class='success-box'>
                            <div class='text-message'></div>
                        </div>
                    </section>
                    @if($row->status == 0)
                        <div class="outstock_btn">
                            <img class="stock_img" src="{{asset('front/assets/images/out_btn.png')}}">
                        </div>
                    @else
                        <div class="shop-btns">
                           <ul class="list-unstyled">
                               <li class="d-inline-block"><button class="pink-btn @if(session('user_id') == '') list-btn-login @endif" @if(session('user_id') != '') onclick="itemAddToCart('{{$row->product_id}}','{{$row->product_price}}','{{str_replace("'", "", $row->product_title)}}','{{$row->weight}}','Product','1')" @endif>Add to Cart</button></li>
                               <li class="d-inline-block"><button class="book-btn quickview-btn" product_id="{{$row->product_id}}">Quick View</button></li>
                           </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
    <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-4"><p>No record found.</p></div>
    @endif
</div>
{{ $products->onEachSide(1)->links("pagination1") }}
<script>
$(".filterVal").click(function() {
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
    filterPrice = $(this).attr("value");
    filterText = $(this).text();
    $('.filterPrice').attr("value",filterPrice);
    $('.filterPrice').text(filterText);
    $('.loaders').show();
    var url = window.location.href;
    console.log(category);
    console.log(subCategory);
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
</script>