@extends('admin.template')
@section('content')
<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid11">
        <div class="container">
            <div class="card">
                <div class="pdd-vertical-5 pdd-horizon-10 border bottom print-invisible">
                    <div class="pull-left">
                        <h2>ORDER ID {{$orders->order_id}}</h2>
                    </div>
<!--                    <ul class="list-unstyle list-inline text-right">
                        <li class="list-inline-item">
                            <a href="" class="btn text-gray text-hover display-block padding-10 no-mrg-btm" onclick="window.print();">
                                <i class="ti-printer text-info pdd-right-5"></i>
                                <b>Print</b>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="" class="text-gray text-hover display-block padding-10 no-mrg-btm">
                                <i class="fa fa-file-pdf-o text-danger pdd-right-5"></i>
                                <b>Export PDF</b>
                            </a>
                        </li>
                    </ul>-->
                </div>
                <div class="card-body">
                    <div class="">
                        <div class="row">
                            <div class="col-md-8 col-sm-8">
                                <h3 class="">Customer Detail:</h3>
                                <address class="mrg-top-10">
                                    <b class="text-dark">{{$first_name.' '.$last_name}}</b><br>
                                    <b class="text-dark">{{$email}}</b><br>
                                    <b class="text-dark">{{$phone}}</b><br>
                                    @if($phone_number != '')
                                        <b class="text-dark">{{$phone_number}}</b><br>
                                    @endif
                                    @if($is_product == 1)
                                        <b class="text-dark">{{$address}}. </b>
                                    @endif
                                    @if($is_service == 1)
                                       <br> <b class="text-dark">10 Asenuga Street, off Opebi Link Rd, Opebi, Ikeja </b>
                                    @endif
                                </address>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="">
                                    <div class="text-dark text-uppercase inline-block"><b>ORDER ID :</b></div>
                                    <div class="pull-right">{{$orders->order_id}}</div>
                                </div>
                                <div class="">
                                    <div class="text-dark text-uppercase inline-block"><b>Date :</b></div>
                                    <div class="pull-right">{{$DateTime}}</div>
                                </div>
                                @if($is_product == 1)
                                    <div class="">
                                        <div class="text-dark text-uppercase inline-block"><b>Delivery Date :</b></div>
                                        <div class="pull-right">{{date('Y-m-d H:i A' , strtotime($orders->dilivery_date))}}</div>
                                    </div>
                                @endif
                                @if($is_service == 1)
                                    <div class="">
                                        <div class="text-dark text-uppercase inline-block"><b>Appoinment Date :</b></div>
                                        <div class="pull-right">{{date('Y-m-d h:i A' , strtotime($orders->app_datetime))}}</div>
                                    </div>
                                @endif
                                <div class="">
                                    <div class="text-dark text-uppercase inline-block"><b>Coupon Code :</b></div>
                                    <div class="pull-right">{{$orders->coupon_code}}</div>
                                </div>
                                
                            </div>
                        </div>
                        
                        <div class="row mrg-top-20">
                            <div class="col-md-12">
                                <table class="table table-hover  table-responsive w-100 d-block d-md-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Items</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($transaction_items)
                                            <?php 
                                                $count = 1;
                                                $grandTotal = 0;
                                            ?>
                                            @foreach($transaction_items as $item)
                                                <tr>
                                                    <td>{{$count}}</td>
                                                    <td>
                                                        {{$item->product_title}} <br>
                                                        
                                                        
                                                    </td>
                                                    
                                                    <td>{{$item->quantity}}</td>
                                                    <td>₦{{$item->price}}</td>
                                                    <td>
                                                        <form action="" method="POST">
                                                            {!! csrf_field() !!}
                                                            <input type="hidden" name="item_id" value="{{$item->id}}">
                                                            <select name="delivery_status" id="delivery_status" onchange="this.form.submit()">
                                                                <option value="Pending" <?php if($item->delivery_status == 'Pending'){ ?> selected="" <?php } ?>>Pending</option>
                                                                <option value="Delivered" <?php if($item->delivery_status == 'Delivered'){ ?> selected="" <?php } ?>>Delivered</option>
                                                                <option value="Unavailable" <?php if($item->delivery_status == 'Unavailable'){ ?> selected="" <?php } ?>>Unavailable</option>
                                                                <option value="Refund" <?php if($item->delivery_status == 'Refund'){ ?> selected="" <?php } ?>>Refund</option>
                                                                <option value="In Progress" <?php if($item->delivery_status == 'In Progress'){ ?> selected="" <?php } ?>>In Progress</option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <?php $productPrice = $item->price * $item->quantity;?>
                                                    <td class="text-right">₦{{number_format($productPrice , 2)}}</td>
                                                </tr>
                                                <?php 
                                                    $grandTotal = $grandTotal + $productPrice;
                                                    $count++;
                                                ?>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <div class="row mrg-top-30">
                                    <div class="col-md-12">
                                        <div class="pull-right text-right">
                                            <p>Sub - Total amount: ₦{{number_format($grandTotal,2)}}</p>
                                            <p>Shipping Cost : ₦{{number_format($orders->shipping_cost,2)}} </p>
                                            <p>Discount : ₦{{number_format($orders->discount_amount,2)}} </p>
                                            <hr>
                                            <h3><b>Total :</b> ₦{{number_format($grandTotal + $orders->shipping_cost - $orders->discount_amount,2)}}</h3>
                                        </div>
                                    </div>
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
@endsection