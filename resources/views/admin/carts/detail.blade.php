@extends('admin.template')
@section('content')
<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        <div class="container">
            <div class="card">
                <div class="pdd-vertical-5 pdd-horizon-10 border bottom print-invisible">
                    <div class="pull-left">
                        <h2>User ID {{$cart_items[0]->user_id}}</h2>
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
                    <div class="pdd-horizon-30">
                        <div class="row mrg-top-20">
                            <div class="col-md-8 col-sm-8">
                                <h3 class="pdd-left-10 mrg-top-10">Customer Detail:</h3>
                                <address class="pdd-left-10 mrg-top-10">
                                    <b class="text-dark">{{$cart_items[0]->first_name.' '.$cart_items[0]->last_name}}</b><br>
                                    <b class="text-dark">{{$cart_items[0]->email}}</b><br>
                                    <b class="text-dark">{{$cart_items[0]->phone}}</b><br>
                                </address>
                            </div>
<!--                            <div class="col-md-4 col-sm-4">
                                <div class="mrg-top-20">
                                    <div class="text-dark text-uppercase inline-block"><b>User ID :</b></div>
                                    <div class="pull-right">{{$cart_items[0]->user_id}}</div>
                                </div>
                            </div>-->
                        </div>
                        
                        <div class="row mrg-top-20">
                            <div class="col-md-12">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Items</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($cart_items) > 0)
                                            <?php 
                                                $count = 1;
                                            ?>
                                            @foreach($cart_items as $item)
                                                <tr>
                                                    <td>{{$count}}</td>
                                                    <td>
                                                        {{$item->name}} <br>
                                                        @if($item->attribute_id != 0)
                                                            ({{$item->attribute_name.' - '.$item->attribute_cost}})
                                                        @endif
                                                    </td>
                                                    @if($item->type == 'pally' || $item->type == 'Close' || $item->type == 'Open')
                                                        <td>{{commonHelper::getPallyType($item->pally_id)}}</td>
                                                    @elseif($item->type == 'normal')
                                                        <td>Normal</td>
                                                    @else
                                                        <td>{{commonHelper::getPallyType($item->pally_id)}}</td>
                                                    @endif
                                                    <td>{{$item->qty}}</td>
                                                    <td>₦{{$item->price}}</td>
                                                    
                                                    <?php $productPrice = $item->price * $item->qty;?>
                                                    <td class="text-right">₦{{number_format($productPrice , 2)}}</td>
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
</div>
<!-- Content Wrapper END -->
@endsection