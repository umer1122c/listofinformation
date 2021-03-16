<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;

use App\Models\Area;
use App\Models\UserAddress;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\OrderServiceItem;
use App\Models\CartServiceItem;
use App\Models\CartItem;
use App\Models\Copoun;
use App\Models\CouponUser;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Mail;
use Illuminate\Support\Facades\URL;

class OrderController extends Controller
{
    
    public function GetOrders(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'offset' => 'required'
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $offset = request()->offset + 20;
            $prodImageUrl = URL::to('').'/products/';
            $serviceImageUrl = URL::to('').'/service_categories/';
            $orders = Order::select('order_id','shipping_cost','order_total','status as order_status','created_at as order_date')
                        ->where('user_id' , $user->user_id)
                        //->where('status' , '!=' , 'Pending')
                        ->orderby('created_at','desc')
                        ->skip(request()->offset)->take(20)
                        ->get();
            //dd($products);
            $orderArray = [];
            if(count($orders) > 0){
                foreach($orders as $row){
                    $order_id = $row->order_id;
                    $row->order_total = $row->order_total + $row->shipping_cost;
                    $row->no_of_items = DB::table('order_details')->where('order_id' , $order_id)->count();
                    $order_details = DB::table('order_details')->where('order_id' , $order_id)->first();
                    if($order_details){
                        $type_id = $order_details->type_id;
                        $row->product_title = $order_details->name;
                        $row->type = $order_details->type;
                        if($row->type == 'Product'){
                            
                            $product = Product::where('product_id' , $type_id)->first();
                            if($product){
                                $product_images = json_decode($product->product_images);
                                $row->product_image = $prodImageUrl.$product_images[0]->imagePath;
                            }else{
                                $row->product_image = '';
                            }
                        }else{
                            $service = ServiceCategory::where('service_category_id' , $type_id)->first();
                            if($service){
                                $row->product_image = $serviceImageUrl.$service->image;
                            }else{
                                $row->product_image = asset('front/placeholder.png');
                            }
                            
                        }
                    }else{
                        $row->product_image = '';
                    }
                    $row->order_date = date('d-m-Y' , strtotime($row->order_date));
                    $orderArray[] = $row;
                }
                return ['status'=>true,"message"=>"Orders data found.","user_access"=>request()->user()->user_access, 'offset' => $offset,"orders" => $orderArray];
            }else{
                return ['status'=>true,"message"=>"Orders data found.","user_access"=>request()->user()->user_access, 'offset' => 0,"orders" => $orderArray];
            }
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function OrderDetail(Request $request){
        try{
            $prodImageUrl = URL::to('').'/products/';
            $serviceImageUrl = URL::to('').'/service_categories/';
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'order_id' => 'required'
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $prodImageUrl = URL::to('').'/products/';
            
            $orders = Order::select('order_id','shipping_cost','address as delivery_address','status as order_status','coupon_code','discount_amount','app_datetime as appoinment_date','created_at as order_date','dilivery_date')->where('order_id' , request()->order_id)->first();
            //dd($products);
            $orderArray = [];
            if($orders){
                $address = DB::table('user_address')
                                ->select('user_address.*','areas.*')
                                ->join('areas', 'areas.id', '=', 'user_address.area_id' , 'left')
                                ->where('address_id' , $orders->delivery_address)
                                ->first();
                if($address){
                    $orders->delivery_address = $address->lable.' '.$address->house_name.' '.$address->street.' '.$address->town.' '.$address->name;
                    $orders->delivery_phone = $address->country_code.' '.$address->phone_number;
                }else{
                    $orders->delivery_address = "";
                    $orders->delivery_phone = "";
                }
                $orders->order_date = date('d-m-Y | h:i A' , strtotime($orders->order_date));
                $orders->dilivery_date = date('l, jS F Y' , strtotime($orders->dilivery_date));
                $orders->appoinment_date = date('l, jS F Y h:i A' , strtotime($orders->appoinment_date));
                $order_details = DB::table('order_details')->select('id','order_id','type_id','name' , 'quantity' , 'price','type','delivery_status')->where('order_id' , request()->order_id)->get();
                //$orders->order_details = $order_details;
                foreach($order_details as $row){
                    if($row->type == 'Product'){
                        $products = Product::select('product_images','product_title')->where('product_id' , $row->type_id)->first();
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
                $orders->products = $orderArray;
                return ['status'=>true,"message"=>"Orders data found.","user_access"=>request()->user()->user_access,"orders" => $orders];
            }else{
                return ['status'=>true,"message"=>"No data found.","user_access"=>request()->user()->user_access,"orders" => $orders];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function MakeOrder(Request $request)
    {
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $user = $request->user();
            //$productsArray = $jsonrequest['products_list'];
            $productsArray = CartItem::where('user_id' , $user->user_id)->get();
            if(count($productsArray) > 0){
                $order_id = substr(str_shuffle('123456789123456789123456789321654987'),0,6);
                Session::put('order_id' , $order_id);
                $total_amount = $request->amount;
                $transArray = [];
                $transArray['order_id'] = $order_id;
                $transArray['deviceType'] = $jsonrequest['device_type'];
                $transArray['user_id'] = $user->user_id;
                $transArray['status'] = 'Pending';
                $transArray['order_total'] = $jsonrequest['amount'];
                $transArray['address'] = $jsonrequest['address_id'];
                $transArray['shipping_cost'] = $jsonrequest['shipping_cost'];
                $transArray['coupon_code'] = $jsonrequest['coupon_code'];
                $transArray['discount_amount'] = $jsonrequest['discount_amount'];
                $transArray['app_datetime'] = $jsonrequest['booking_datetime'];
                $transArray['created_at'] = date('Y-m-d H:i:s');
                $transArray['updated_at'] = date('Y-m-d H:i:s');
                Order::insert($transArray);
                foreach($productsArray as $row){
                    //dd($row);
                    $itemsArray = [];
                    $itemsArray['order_id'] = $order_id;
                    $itemsArray['type_id'] = $row['type_id'];
                    $itemsArray['name'] = $row['name'];
                    $itemsArray['quantity'] = $row['qty'];
                    $itemsArray['price'] = $row['price'];
                    $itemsArray['type'] = $row['type'];
                    DB::table('order_details')->insert($itemsArray);
                    if($row['type'] == 'Service'){
                        $serviceArray = CartServiceItem::where('cart_id' , $row->cart_id)->get();
                        if(count($serviceArray) > 0){
                            foreach($serviceArray as $row){
                                $serviceArrayNew[] = [
                                    "order_id"  => $order_id,
                                    "service_id"  => $row->service_id,
                                    "service_name"  => $row->service_name,
                                    "price"  => $row->price
                                ];
                            }
                            OrderServiceItem::insert($serviceArrayNew);
                        }
                    }
                }
                
                $totalAmount = $jsonrequest['amount'] + $jsonrequest['shipping_cost'] - $jsonrequest['discount_amount'];
                
                $responce = $this->getPaystackLink($totalAmount,$user->email);
                if($responce['status'] == 'success'){
                    $result = json_decode($responce['data']);
                    $paystack_info = $result->data;
                    $reference = $paystack_info->reference;
                    DB::table('orders')->where('order_id' , $order_id)->update(['reference' => $reference]);
                    $data_res['payment_url'] = $result->data->authorization_url;
                    $data_res['order_id'] = $order_id;
                    return ['status'=>true,"message"=>"Authorization URL not created.","user_access"=>request()->user()->user_access,"order_detail" => $data_res];
                }else{
                    return ['status'=>false,"message"=>"Authorization URL not created.","user_access"=>request()->user()->user_access,"order_detail" => $data_res];
                }
                
            }else{
                return ['status'=>false,"message"=>"No items found in cart.","user_access"=>1];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function getPaystackLink($totalAmount,$email){
        //return $email;
        $settings = Setting::where('settings_id' , 1)->first();
        //dd($settings);
        if($settings){
            if($settings->payment_mod == 1){
                config(['paystack.publicKey' => $settings->PAYSTACK_PUBLIC_KEY_LIVE]);
                config(['paystack.secretKey' => $settings->PAYSTACK_SECRET_KEY_LIVE]);
                $secretKey = $settings->PAYSTACK_SECRET_KEY_LIVE;
            }else{
                config(['paystack.publicKey' => $settings->PAYSTACK_PUBLIC_KEY_LOCAL]);
                config(['paystack.secretKey' => $settings->PAYSTACK_SECRET_KEY_LOCAL]);
                $secretKey = $settings->PAYSTACK_SECRET_KEY_LOCAL;
            }
        }
        $PAYSTACK_SECRET_KEY = $secretKey;
        $reference = Str::random(20);
        $totalAmount = $totalAmount * 100;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"reference\": \"$reference\", \"amount\": $totalAmount, \"email\": \"$email\"}",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $PAYSTACK_SECRET_KEY",
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 1d2ef98e-04b2-aaee-ec4a-d2c00261b591"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return array('status' => 'failed','data' => $err);
        } else {
            return array('status' => 'success','data' => $response);
        }
    }
    
    public function order_send_email($email, $user_name, $email_subject, $email_from,$order_id,$dilivery_date,$shipping_cost,$discount_amount,$wallet_amount, $delivery_address1,$delivery_address2,$link,$view_name) {
        $res['userName'] = $user_name;
        $res['orders'] = $link;
        $res['shipping_cost'] = $shipping_cost;
        $res['delivery_address1'] = $delivery_address1;
        $res['delivery_address2'] = $delivery_address2;
        $res['order_id'] = $order_id;
        $res['discount_amount'] = $discount_amount;
        $res['wallet_amount'] = $wallet_amount;
        $res['dilivery_date'] = $dilivery_date;
        $res['url'] = url('/');
        //echo '<pre>'; print_r($res['orders']);exit;
        Mail::send('email/'.$view_name , $res, function ($message) use ($email_from, $email, $user_name, $email_subject) {
            $message->from($email_from, $name = 'Pricepally');
            $message->to($email, $user_name)->subject($email_subject);
            //$message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
        });
    }
    
    public function firebase($device_token,$message) {
	//echo '<pre>';print_r($message);exit;
        // Message should contain key and value. It should be an array like =====  message=>'Hi test'
        $url = 'https://fcm.googleapis.com/fcm/send';
		
        $fields = array(
            'to' => $device_token,
            'content_available' => true,
	    'mutable_content' => true,
            'data' => $message,
	    'notification' =>  $message
        );
		//echo json_encode($fields);exit;
        // Authentication..... Identification for project on firebase

        $header = array(
            'Authorization:key =AAAAasYy5JQ:APA91bFfw0DZHXZIhb5or73hsMokgehBkyMJRx_NKo59DVwQiPerNERyiYQHyJXL8Vv39E4W5UHm3OOu0JUZ9vvya5BOOv_IWL0kQFYpjPDaF6isKqvu8x83TtYlgwVchuzEdNLw6WPH',
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
	//echo '<pre>'; print_r($result);exit;
        if ($result === false) {
            die('Curl Failed: ' . curl_error($ch));
        }
        
        curl_close($ch);
        return $result;
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }   
    
    function getdate($second_time){
        $string = "";
        $seconds = time()- $second_time;
        //$days = date('d-M-Y' , strtotime($second_time));
        $year = intval(intval($seconds) / (365 * 24 * 60 * 60));
        $momth = intval(intval($seconds) / (30 * 24 * 60 * 60));
        $weeks = intval(intval($seconds) / (7 * 24 * 60 * 60));
        $days = intval(intval($seconds) / (3600*24));
        $hours = (intval($seconds) / 3600) % 24;
        $minutes = (intval($seconds) / 60) % 60;
        $seconds = (intval($seconds)) % 60;
        //echo $weeks.'======'.$days.'===='. $hours .'====' . $minutes. '=====' . $seconds;exit;
        if($year > 0){
            return  $string .= "$year Year ago";
        }
        if($momth > 0){
            return  $string .= "$momth Month ago";
        }
        if($weeks > 0){
            return  $string .= "$weeks weeks ago";
        }
        if($hours > 24 && $hours < 48){
                return $string .= "Yesterday";
        }
        if($hours > 1 && $hours < 24){
                return $string .= "Today";
        }
        if($days > 0){
                return  $string .= "$days days ago";
        }
        if($hours > 0){
                return $string .= "$hours hours ago";
        }
        if($minutes > 0){
                return $string .= "$minutes minutes ago";
        }
        if ($seconds < 59){
                return $string .= "Just now";
        }
    }
    
    public function ValidationCoupon($user_id , $coupon_code = ''){
        try{
            
            $Coupon = Copoun::select('id as coupon_id', 'code as coupon_code','discount as coupon_discount','min_price as min_cart_total','no_of_time','max_price_applay as max_amount')->where('code',$coupon_code)->where('status',0)->first();
            
            if($Coupon){
                $userCount = CouponUser::where('user_id' , $user_id)->where('coupon_code',$coupon_code)->count();
                //dd($userCount);
                if($userCount < $Coupon->no_of_time){
                    return ["status" => true,"message"=>"Coupon Applied successfully.","user_access"=>request()->user()->user_access,"Cart_details"=>$Coupon];
                }else{
                    return ['status' => false, 'message' => 'Coupon code hass been expaired for this user.',"user_access"=>request()->user()->user_access];
                }
            }else{
                return ['status' => false, 'message' => 'No coupon code found.',"user_access"=>request()->user()->user_access];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
}
