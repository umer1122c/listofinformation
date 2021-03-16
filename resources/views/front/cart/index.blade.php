@extends('front.template')
@section('content')
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
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
    <!------ CART-WRAPPER-SECTION-START ------>
        <section class="cart-wrapper">
            <div class="container">
                <div class="row justify-content-center left-head">
                    <div class="col-md-12 col-lg-12">
                        <div class="main-heading">
                            <h3><span class="pink-clr">Welcome to</span> Cart</h3>
                        </div>
                    </div>
                </div>
                <div class="loaders" style="display:none; margin: auto; width: 100%;text-align: center;">
                    <img src="{{asset('front/loader.gif')}}" alt="" class="img-fluid" style="width: 50px;"/>
                </div>
                @if(count($items) > 0)
                    <?php $total = 0; ?>
                    <div class="order-table">
                        @foreach($items as $row)

                            <?php 
                                if($row->type == 'Product'){
                                    $product_images = json_decode($row->product_images);
                                    if(count($product_images) > 0){
                                        $product_images = $product_images[0]->imagePath;
                                    }else{
                                        $product_images = asset('front/placeholder.png');
                                    }
                                }else{
                                    $product_images = '';
                                }
                            ?>
                            <div class="row box-shadow mb-4 rowCount{{$row->cart_id}}">
                                <div class="col-4 col-sm-4 col-md-3 col-lg-2 pr-0">
                                    <div class="order-img">
                                        @if($row->type == 'Product')
                                            <img class="shape " src="@if(file_exists(public_path('/products/').$product_images)) {{$prodImageUrl.$product_images}} @else {{asset('front/placeholder.png')}} @endif">
                                        @else
                                            <img class="shape " src="@if(file_exists(public_path('/service_categories/').$row->product_images)) {{$serviceImageUrl.$row->image}} @else {{asset('front/placeholder.png')}} @endif">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 col-sm-5 col-md-7 col-lg-6 pl-0">
                                    <div class="order-text">
                                        <h4 class="mb-3">{{$row->name }}</h4>
                                        @if($row->type == 'Product')
                                            <div class='main'>
                                                <button class='down_count  btn-black' title='Down' onclick="decrease('{{$row->item_id}}' , '{{$row->cart_id}}')"><i class="fa fa-minus" aria-hidden="true"></i></button>
                                                <input class='counter qty count_{{$row->cart_id}}' type="text" placeholder="value..." value='{{$row->qty}}' />    
                                                <button class='up_count  btn-black' title='Up' onclick="increase('{{$row->item_id}}' , '{{$row->cart_id}}')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                            </div>
<!--                                                <div class="number">
                                                <span class="minus" onclick="decrease('{{$row->item_id}}' , '{{$row->cart_id}}')"><i class="fa fa-minus" aria-hidden="true"></i></span>
                                                <input type="text" class='qty count_{{$row->cart_id}}' value="{{$row->qty}}"/>
                                                <span class="plus" onclick="increase('{{$row->item_id}}' , '{{$row->cart_id}}')"><i class="fa fa-plus" aria-hidden="true"></i></span>
                                            </div>-->
                                        @else
                                            <h6 class="pink-clr">{!!  commonHelper::getServices($row->cart_id) !!}</h6>
                                            <p  class="book-clr">Services</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-2 col-sm-3 col-md-2 col-lg-4">
                                    <div class="float-right price-text">
                                        <p class="text-right">
                                            <span class="black-clr">₦{{number_format($row->price,2)}}</span>
                                        </p>
                                        <a href="javascript:void(0)" class="close-bg"><span type="button" class="close" data-toggle="modal" data-target="#removePayment" onclick="deleteCartItem('{{$row->cart_id}}')" >&times;</span></a>
                                    </div>
                                </div>
                            </div>
                            <?php $total = $total + $row->qty * $row->price; ?>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12 float-right">
                            <div class="cart-bottom">
                                <div class="price-bg">
                                    <strong class="sub-head">Subtotal</strong>
                                    <strong class="price-head">₦ <span class="price-total">{{number_format($total , 2)}}</span></strong>
                                </div>
                                <div class="price-bg">
                                    <strong class="sub-head">Total</strong>
                                    <strong class="price-head">₦ <span class="price-total">{{number_format($total , 2)}}</span></strong>
                                </div>
                                <div class="price-btn">
                                    <a href="{{url('/checkout')}}"><button class="pink-btn mb-4">Proceed to Checkout</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12 col-lg-12"><p>Cart empty.</p></div>
                    </div>
                @endif
            </div>
        </section>
    <!------ CART-WRAPPER-SECTION-END ------>
    
    <!---- FORGOT-PASSWORD-START ----->
    <div class="modal fade" id="removePayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Remove Product</h5>
                    <span type="text" class="close" data-dismiss="modal">&times;</span>
                </div>
                <div class="modal-body py-4">
                  <span>Are you sure you want to delete this item?</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn_primery" data-dismiss="modal">Close</button>
                    <button type="button" class="btn sub-btn" data-dismiss="modal" onclick="deleteCartItemConfirm()">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <!---- FORGOT-PASSWORD-END ----->
