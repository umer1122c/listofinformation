<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\User;
use App\Models\Product;
use App\Models\UserLoginData;
use App\Classes\CommonLibrary;
use commonHelper;
use App\Models\CartItem;
use App\Models\CartServiceItem;
use App\Models\OrderServiceItem;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Copoun;
use App\Models\CouponUser;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use Paystack;
use Session;
use Validator;
use Gloudemans\Shoppingcart\Facades\Cart;
use DB;
use Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    
     public function __construct()
    {
        /** Paystack api context **/
        $settings = Setting::where('settings_id' , 1)->first();
        //dd($account);
        if($settings){
            if($settings->payment_mod == 1){
                config(['paystack.publicKey' => $settings->PAYSTACK_PUBLIC_KEY_LIVE]);
                config(['paystack.secretKey' => $settings->PAYSTACK_SECRET_KEY_LIVE]);
            }else{
                config(['paystack.publicKey' => $settings->PAYSTACK_PUBLIC_KEY_LOCAL]);
                config(['paystack.secretKey' => $settings->PAYSTACK_SECRET_KEY_LOCAL]);
            }
        }
        
    }

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function payment(Request $request)
    {
        try{
            $jsonrequest = $request->all();
            //dd($jsonrequest['reference']);
            $user = $request->user();
            $productsArray = CartItem::where('user_id' , session('user_id'))->get();
            //dd($productsArray);
            $deviceType = 'web';
            $order_id = substr(str_shuffle('123456789123456789123456789321654987'),0,6);
            $serviceArrayNew = [];
            if(count($productsArray) > 0){
                if(isset($jsonrequest['app_time'])){
                    $app_datetime = $jsonrequest['app_time'];
                }else{
                    $app_datetime = '';
                }
                //return $app_datetime;
                if($jsonrequest['coupon_code'] == ''){
                    $coupon_code = 'None';
                }else{
                    $coupon_code = $jsonrequest['coupon_code'];
                }
                $transArray = [];
                $transArray['order_id'] = $order_id;
                $transArray['deviceType'] = $deviceType;
                $transArray['user_id'] = session('user_id');
                $transArray['status'] = 'Pending';
                $transArray['reference'] = $jsonrequest['reference'];
                $transArray['order_total'] = $jsonrequest['sub_total'];
                $transArray['address'] = $jsonrequest['address_id_post'];
                $transArray['app_datetime'] = $app_datetime;
                $transArray['shipping_cost'] = $jsonrequest['shipping_cost'];
                $transArray['discount_amount'] = $jsonrequest['discount_amount'];
                $transArray['coupon_code'] = $coupon_code;
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
                Session::put('order_id', $order_id);
                Session::flash('success_msg', 'Order place successfully.'); 
                //return redirect('payment/success?order_id='.$order_id);
                return Paystack::getAuthorizationUrl()->redirectNow();
            }else{
                return redirect('my/cart');
            }
        }catch(\Exception $e){
            return redirect('my/cart');
        }
        
        
    }
    
    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        //Log::useFiles(storage_path().'/logs/myPaymentsLogs.log');
        //echo 'test print';exit;
        $data['title'] = 'Payment Success';
        $data['class'] = 'success';
        $order_id_session = Session::get('order_id');
        try{
            $user_id = Session::get('user_id');
            $paymentDetails = Paystack::getPaymentData();
            //dd($paymentDetails);
            $reference = $paymentDetails['data']['reference'];
            $paystck_id = $paymentDetails['data']['id'];
            //Log::info(response()->json(['status'=>1,"message"=>'Get responce from paystack.','paystck_responce' => $paymentDetails,'reference' => $reference],200));
            if(isset($paymentDetails['status']) && $paymentDetails['status'] == 1){
                $orders = DB::table('orders')->where('reference' , $reference)->first();
                if($orders){
                    $order_id = $orders->order_id;
                    $user_id = $orders->user_id;
                }
                DB::table('orders')->where('reference' , $reference)->update(['status' => 'In Progress' ,'paystck_id' => $paystck_id , 'paystck_responce' => json_encode($paymentDetails)]);
                
                //Send Order Email
                $nororders = DB::table('orders')->where('reference' , $reference)->first();
                if($nororders){
                    $nor_order_id = $nororders->order_id;
                    $shipping_cost = $nororders->shipping_cost;
                    
                    
                    $address = UserAddress::select('user_address.*','areas_inside.title as areasInside','areas_zone_areas.title as areasOutside')
                                    ->Join('areas_inside', 'user_address.area_id', '=', 'areas_inside.id','left')
                                    ->Join('areas_zone_areas', 'user_address.area_id', '=', 'areas_zone_areas.id','left')
                                    ->Join('area_zone_countries', 'user_address.area_id', '=', 'area_zone_countries.id','left')
                                    ->where('address_id' , $nororders->address)
                                    ->first();
                    if($address){
                        $delivery_address1 = $address->house_name.' '.$address->street;
                        $delivery_address2 = $address->town.' '.$address->name;
                    }else{
                        $delivery_address1 = "";
                        $delivery_address2 = "";
                    }
                    $order_details = DB::table('order_details')->where('order_id' , $nor_order_id)->get();
                    $user_id = $nororders->user_id;
                    $users = User::where('user_id' , $user_id)->first();
                    $link = $order_details;
                    //dd($link);
                    //echo '<pre>'; print_r($link);exit;
                    $email_subject = 'Order Detail';
                    $user_name = $users->first_name;
                    $email_from = 'hello@hushd.com';
                    //$this->order_send_email($users->email, $user_name, $email_subject, $email_from,$nor_order_id,$dilivery_date,$shipping_cost,$discount_amount,$wallet_amount,$delivery_address1,$delivery_address2, $link,'order_email');
                }
                //Send Order Email
                $cartItem = CartItem::where('user_id' , $user_id)->first();
                $cart_id = $cartItem->cart_id;
                $cartItem = CartItem::where('user_id' , $user_id)->delete();
                CartServiceItem::where('cart_id' , $cart_id)->delete();
                Session::put('order_id', '');
                Session::flash('success_msg', 'Order place successfully.'); 
                return redirect('payment/success?order_id='.$nor_order_id);
            }else{
                DB::table('order_details')->where('order_id' , $order_id_session)->delete();
                DB::table('order_service_items')->where('order_id' , $order_id_session)->delete();
                DB::table('orders')->where('order_id' , $order_id_session)->delete();
                Session::put('order_id', '');
                //Log::info(response()->json(['status'=>0,"message"=>'Payment failed.','paystck_responce' => $paymentDetails,'reference' => $reference],200));
                return redirect('payment/failed');
            }
        }catch(\Exception $e){
            DB::table('order_details')->where('order_id' , $order_id_session)->delete();
            DB::table('order_service_items')->where('order_id' , $order_id_session)->delete();
            DB::table('orders')->where('order_id' , $order_id_session)->delete();
            Session::put('order_id', '');
            //DB::table('orders')->where('reference' , $reference)->update(['paystck_id' => $paystck_id , 'paystck_responce' => json_encode($paymentDetails)]);
            //Log::info(response()->json(['status'=>0,"message"=>$e->getMessage(),'paystck_responce' => $paymentDetails,'reference' => $reference],200));
            return redirect('payment/failed');
        }
    }
    
    public function sendNotificationToUser($user_id , $follower_id,$username,$title,$body,$pally_id,$type){
        $users = UserLoginData::select('deviceToken','userId')->where('userId' , $follower_id)->where('tokenStatus',0)->get();
        if(count($users) > 0){
            $deviceToken = [];
            foreach($users as $key => $val ){
                $deviceToken[] = $val->deviceToken;
            }
            $message =  array(
                            'type' => 'notification',
                            'title' => $title,
                            'body' => $body,
                            'username' => $username,
                            'pally_id' => $pally_id,
                            'notification_userid' => $follower_id,
                            'type1' => $type,
                            'sound'=> 'default',
                            'content-available'=> true,
                            'icon' => 'chat1'
                        );
            commonHelper::firebase($deviceToken,$message);
            commonHelper::saveNotification($user_id,$follower_id,$username,$title,$body,$type,$pally_id);
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
            $message->from($email_from, $name = 'HushD');
            $message->to($email, $user_name)->subject($email_subject);
            $message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
        });
    }
    
    public function successPayment(Request $request)
    {
        $data['title'] = 'Payment Success';
        $data['class'] = 'success';
        $data['order_id'] = $request->order_id;
        $Order = Order::where('order_id' , $request->order_id)->first();
        if($Order){
            $data['total'] = $Order->order_total + $Order->shipping_cost - $Order->discount_amount - $Order->wallet_amount;
        }else{
            $data['total'] = 0;
        }
        return view('front/home/thankyou' , $data);
    }
    
    public function failerPayment()
    {
        $data['title'] = 'Payment Failed';
        $data['class'] = 'failed';
        return view('front/home/failed_payment' , $data);
    }
}