<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Order;
use App\Models\ServiceCategory;
use App\Models\Product;
use App\Models\Copoun;
use App\Models\CouponUser;
use App\Models\OrderDetail;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Mail;
use URL;
class OrderControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function index(Request $request)
    {
        $data['title'] = 'My Orders';
        $data['class'] = 'orders';
        $data['prodImageUrl'] = URL::to('').'/products/';
        $user_id = Session::get('user_id');
        $data['orders'] = DB::table('orders')->select('orders.order_id','orders.order_total','orders.shipping_cost','orders.discount_amount','orders.created_at','orders.dilivery_date','orders.status as order_status')
                            ->where('orders.user_id' , $user_id)
                            //->where('orders.status' , '!=' , 'Pending')
                            ->orderby('orders.created_at' , 'desc')
                            ->paginate(10);
        if ($request->ajax()) {
            return view('front/dashboard/ordersLoad',$data)->render(); 
        }
        return view('front/dashboard/orders',$data);
    }
    
    public function orderDetail($order_id = '')
    {
        $data['title'] = 'Order Detail';
        $data['class'] = 'orders';
        $prodImageUrl = URL::to('').'/products/';
        $serviceImageUrl = URL::to('').'/service_categories/';
        $is_product = 0;
        $is_service = 0;
        $orders = Order::select('order_id','order_total','shipping_cost','address','coupon_code','discount_amount','status','created_at as order_date','dilivery_date','app_datetime')->where('order_id' , $order_id)->first();
        //dd($products);
        $orderArray = [];
        if($orders){
            //return $orders->address;
            $address = DB::table('user_address')
                            ->select('user_address.*','areas.*')
                            ->join('areas', 'areas.id', '=', 'user_address.area_id' , 'left')
                            ->where('user_address.address_id' , $orders->address)
                            ->first();
            //dd($address);
            if($address){
                $orders->delivery_address = $address->lable.' '.$address->house_name.' '.$address->street.' '.$address->town.' '.$address->name;
                $orders->phone_number = $address->country_code.' '.$address->phone_number;
            }
            $orders->order_date = date('M d Y h:i A' , strtotime($orders->order_date));
            $order_details = DB::table('order_details')->select('order_id','type_id','name' , 'quantity' , 'price','type','delivery_status')->where('order_id' , $orders->order_id)->get();
            //$orders->order_details = $order_details;
            foreach($order_details as $row){
                if($row->type == 'Product'){
                    $is_product = 1;
                    $products = Product::select('product_images')->where('product_id' , $row->type_id)->first();
                    //dd($products);
                    if($products){
                        $product_images = json_decode($products->product_images);
                        if(count($product_images) > 0){
                            $row->product_image = $prodImageUrl.$product_images[0]->imagePath;
                        }else{
                            $row->product_image = asset('front/placeholder.png');
                        }
                    }else{
                        $row->product_image = asset('front/placeholder.png');
                    }
                }else{
                    $is_service = 1;
                    $service = ServiceCategory::where('service_category_id' , $row->type_id)->first();
                    if($service->image){
                        $row->product_image = $serviceImageUrl.$service->image;
                    }else{
                        $row->product_image = asset('front/placeholder.png');
                    }
                }
                //return $row->product_image;
                $orderArray[] = $row;
            }
            
            $orders->order_details = $orderArray;
            $data['order_detail'] = $orders;
            $data['is_service'] = $is_service;
            $data['is_product'] = $is_product;
            //dd($orderArray);
            $data['item_array'] = $orderArray;
            return view('front/dashboard/orderDetail' , $data);
        }else{
            return redirect('my/orders');;
        }
    }
    
    
    public function applyCouponCode($code= '',$amount = ''){
        $user_id = Session::get('user_id');
        $Coupon = Copoun::where('code',$code)->where('status',0)->first();
        if($Coupon){
            $userCount = CouponUser::where('user_id' , $user_id)->where('coupon_code',$code)->count();
            //return $Coupon->no_of_time;
            if($userCount < $Coupon->no_of_time){
                if($amount <= $Coupon->min_price){
                    return response()->json(['status'=>"failed","message"=>"Cart amount is not sufficent for this coupon code. Minimum amount -".$Coupon->min_price],200);
                }
                $discount = $Coupon->discount;
                $totalDiscount = $amount * $discount /100; 
                if($totalDiscount >= $Coupon->max_price_applay){
                    $totalDiscount = $Coupon->max_price_applay;
                }
                return response()->json(['status'=>"success","message"=>"Coupon Applied successfully.",'discount' => round($totalDiscount,2)],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"failed","message"=>"Coupon code hass been expaired for this user.","user_access" => 0],200);
            }
        }else{
            return response()->json(['status'=>"failed","message"=>"Coupon code is incorrect.","user_access" => 0],200);
        }  
    }
}