<script>
    var APP_URL = {!! json_encode(url('/')) !!};
    var CartID = 0;
    function deleteCartItemConfirm(){
        $.ajax({
            type: 'GET',
            url: APP_URL+'/item/cart/deleteItem/'+CartID,
            data: '',
            processData: false,
            contentType: false,
            success: function (msg) {
                if(msg.status === "success"){
                    $('.rowCount'+CartID).remove();
                    $('.price-total').html(msg.total);
                    $('#cartCount').val(msg.cartCount);
                    $('.cartCount').html(msg.cartCount);
                    $('.success_message').html(msg.message);
                    $('.show_message_success').show();
                    $(".alert-success").delay(3000).fadeOut();
                }else{
                    $('.success_message').html(msg.message);
                    $('.show_message_success').show();
                    $(".alert-success").delay(3000).fadeOut();
                }
            }
        });
    }
    
    function deleteCartItem(id,qty){
        CartID = id;
        $('#removePayment').show();
    }
    
    function increase(id , rowId){
        $('.loaders').show();
        $('.count_'+rowId).val(parseInt($('.count_'+rowId).val()) + 1 );
        var qty = $('.count_'+rowId).val();
        $.ajax({
            type: 'GET',
            url: APP_URL+'/item/cart/updateItem/'+rowId+'/'+qty,
            data: '',
            processData: false,
            contentType: false,
            success: function (msg) {
                $('.loaders').hide();
                if(msg.status === "success"){
                    $('.price-total').html(msg.total);
                    $('.success_message').html(msg.message);
                    $('.show_message_success').show();
                    $(".alert-success").delay(3000).fadeOut();
                }else{
                    $('.success_message').html(msg.message);
                    $('.show_message_success').show();
                    $(".alert-success").delay(3000).fadeOut();
                }
            }
        });
    }

    function decrease(id , rowId){
        var initCount = $('.count_'+rowId).val();
        $('.count_'+rowId).val(parseInt($('.count_'+rowId).val()) - 1 );
        if ($('.count_'+rowId).val() == 0) {
            $('.count_'+rowId).val(1);
        }
        var qty = $('.count_'+rowId).val();
        if(initCount > 1){
            $('.loaders').show();
            $.ajax({
                type: 'GET',
                url: APP_URL+'/item/cart/updateItem/'+rowId+'/'+qty,
                data: '',
                processData: false,
                contentType: false,
                success: function (msg) {
                    $('.loaders').hide();
                    if(msg.status === "success"){
                        $('.price-total').html(msg.total);
                        $('.success_message').html(msg.message);
                        $('.show_message_success').show();
                        $(".alert-success").delay(3000).fadeOut();
                    }else{
                        $('.success_message').html(msg.message);
                        $('.show_message_success').show();
                        $(".alert-success").delay(3000).fadeOut();
                    }
                }
            });
        }
    }
</script>
@endsection