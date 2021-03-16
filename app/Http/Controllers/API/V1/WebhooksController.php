<?php
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\Types\Self_;
use Illuminate\Support\Facades\Hash;
use App\Models\UserLoginData;
use App\Models\User;
use App\Models\Review;
use App\Models\Bookmark;
use App\Models\UserAddress;
use App\Models\Category;
use App\Models\Area;
use App\Models\Product;
use App\Models\Follower;
use App\Models\Order;
use App\Models\TransactionItem;
use App\Models\Transaction;
use App\Models\CardDetail;
use App\Models\ClosePallyUser;
use App\Models\Setting;
use App\Models\CartItem;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Stripe\Error\Card;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Paystack;
use Session;
//use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;
use DB;
use Mail;
use URL;
use Illuminate\Support\Facades\Log;
class WebhooksController extends Controller
{
    
    public  function __construct()
    {

    }
    
    public function paymentSuccess(){
        Log::useFiles(storage_path().'/logs/myWebhooksLogs.log');
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $event = $jsonrequest['event'];
            if($event == 'charge.success'){
                $data = $jsonrequest['data'];
                //dd($data);
                $paystck_id = $data['id'];
                $reference = $data['reference'];
                if($paystck_id != '' && $data['status'] == 'success'){
                    DB::table('orders')->where('reference' , $reference)->update(['status' => 'In Progress' , 'paystck_responce' => json_encode($jsonrequest)]);
                    //Send Order Email
                    $nororders = DB::table('orders')->where('order_type' , 1)->where('reference' , $reference)->first();
                    if($nororders){
                        $nor_order_id = $nororders->order_id;
                        $dilivery_date = $nororders->dilivery_date;
                        $shipping_cost = $nororders->shipping_cost;
                        if($nororders->discount_amount == null || $nororders->discount_amount == ''){
                            $discount_amount = 0.00;
                        }else{
                            $discount_amount = $nororders->discount_amount;
                        }
                        
                        $address = DB::table('user_address')
                                        ->select('user_address.*','areas.*')
                                        ->join('areas', 'areas.id', '=', 'user_address.area_id' , 'left')
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
                        $cartItem = CartItem::where('user_id' , $user_id)->delete();
                        $users = User::where('user_id' , $user_id)->first();
                        $link = $order_details;
                        //dd($link);
                        //echo '<pre>'; print_r($link);exit;
                        $email_subject = 'Order Detail';
                        $user_name = $users->first_name;
                        $email_from = 'hello@pricepally.com';
                        $this->order_send_email($users->email, $user_name, $email_subject, $email_from,$nor_order_id,$dilivery_date,$shipping_cost,$discount_amount,$delivery_address1,$delivery_address2, $link,'order_email');
                    }
                    $nororders = DB::table('orders')->where('order_type' , 2)->where('reference' , $reference)->first();
                    if($nororders){
                        $nor_order_id = $nororders->order_id;
                        $dilivery_date = $nororders->dilivery_date;
                        $shipping_cost = $nororders->shipping_cost;
                        if($nororders->discount_amount == null || $nororders->discount_amount == ''){
                            $discount_amount = 0.00;
                        }else{
                            $discount_amount = $nororders->discount_amount;
                        }
                        
                        $address = DB::table('user_address')
                                        ->select('user_address.*','areas.*')
                                        ->join('areas', 'areas.id', '=', 'user_address.area_id' , 'left')
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
                        $cartItem = CartItem::where('user_id' , $user_id)->delete();
                        $users = User::where('user_id' , $user_id)->first();
                        $link = $order_details;
                        //dd($link);
                        //echo '<pre>'; print_r($link);exit;
                        $email_subject = 'Order Detail';
                        $user_name = $users->first_name;
                        $email_from = 'hello@pricepally.com';
                        $this->order_send_email($users->email, $user_name, $email_subject, $email_from,$nor_order_id,$dilivery_date,$shipping_cost,$discount_amount,$delivery_address1,$delivery_address2, $link,'order_email');
                    }
                    //Send Order Email
                    Log::info(response()->json(['status'=>1,"message"=>'Order place successfully.','paystck_responce' => $jsonrequest],200));
                    return response()->json(['status'=>"success","message"=>"order has been placed successfully."],200);
                }else{
                    Log::info(response()->json(['status'=>1,"message"=>'order has been failed successfully.','paystck_responce' => $jsonrequest],200));
                    return response()->json(['status'=>"success","message"=>"order has been failed successfully."],200);
                }
                
            }else{
                Log::info(response()->json(['status'=>1,"message"=>'order has been failed successfully.','paystck_responce' => $jsonrequest],200));
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            Log::info(response()->json(['status'=>1,"message"=>$e->getMessage(),'paystck_responce' => $jsonrequest],200));
            return response()->json(['success'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function order_send_email($email, $user_name, $email_subject, $email_from,$order_id,$dilivery_date,$shipping_cost,$discount_amount, $delivery_address1,$delivery_address2,$link,$view_name) {
        $res['userName'] = $user_name;
        $res['orders'] = $link;
        $res['shipping_cost'] = $shipping_cost;
        $res['delivery_address1'] = $delivery_address1;
        $res['delivery_address2'] = $delivery_address2;
        $res['order_id'] = $order_id;
        $res['discount_amount'] = $discount_amount;
        $res['dilivery_date'] = $dilivery_date;
        //echo '<pre>'; print_r($res['orders']);exit;
        Mail::send('email/'.$view_name , $res, function ($message) use ($email_from, $email, $user_name, $email_subject) {
            $message->from($email_from, $name = 'Pricepally');
            $message->to($email, $user_name)->subject($email_subject);
            //$message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
        });
    }
	
}