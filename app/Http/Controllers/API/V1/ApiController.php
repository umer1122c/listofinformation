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
use App\Models\Copoun;
use App\Models\CouponUser;
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

class ApiController extends Controller
{
    
    public  function __construct()
    {

    }

    public function getYoutubeKey(Request $request){
        try{
            $settings = DB::table('settings')->first();
            if($settings){
                if($settings->payment_mod == 1){
                    $api_key = $settings->api_key_live;
                    $contract = $settings->contract_live;
                }else{
                    $api_key = $settings->api_key_local;
                    $contract = $settings->contract_local;
                }
                return response()->json(['status'=>"success","message"=>'Youtube Key found.','youtube_key' => $settings->youtube_key,'payment_mod' => $settings->payment_mod,'api_key' => $api_key,'contract' => $contract],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200,[],JSON_NUMERIC_CHECK);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200,[],JSON_NUMERIC_CHECK);
        }
    }
    
    public function registerUser(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            if($jsonrequest['social_type'] == 1 || $jsonrequest['social_type'] == 2 || $jsonrequest['social_type'] == 3){
                $jsonrequest['loginToken'] = str_random(30);
                    
                $users = User::where('social_id' , $jsonrequest['social_id'])->orWhere('email' , $jsonrequest['email'])->first();
                //dd($users);
                if($users){
                    $user = new UserLoginData([
                                'userId' 	=> $users->user_id,
                                'deviceToken' 	=> $jsonrequest['deviceToken'],
                                'deviceType' 	=> $jsonrequest['deviceType'],
                                'appversion' 	=> $jsonrequest['appversion'],
                                'loginToken' 	=> $jsonrequest['loginToken'],
                                'tokenStatus' 	=> 0,
                                'timeZone' 	=> $jsonrequest['timeZone'],
                                'createdDate' 	=> strtotime("now"),
                            ]);
                    $user->save();
                    $users_res = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('social_id' , $jsonrequest['social_id'])->orWhere('email' , $jsonrequest['email'])->first();
                    //dd($users_res);
                    if($users_res){
                        if($users_res->user_image == ''){
                            if($users_res->social_image != ''){
                                $users_res->user_image = $users_res->social_image;
                            }else{
                                $users_res->user_image = URL::to('/public/front/images/dummy_round.png');
                            }
                        }else{
                            $users_res->user_image = URL::to('/') . '/public/users/'.$users_res->user_image;
                        }
                        $users_res->loginToken = $jsonrequest['loginToken'];
                        return response()->json(['status'=>"success","message"=>'Login Successfully!','loginToken' => $jsonrequest['loginToken'],'data'=>$users_res,],200,[],JSON_NUMERIC_CHECK);
                    }else{
                        return response()->json(['status'=>"failed","message"=>'An error occur during registration!',"user_access" => 1],200);
                    }
                }else{
                    
                    $users = new User();
                    $users->user_id = time();
                    $users->deviceType = $jsonrequest['deviceType'];
                    $users->user_type = $jsonrequest['user_type'];
                    $users->social_type = $jsonrequest['social_type'];
                    $users->social_id = $jsonrequest['social_id'];
                    $users->first_name = $jsonrequest['first_name'];
                    $users->last_name = $jsonrequest['last_name'];
                    $users->email = $jsonrequest['email'];
                    $users->password = Hash::make($jsonrequest['password']);
                    $users->social_image = $jsonrequest['user_image'];
                    $users->created_at = date('Y-m-d h:i:s');
                    $users->updated_at = date('Y-m-d h:i:s');
                    $users->save();
                    $user_id = $users->user_id;
                    $jsonrequest['loginToken'] = str_random(30);
                    $user = new UserLoginData([
                                'userId' 	=> $user_id,
                                'deviceToken' 	=> $jsonrequest['deviceToken'],
                                'deviceType' 	=> $jsonrequest['deviceType'],
                                'appversion' 	=> $jsonrequest['appversion'],
                                'loginToken' 	=> $jsonrequest['loginToken'],
                                'tokenStatus' 	=> 0,
                                'timeZone' 	=> $jsonrequest['timeZone'],
                                'createdDate' 	=> strtotime("now"),
                            ]);
                    $user->save();
                    $users_res = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('user_id' , $user_id)->first();
                    if($users_res){
                        if($users_res->user_image == ''){
                            if($users_res->social_image != ''){
                                $users_res->user_image = $users_res->social_image;
                            }else{
                                $users_res->user_image = URL::to('/public/front/images/dummy_round.png');
                            }
                        }else{
                            $users_res->user_image = URL::to('/') . '/public/users/'.$users_res->user_image;
                        }
                        $users_res->loginToken = $jsonrequest['loginToken'];
                        return response()->json(['status'=>"success","message"=>'User register successfully!','loginToken' => $jsonrequest['loginToken'],'data'=>$users_res],200,[],JSON_NUMERIC_CHECK);
                    }else{
                        return response()->json(['status'=>"failed","message"=>'An error occur during registration!',"user_access" => 1],200);
                    }
                }
            }else{
                $res_email = User::where('email' , $jsonrequest['email'])->first();
                if($res_email){
                    return response()->json(['status'=>"failed","message"=>'This email or username already exist in our database. please try another!',"user_access" => 1],200);
                }else{
                    $user_name = explode('@' , $jsonrequest['email']);
                    $users = new User();
                    $users->user_id = time();
                    $users->deviceType = $jsonrequest['deviceType'];
                    $users->user_type = $jsonrequest['user_type'];
                    $users->business_name = $jsonrequest['business_name'];
                    $users->first_name = $jsonrequest['first_name'];
                    $users->last_name = $jsonrequest['last_name'];
                    $users->user_name = $user_name[0];
                    $users->email = $jsonrequest['email'];
                    $users->password = Hash::make($jsonrequest['password']);
                    $users->created_at = date('Y-m-d h:i:s');
                    $users->updated_at = date('Y-m-d h:i:s');
                    $users->save();
                    $user_id = $users->user_id;
                    $jsonrequest['loginToken'] = str_random(30);
                    $user = new UserLoginData([
                                'userId' 	    => $user_id,
                                'deviceToken'   => $jsonrequest['deviceToken'],
                                'deviceType'    => $jsonrequest['deviceType'],
                                'appversion'    => $jsonrequest['appversion'],
                                'loginToken'    => $jsonrequest['loginToken'],
                                'tokenStatus'   => 0,
                                'timeZone' 	    => $jsonrequest['timeZone'],
                                'createdDate'   => strtotime("now"),
                            ]);
                    $user->save();
                    $link = '';
                    $email_subject = 'Registration email';
                    $user_name = $request->first_name;
                    $email_from = 'hello@pricepally.com';
                    $this->send_email_reg($request->email, $user_name, $email_subject, $email_from, $link, 'register_email');
                    $users_res = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('user_id' , $user_id)->first();
                    if($users_res){
                        if($users_res->user_image == ''){
                            if($users_res->social_image != ''){
                                $users_res->user_image = $users_res->social_image;
                            }else{
                                $users_res->user_image = URL::to('/public/front/images/dummy_round.png');
                            }
                        }else{
                            $users_res->user_image = URL::to('/') . '/public/users/'.$users_res->user_image;
                        }
                        $users_res->fallowing = Follower::where('from_user_id' , $users_res->user_id)->where('is_follow' , 1)->count();
                        $users_res->fallower = Follower::where('to_user_id' , $users_res->user_id)->count();
                        $users_res->loginToken = $jsonrequest['loginToken'];
                        return response()->json(['status'=>"success","message"=>'User register successfully!','loginToken' => $jsonrequest['loginToken'],'data'=>$users_res],200,[],JSON_NUMERIC_CHECK);
                    }else{
                        return response()->json(['status'=>"failed","message"=>'An error occur during registration!'],200);
                    }
                }
            }
        }catch(\Exception $e){ 
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function send_email_reg($email, $user_name, $email_subject, $email_from, $link,$view_name) {
        $res['userName'] = $user_name;
        $res['activationLink'] = $link;
        Mail::send('email/'.$view_name , $res, function ($message) use ($email_from, $email, $user_name, $email_subject) {
            $message->from($email_from, $name = 'Pricepally');
            $message->to($email, $user_name)->subject($email_subject);
            //$message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
        });
    }

    public function login(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $users = User::where('email' , $jsonrequest['email'])->first();
            if($users){
                $hash_pass = $users->password;
                if (Hash::check($jsonrequest['password'], $hash_pass)) {
                    $jsonrequest['loginToken'] = str_random(30);
                    $user = new UserLoginData([
                                'userId' 	=> $users->user_id,
                                'deviceToken' 	=> $jsonrequest['deviceToken'],
                                'deviceType' 	=> $jsonrequest['deviceType'],
                                'appversion' 	=> $jsonrequest['appversion'],
                                'loginToken' 	=> $jsonrequest['loginToken'],
                                'tokenStatus' 	=> 0,
                                'timeZone' 	=> $jsonrequest['timeZone'],
                                'createdDate' 	=> strtotime("now"),
                            ]);
                    $user->save();
                    $users_res = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('email' , $jsonrequest['email'])->first();
                    if($users_res->user_image == ''){
                        if($users_res->social_image != ''){
                            $users_res->user_image = $users_res->social_image;
                        }else{
                            $users_res->user_image = URL::to('/public/front/images/dummy_round.png');
                        }
                    }else{
                        $users_res->user_image = URL::to('/') . '/public/users/'.$users_res->user_image;
                    }
                    $users_res->loginToken = $jsonrequest['loginToken'];
                    $users_res->fallowing = Follower::where('from_user_id' , $users_res->user_id)->where('is_follow' , 1)->count();
                    $users_res->fallower = Follower::where('to_user_id' , $users_res->user_id)->count();
                    return response()->json(['status'=>"success","message"=>'Login Successfully!','loginToken' => $jsonrequest['loginToken'],'data'=>$users_res,],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"failed","message"=>'Email or password is invalid.',"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>'Email or password is invalid.',"user_access" => 1],200);
            }
        }catch(\Exception $e){ 
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function logoutUser(){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->delete();
                return response()->json(['status'=>"success","message"=>"User Logout successfully."],200);
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getUserDeviceToken(){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('tokenStatus',0)->get();
            if($checkUser){
                //UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->delete();
                return response()->json(['status'=>"success","message"=>"Token Data found.",'data' => $checkUser],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function forgotPassword(Request $request) {
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            if($jsonrequest['email'] == ''){
                return response()->json(['status'=>"failed","message"=>'email parameater is required!',"user_access" => 1],200);
            }
            $users = User::where('email', $jsonrequest['email'])->first();
            if ($users) {
                $pin = base64_encode($jsonrequest['email']);
                $link = URL::to('/') . '/user/reset_password/' . $pin;
                $email_subject = 'Forgot Password';
                $user_name = $users->email;
                $email_from = 'hello@pricepally.com';
                //$sent_to_email = trim($jsonrequest['email']);
                //$sent_to_email = 'rizwan@decodershub.com';
                //$send_email_from = $email_from;
                //$to = $sent_to_email;
                //$subject = "Reset password";
                //$txt = "Test Email";
                //$headers = "From: ".$send_email_from."";
                //mail($to,$subject,$txt,$headers);
                $this->send_email($jsonrequest['email'], $user_name, $email_subject, $email_from, $link, 'forget_email');
                return response()->json(['status'=>"success","message"=>'Please check your email to reset your password.'],200);
            } else {
                return response()->json(['status'=>"success","message"=>'Email does not exist.'],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function send_email($email, $user_name, $email_subject,$email_from,$link,$view_name) {
        $data['userName'] = $user_name;
        $data['activationLink'] = $link;
        Mail::send('email/'.$view_name , $data, function ($message) use ($email_from, $email, $user_name, $email_subject ) {
            $message->from($email_from, $name = 'Pricepally');
            $message->to($email, $user_name)->subject($email_subject);
            $message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
        });
    }

    public function getProfile(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $users = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('user_id' , $jsonrequest['user_id'])->first();
                if($users){
                    if($users->user_image == ''){
                        if($users->social_image != ''){
                            $users->user_image = $users->social_image;
                        }else{
                            $users->user_image = URL::to('/public/front/images/dummy_round.png');
                        }
                    }else{
                        $users->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                    }
                    $users->fallowing = Follower::where('from_user_id' , $jsonrequest['user_id'])->where('is_follow' , 1)->count();
                    $users->fallower = Follower::where('to_user_id' , $jsonrequest['user_id'])->where('is_follow' , 1)->count();
                    return response()->json(['status'=>"success","message"=>'User data found.','data' => $users],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"failed","message"=>'User not found.',"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getOtherPersonProfile(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $users = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('user_id' , $jsonrequest['other_person_id'])->first();
                if($users){

                     if($users->user_image == ''){
                            if($users->social_image != ''){
                                $users->user_image = $users->social_image;
                            }else{
                                $users->user_image = URL::to('/public/front/images/dummy_round.png');
                            }
                        }else{
                            $users->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                        }
                    $users->fallowing = Follower::where('from_user_id' , $jsonrequest['other_person_id'])->where('is_follow' , 1)->count();
                    $users->fallower = Follower::where('to_user_id' , $jsonrequest['other_person_id'])->where('is_follow' , 1)->count();
                    
                    $userFollowing = Follower::select('from_user_id as user_id','is_follow')->where('from_user_id' , $jsonrequest['user_id'])->where('to_user_id' , $jsonrequest['other_person_id'])->where('is_follow' , 1)->first();
                    if($userFollowing){
                        $users->is_follow = 1;
                    }else{
                        $users->is_follow = 0;
                    }
                    $user_follower = Follower::select('from_user_id as user_id','is_follow')->where('to_user_id' , $jsonrequest['other_person_id'])->where('is_follow' , 1)->get();
                    //dd($user_follower);
                    if(count($user_follower) > 0){
                        $friendArray = [];
                        foreach($user_follower as $row){
                            $user = User::where('user_id' , $row->user_id)->first();
                            //dd($user);
                            if($user){
                                $row->first_name = $user->first_name;
                                if($user->last_name == null){
                                    $row->last_name = '';
                                }else{
                                    $row->last_name = $user->last_name;
                                }
                                if($user->user_image == ''){
                                    if($user->social_image != ''){
                                        $row->user_image = $user->social_image;
                                    }else{
                                        $row->user_image = URL::to('/public/front/images/dummy_round.png');
                                    }
                                }else{
                                    $row->user_image = URL::to('/') . '/public/users/'.$user->user_image;
                                }
                                $row->follower = Follower::select('to_user_id as user_id','is_follow')->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                                $row->following = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                                $userFollowing = Follower::select('to_user_id as user_id','is_follow')->where('to_user_id' , $jsonrequest['other_person_id'])->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                                if($userFollowing){
                                    $row->follow = 1;
                                }else{
                                    $row->follow = 0;
                                }
                            }
                            $friendArray[] = $row;
                        }
                        $users->user_followers = $friendArray;
                    }else{
                        $users->user_followers = [];
                    }
                    
                    return response()->json(['status'=>"success","message"=>'User data found.','data' => $users],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"failed","message"=>'User not found.',"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function editProfile(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $image_name = User::where('user_id' , $jsonrequest['user_id'])->first()->user_image;
                if($jsonrequest['user_image'] != ''){
                    $path = public_path()."/users";
                    if(!is_dir($path)){
                        mkdir($path);
                    }
                    if($image_name != ''){
                        array_map('unlink', glob("$path/".$image_name));
                    }
                    $text = str_replace(' ', '+', $request->user_image);
                    $image = base64_decode($text);
                    //$image = base64_decode($request->image);
                    $image_name = uniqid() . '.jpeg';
                    $path1 = $path .'/'. $image_name;
                    file_put_contents($path1, $image);
                }
                
                if(isset($jsonrequest['phone'])){
                    User::where('user_id', $jsonrequest['user_id'])
                        ->update([
                            'first_name' => $jsonrequest['first_name'] ,
                            'last_name' => $jsonrequest['last_name'] ,
                            'phone' => $jsonrequest['phone'] ,
                            'user_image' => $image_name
                        ]);
                }else{
                    User::where('user_id', $jsonrequest['user_id'])
                        ->update([
                            'first_name' => $jsonrequest['first_name'] ,
                            'last_name' => $jsonrequest['last_name'] ,
                            'user_image' => $image_name
                        ]);
                }

                
                $users_res = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('user_id', $jsonrequest['user_id'])->first();
                if($users_res){
                    if($users_res->user_image == ''){
                        if($users_res->social_image != ''){
                            $users_res->user_image = $users_res->social_image;
                        }else{
                            $users_res->user_image = URL::to('/public/front/images/dummy_round.png');
                        }
                    }else{
                        $users_res->user_image = URL::to('/') . '/public/users/'.$users_res->user_image;
                    }
                    $users_res->fallowing = Follower::where('from_user_id' , $users_res->user_id)->where('is_follow' , 1)->count();
                    $users_res->fallower = Follower::where('to_user_id' , $users_res->user_id)->count();
                    return response()->json(['status'=>"success","message"=>'User profile updated successfully!','data' => $users_res],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"failed","message"=>'User not found.',"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
            
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function updatePhone(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                
                //return $jsonrequest['phone'];
                
                User::where('user_id', $jsonrequest['user_id'])
                        ->update([
                            'phone' => $jsonrequest['phone'] 
                        ]);
                $users_res = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('user_id', $jsonrequest['user_id'])->first();
                if($users_res){
                    if($users_res->user_image == ''){
                        if($users_res->social_image != ''){
                            $users_res->user_image = $users_res->social_image;
                        }else{
                            $users_res->user_image = URL::to('/public/front/images/dummy_round.png');
                        }
                    }else{
                        $users_res->user_image = URL::to('/') . '/public/users/'.$users_res->user_image;
                    }
                    return response()->json(['status'=>"success","message"=>'User phone number updated successfully!','data' => $users_res],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"failed","message"=>'User not found.',"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
            
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function changePassword(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $user_password = User::where('user_id' , $jsonrequest['user_id'])->first()->password;
                if(Hash::check($jsonrequest['old_password'], $user_password)){
                    User::where('user_id', $jsonrequest['user_id'])
                            ->update([
                                'password' => Hash::make($jsonrequest['new_password']) 
                            ]);
                    $users = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','user_image','social_image')->where('user_id', $jsonrequest['user_id'])->first();
                    if($users){
                        if($users->user_image == ''){
                            if($users->social_image != ''){
                                $users->user_image = $users->social_image;
                            }else{
                                $users->user_image = URL::to('/public/front/images/dummy_round.png');
                            }
                        }else{
                            $users->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                        }
                        return response()->json(['status'=>"success","message"=>'Password has been updated successfully!','data' => $users],200,[],JSON_NUMERIC_CHECK);
                    }else{
                        return response()->json(['status'=>"failed","message"=>'User not found.',"user_access" => 1],200);
                    }
                }else{
                    return response()->json(['status'=>"failed","message"=>'Old password does not matched.',"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
            
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function checkAppVersion(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $settings = DB::table('settings')->first();
                if($jsonrequest['device_type'] == 1){
                    if($jsonrequest['app_version'] < $settings->android_version){
                        return response()->json(['status'=>"success","message"=>'Cart data found.','is_update' =>1],200,[],JSON_NUMERIC_CHECK);
                    }else{
                        return response()->json(['status'=>"success","message"=>'Cart data found.','is_update' =>0],200,[],JSON_NUMERIC_CHECK);
                    }
                }else{
                    if($jsonrequest['app_version'] < $settings->ios_version){
                        return response()->json(['status'=>"success","message"=>'Cart data found.','is_update' =>1],200,[],JSON_NUMERIC_CHECK);
                    }else{
                        return response()->json(['status'=>"success","message"=>'Cart data found.','is_update' =>0],200,[],JSON_NUMERIC_CHECK);
                    }
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function applyCouponCode(){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('tokenStatus',0)->first();
            if($checkUser){
                $Coupon = Copoun::where('code',$jsonrequest['code'])->where('status',0)->first();
                if($Coupon){
                    $userCount = CouponUser::where('user_id' , $jsonrequest['user_id'])->where('coupon_code',$jsonrequest['code'])->count();
                    if($userCount < $Coupon->no_of_time){
                        return response()->json(['status'=>"success","message"=>"Coupon Applied successfully.",'coupon_detail' => $Coupon],200,[],JSON_NUMERIC_CHECK);
                    }else{
                        return response()->json(['status'=>"failed","message"=>"Coupon code hass been expaired for this user.","user_access" => 1],200);
                    }
                }else{
                    return response()->json(['status'=>"failed","message"=>"No coupon found.","user_access" => 1],200);
                }
                
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function validateCartItems(Request $request)
    {
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $normalProductsArray = $jsonrequest['normalProductArray'];
            $pallyProductsArray = $jsonrequest['pallyproductArray'];
            //echo '<pre>';print_r($normalProductsArray);exit;
            
            $is_update = 0;
            if(count($normalProductsArray) > 0){
                foreach ($normalProductsArray as $normal) {
                    $product_id = $normal['product_id'];
                    $product = Product::where('product_id' , $product_id)->where('status' , 0)->first();
                    //dd($product);
                    if($product){
                        CartItem::where('product_id' , $product_id)->where('user_id' , $jsonrequest['user_id'])->where('pally_id' , 0)->delete();
                        $is_update = 1;
                    }
                    
                }
            }
            //return $is_update;
            //exit;
            //echo '<pre>';print_r($pallyProductsArray);exit;
            if(count($pallyProductsArray) > 0){
                foreach ($pallyProductsArray as $pally) {
                    
                    $pally_id = $pally['pally_id'];
                    $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally['pally_id'])->first();
                    //dd($open_pallys);
                    if($open_pallys){
                        if($open_pallys->pally_type == 'Open'){
                            $cartItemCount = CartItem::where('pally_id' , $pally['pally_id'])->where('user_id' , $jsonrequest['user_id'])->where('type','pally')->first();
                            $cartItemCount = $cartItemCount->qty;
                            $pally_count = $open_pallys->number_of_person - $open_pallys->pally_count;
                            if($cartItemCount > $pally_count){
                                //echo $cartItemCount;exit;
                                if($cartItemCount == 1){
                                    CartItem::where('pally_id' , $pally['pally_id'])->where('user_id' , $jsonrequest['user_id'])->delete();
                                }else{
                                    if($cartItemCount  > $pally_count){
                                        $remaning = $pally_count;
                                    }else{
                                        $remaning = $cartItemCount;
                                    }
//                                    if($cartItemCount  >= $open_pallys->pally_count){
//                                        $x = $cartItemCount - $open_pallys->pally_count;
//                                    }else{
//                                        $x = $open_pallys->pally_count - $cartItemCount;
//                                    }
//                                    
//                                    if($x <= $pally_count){
//                                        $remaning = $x;
//                                    }else{
//                                        $remaning = $pally_count;
//                                    }
                                    if($remaning > 0){
                                        CartItem::where('pally_id' , $pally['pally_id'])->where('user_id' , $jsonrequest['user_id'])->update(['qty'=> $remaning]);
                                    }else{
                                        CartItem::where('pally_id' , $pally['pally_id'])->where('user_id' , $jsonrequest['user_id'])->delete();
                                    }
                                    
                                }
                                $is_update = 1;
                            }
                        }
//                        elseif($open_pallys->pally_type == 'Close'){
//                            CartItem::where('pally_id' , $pally['pally_id'])->where('user_id' , $jsonrequest['user_id'])->delete();
//                            $is_update = 1;
//                        }
                    }
                }
            }
            
            if($is_update == 1){
                $cartItemArray = [];
                $cartItems = CartItem::where('user_id' , $jsonrequest['user_id'])->get();
                if(count($cartItems) > 0){
                    foreach($cartItems as $row){
                        $product = Product::select('product_images')->where('product_id' , $row->product_id)->first();
                        if($product){
                            $product_images = json_decode($product->product_images);
                            $row->product_images = $prodImageUrl.$product_images[0]->imagePath;
                        }else{
                            $row->product_images = '';
                        }
                        
                        $cartItemArray[] = $row;
                    }
                    return response()->json(['status'=>"success","message"=>'Cart data found.','is_update' => $is_update,'data' => $cartItemArray],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'Cart data found.','is_update' => $is_update,'data' => $cartItemArray],200,[],JSON_NUMERIC_CHECK);
                }
            }else{
                return response()->json(['status'=>"success","message"=>'Cart data found.','is_update' => $is_update,'data' => []],200,[],JSON_NUMERIC_CHECK);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function addToCart(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                if($jsonrequest['type'] == 'normal'){
                    $cartItem = CartItem::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $jsonrequest['product_id'])->where('pally_id' , 0)->where('type','normal')->first();
                    if($cartItem){
                        $qty = $cartItem->qty + 1;
                        CartItem::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $jsonrequest['product_id'])->update(['qty'=>$qty]);
                        return response()->json(['status'=>"success","message"=>'Item added into cart successfully.'],200);
                    }else{
                        $cartItems = new CartItem();
                        $cartItems->user_id = $jsonrequest['user_id'];
                        $cartItems->cart_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);
                        $cartItems->product_id = $jsonrequest['product_id'];
                        $cartItems->name = $jsonrequest['name'];
                        $cartItems->qty = $jsonrequest['qty'];
                        $cartItems->price = $jsonrequest['price'];
                        $cartItems->type = $jsonrequest['type'];
                        $cartItems->pally_id = 0;
                        $cartItems->created_at = time();
                        $cartItems->updated_at = time();
                        $cartItems->save();
                        return response()->json(['status'=>"success","message"=>'Item added into cart successfully.'],200);
                    }
                }elseif($jsonrequest['type'] == 'pally'){
                    $open_pallys = DB::table('open_pallys')->where('pally_id' , $jsonrequest['pally_id'])->first();
                    //dd($open_pallys);
                    if($open_pallys){
                        if($open_pallys->pally_type == 'Open'){
                            $cartItemCount = CartItem::where('pally_id' , $jsonrequest['pally_id'])->where('user_id' , $jsonrequest['user_id'])->where('type','pally')->first();
                            //dd($cartItemCount);
                            if($cartItemCount){
                                $cartItemCount = $cartItemCount->qty;
                                $pally_count = $open_pallys->number_of_person - $open_pallys->pally_count;
                                if($cartItemCount < $pally_count){
                                    $cartItem = CartItem::where('user_id' , $jsonrequest['user_id'])->where('pally_id' , $jsonrequest['pally_id'])->where('type','pally')->first();
                                    if($cartItem){
                                        $qty = $cartItem->qty + 1;
                                        CartItem::where('user_id' , $jsonrequest['user_id'])->where('pally_id' , $jsonrequest['pally_id'])->update(['qty'=>$qty]);
                                        return response()->json(['status'=>"success","message"=>'Item added into cart successfully.'],200);
                                    }else{
                                        $cartItems = new CartItem();
                                        $cartItems->user_id = $jsonrequest['user_id'];
                                        $cartItems->cart_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);
                                        $cartItems->product_id = $jsonrequest['product_id'];
                                        $cartItems->name = $jsonrequest['name'];
                                        $cartItems->qty = $jsonrequest['qty'];
                                        $cartItems->price = $jsonrequest['price'];
                                        $cartItems->type = $jsonrequest['type'];
                                        $cartItems->pally_id = $jsonrequest['pally_id'];
                                        $cartItems->created_at = time();
                                        $cartItems->updated_at = time();
                                        $cartItems->save();
                                        return response()->json(['status'=>"success","message"=>'Item added into cart successfully.'],200);
                                    }
                                }else{
                                    return response()->json(['status'=>"failed","message"=>'You cannot add more item into card.'],200);
                                }
                            
                            }else{
                                $cartItems = new CartItem();
                                $cartItems->user_id = $jsonrequest['user_id'];
                                $cartItems->cart_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);
                                $cartItems->product_id = $jsonrequest['product_id'];
                                $cartItems->name = $jsonrequest['name'];
                                $cartItems->qty = $jsonrequest['qty'];
                                $cartItems->price = $jsonrequest['price'];
                                $cartItems->type = $jsonrequest['type'];
                                $cartItems->pally_id = $jsonrequest['pally_id'];
                                $cartItems->created_at = time();
                                $cartItems->updated_at = time();
                                $cartItems->save();
                                return response()->json(['status'=>"success","message"=>'Item added into cart successfully.'],200);
                            }
                        }elseif($open_pallys->pally_type == 'Close'){
                            $cartItemCount = CartItem::where('pally_id' , $jsonrequest['pally_id'])->count();
                            if($cartItemCount == 0){
                                $cartItems = new CartItem();
                                $cartItems->user_id = $jsonrequest['user_id'];
                                $cartItems->cart_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);
                                $cartItems->product_id = $jsonrequest['product_id'];
                                $cartItems->name = $jsonrequest['name'];
                                $cartItems->qty = $jsonrequest['qty'];
                                $cartItems->price = $jsonrequest['price'];
                                $cartItems->type = $jsonrequest['type'];
                                $cartItems->pally_id = $jsonrequest['pally_id'];
                                $cartItems->created_at = time();
                                $cartItems->updated_at = time();
                                $cartItems->save();
                                return response()->json(['status'=>"success","message"=>'Item added into cart successfully.'],200);
                            }else{
                                return response()->json(['status'=>"failed","message"=>'You cannot add more then one slot for close pally.'],200);
                            }
                        }
                    }else{
                        return response()->json(['status'=>"failed","message"=>'You cannot add more item into card.'],200);
                    }
                    
                } 
                
                
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getUserCartItems(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $cartItemArray = [];
                $cartItems = CartItem::where('user_id' , $jsonrequest['user_id'])->get();
                if(count($cartItems) > 0){
                    foreach($cartItems as $row){
                        $pally_id = $row->pally_id;
                        if($pally_id == '0'){
                            $row->pally_type = 'normal';
                        }else{
                            $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally_id)->first();
                            //dd($open_pallys);
                            $row->pally_type = $open_pallys->pally_type;
                        }
                        $product = Product::select('product_images')->where('product_id' , $row->product_id)->first();
                        if($product){
                            $product_images = json_decode($product->product_images);
                            $row->product_images = $prodImageUrl.$product_images[0]->imagePath;
                        }else{
                            $row->product_images = '';
                        }
                        
                        $cartItemArray[] = $row;
                    }
                    return response()->json(['status'=>"success","message"=>'Cart data found.','data' => $cartItemArray],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.','data' => $cartItemArray],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function updateCartItems(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                CartItem::where('cart_id' , $jsonrequest['cart_id'])->update(['qty'=>$jsonrequest['qty']]);
                
                return response()->json(['status'=>"success","message"=>'Shopping cart has been updated successfully.'],200);
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function deleteCartItems(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                CartItem::where('cart_id' , $jsonrequest['cart_id'])->delete();
                return response()->json(['status'=>"success","message"=>'Deleted cart item successfully.'],200);
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function saveCardDetail(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $card_detail = CardDetail::where('user_id' , $jsonrequest['user_id'])->first();
                if($card_detail){
                    CardDetail::where('user_id' , $jsonrequest['user_id'])
                            ->update([
                                'card_name' => $jsonrequest['card_name'],
                                'card_number' => $jsonrequest['card_number'],
                                'exp_month' => $jsonrequest['exp_month'],
                                'exp_year' => $jsonrequest['exp_year']
                            ]);
                    return response()->json(['status'=>"success","message"=>'Card detail has been updated successfully!'],200);
                }else{
                    $CardDetail = new CardDetail();
                    $CardDetail->user_id = $jsonrequest['user_id'];
                    $CardDetail->card_name = $jsonrequest['card_name'];
                    $CardDetail->card_number = $jsonrequest['card_number'];
                    $CardDetail->exp_month = $jsonrequest['exp_month'];
                    $CardDetail->exp_year = $jsonrequest['exp_year'];
                    $CardDetail->created_at = date('Y-m-d h:i:s');
                    $CardDetail->save();
                    return response()->json(['status'=>"success","message"=>'Card detail has been added successfully!'],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getCardDetail(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $card = CardDetail::select('user_id','card_name','card_number','exp_month','exp_year')->where('user_id' , $jsonrequest['user_id'])->first();
            if($card){
                return response()->json(['status'=>"success","message"=>'Card data found.','data' => $card],200);
            }else{
                return response()->json(['status'=>"failed","message"=>'Card not found.',"user_access" => 1],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getAreas(Request $request){
        try{
            $areas = Area::where('status' , 1)->orderBy('name' , 'ASC')->get();
            //return count($trims);
            $categoriesArray = [];
            if(count($areas) > 0){
                
                return response()->json(['status'=>"success","message"=>'areas data found.','data' => $areas],200);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getCategories(Request $request){
        try{
            $categories = Category::where('parent_id' , 0)->where('status' , 0)->orderBy('updated_at' , 'DESC')->get();
            //return count($trims);
            $categoriesArray = [];
            if(count($categories) > 0){
                foreach($categories as $row){
                    if($row->image != ''){
                        $row->image = URL::to('/').'/public/categories/'.$row->image;
                    }
                    if($row->banner_image != ''){
                        $row->banner_image = URL::to('/').'/public/categories/'.$row->banner_image;
                    }
                    $sub_categories = Category::where('parent_id' , $row->id)->where('status' , 0)->orderBy('updated_at' , 'DESC')->get();
                    if(count($sub_categories) > 0){
                        $subCatArray = [];
                        foreach($sub_categories as $sub_row){
                            if($sub_row->image != ''){
                                $sub_row->image = URL::to('/').'/public/categories/'.$sub_row->image;
                            }
                            if($sub_row->banner_image != ''){
                                $sub_row->banner_image = URL::to('/').'/public/categories/'.$sub_row->banner_image;
                            }
                            $subCatArray[] = $sub_row;
                        }
                    }else{
                        $subCatArray = [];
                    }
                    $row->sub_categories = $subCatArray;
                    $categoriesArray[] = $row;
                }
                return response()->json(['status'=>"success","message"=>'categories data found.','data' => $categoriesArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getSubCategories(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $categories = Category::where('parent_id' , $jsonrequest['cat_id'])->where('status' , 0)->orderBy('title' , 'ASC')->get();
            //return count($trims);
            $categoriesArray = [];
            if(count($categories) > 0){
                foreach($categories as $row){
                    if($row->image != ''){
                        $row->image = URL::to('/').'/public/categories/'.$row->image;
                    }
                    if($row->banner_image != ''){
                        $row->banner_image = URL::to('/').'/public/categories/'.$row->banner_image;
                    }
                    $categoriesArray[] = $row;
                }
                return response()->json(['status'=>"success","message"=>'Sub categories data found.','data' => $categoriesArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function gethomeCategories(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $categories = Category::where('parent_id' , 0)->where('status' , 0)->orderBy('title' , 'ASC')->get();
            //return count($trims);
            $dataArray = [];
            $categoriesArray = [];
            $productArray = [];
            $friendArray = [];
            if(count($categories) > 0){
                foreach($categories as $row){
                    if($row->image != ''){
                        $row->image = URL::to('/').'/public/categories/'.$row->image;
                    }
                    if($row->banner_image != ''){
                        $row->banner_image = URL::to('/').'/public/categories/'.$row->banner_image;
                    }
                    $categoriesArray[] = $row;
                }
                $products = DB::table('open_pallys')
                                ->select('products.product_id','products.is_season','products.bulk_price','open_pallys.pally_id','open_pallys.pally_count','open_pallys.created_at','products.product_title','products.slug','products.product_price','products.product_unit','products.product_description','products.product_images','open_pallys.number_of_person')
                                ->join('products', 'open_pallys.product_id', '=', 'products.product_id')
                                ->where('open_pallys.status' , 0)
                                ->where('open_pallys.pally_type' , 'Open')
                                ->orderby('open_pallys.created_at' , 'DESC')
                                ->get();
                if($products){
                    foreach($products as $prod){
                        //$close_pally_check = DB::table('close_pally_users')->select('user_id')->where('user_id' , $jsonrequest['user_id'])->where('pally_id' , $prod->pally_id)->first();
                        //if($close_pally_check == null){
                            $diffrence = $prod->bulk_price - $prod->product_price;
                            $product_off = $diffrence / $prod->bulk_price * 100;
                            $prod->price_tag = round($product_off);
                            $prod->product_price = $prod->product_price / $prod->number_of_person;
                            $prod->pally_url = URL::to('shop/pally/detail/'.$prod->slug.'/'.$prod->product_id.'/'.$prod->pally_id);
                            $prod->pally_count = $prod->number_of_person - $prod->pally_count;
                            $current_time = strtotime(date('Y-m-d H:i:s', strtotime("-72 hours")));
                            $second_time = $prod->created_at;
                            $seconds =  $second_time - $current_time;
                            if($seconds > 0){
                                $prod->pally_timer = $seconds;
                            }else{
                                $prod->pally_timer = 0;
                            }
                            $close_pally_users = DB::table('close_pally_users')->select('user_id')->where('user_id' , $jsonrequest['user_id'])->where('pally_id' , $prod->pally_id)->first();
                            if($close_pally_users){
                                $prod->already_pally = 1;
                            }else{
                                $prod->already_pally = 0;
                            }
                            $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                            if($ratings == null){
                                $prod->ratings = 0;
                            }else{
                                $prod->ratings = $ratings;
                            }
                            $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $prod->product_id)->first();
                            if($bookmark){ 
                                $prod->is_fav = 1;
                            }else{
                                $prod->is_fav = 0;
                            }
                            $prod->pally_url = URL::to('shop/pally/detail/'.$prod->slug.'/'.$prod->product_id.'/'.$prod->pally_id);
                            $product_images = json_decode($prod->product_images);
                            $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                            $userPally = DB::table('open_pallys')
                                            ->select('users.user_image','users.social_image')
                                            ->join('users', 'users.user_id', '=', 'open_pallys.user_id')
                                            ->where('open_pallys.product_id' , $prod->product_id)
                                            ->groupby('open_pallys.user_id')
                                            ->orderby('open_pallys.created_at' , 'DESC')
                                            ->limit(4)
                                            ->get();
                            $pallyUserArray = [];
                            if(count($userPally) > 0){
                                foreach($userPally as $pally){
                                    if($pally->user_image == ''){
                                        if($pally->social_image != ''){
                                            $pally->user_image = $pally->social_image;
                                        }else{
                                            $pally->user_image = URL::to('/public/front/images/dummy_round.png');
                                        }
                                    }else{
                                        $pally->user_image = URL::to('/') . '/public/users/'.$pally->user_image;
                                    }
                                    $pallyUserArray[] = $pally;
                                }

                            }

                            $prod->pally_by = $pallyUserArray;
                            $productArray[] = $prod;
                        //}
                    }
                }
                $users = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('user_id' ,'!=', $jsonrequest['user_id'])->limit(5)->get();
                if($users){
                    foreach($users as $user){
                        if($user->user_image == ''){
                            if($user->social_image != ''){
                                $users->user_image = $user->social_image;
                            }else{
                                $user->user_image = URL::to('/public/front/images/dummy_round.png');
                            }
                        }else{
                            $user->user_image = URL::to('/') . '/public/users/'.$user->user_image;
                        }
                        $friendArray[] = $user;
                    }
                }
                $user_info = User::select('id','user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image')->where('user_id' , $jsonrequest['user_id'])->first();
                if($user_info){
                    if($user_info->user_image == ''){
                        if($user_info->social_image != ''){
                            $user_info->user_image = $user_info->social_image;
                        }else{
                            $user_info->user_image = URL::to('/public/front/images/dummy_round.png');
                        }
                    }else{
                        $user_info->user_image = URL::to('/') . '/public/users/'.$user_info->user_image;
                    }
                }else{
                    $user_info = (object) array();
                    $user_info->id = '';  
                    $user_info->user_id = '';  
                    $user_info->user_type = '';  
                    $user_info->social_id = '';  
                    $user_info->social_type = '';  
                    $user_info->first_name = '';
                    $user_info->last_name = '';
                    $user_info->user_name = '';
                    $user_info->email = '';
                    $user_info->phone = '';
                    $user_info->user_image = '';
                    $user_info->social_image = '';
                }
                $dataArray['open_pally'] = $productArray;
                $dataArray['categories'] = $categoriesArray;
                $dataArray['find_friend'] = $friendArray;
                $settings = DB::table('settings')->first();
                if($jsonrequest['deviceType'] == 'ios'){
                    if($jsonrequest['appversion'] < $settings->ios_version){
                        $is_update = 1;
                    }else{
                        $is_update = 0;
                    }
                }elseif($jsonrequest['deviceType'] == 'android'){
                    if($jsonrequest['appversion'] < $settings->android_version){
                        $is_update = 1;
                    }else{
                        $is_update = 0;
                    }
                }
                return response()->json(['status'=>"success","message"=>'Sub categories data found.','is_update' => $is_update,'user_info' => $user_info,'data' => $dataArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getOpenPallyProducts(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            //Product::select('product_id','product_title','product_price','product_description','product_images')->where('open_pally' , 1)->get(); 
            $products = DB::table('open_pallys')
                                ->select('products.product_id','products.is_season','products.bulk_price','open_pallys.pally_id','open_pallys.pally_count','open_pallys.created_at','products.product_title','products.slug','products.product_price','products.product_unit','products.product_description','products.product_images','open_pallys.number_of_person')
                                ->join('products', 'open_pallys.product_id', '=', 'products.product_id')
                                ->where('open_pallys.status' , 0)
                                ->where('open_pallys.pally_type' , 'Open')
                                ->orderby('open_pallys.created_at' , 'DESC')
                                ->get();
            $productArray = [];
            if(count($products) > 0){
                foreach($products as $prod){
                    //$close_pally_check = DB::table('close_pally_users')->select('user_id')->where('user_id' , $jsonrequest['user_id'])->where('pally_id' , $prod->pally_id)->first();
                    //if($close_pally_check == null){
                        $diffrence = $prod->bulk_price - $prod->product_price;
                        $product_off = $diffrence / $prod->bulk_price * 100;
                        $prod->price_tag = round($product_off);
                        $prod->pally_count = $prod->number_of_person - $prod->pally_count;
                        $prod->product_price = $prod->product_price / $prod->number_of_person;
                        $current_time = strtotime(date('Y-m-d H:i:s', strtotime("-72 hours")));
                        $second_time = $prod->created_at;
                        $seconds =  $second_time - $current_time;
                        if($seconds > 0){
                            $prod->pally_timer = $seconds;
                        }else{
                            $prod->pally_timer = 0;
                        }
                        $close_pally_users = DB::table('close_pally_users')->select('user_id')->where('user_id' , $jsonrequest['user_id'])->where('pally_id' , $prod->pally_id)->first();
                        if($close_pally_users){
                            $prod->already_pally = 1;
                        }else{
                            $prod->already_pally = 0;
                        }
                        $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                        if($ratings == null){
                            $prod->ratings = 0;
                        }else{
                            $prod->ratings = $ratings;
                        }
                        $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $prod->product_id)->first();
                        if($bookmark){ 
                            $prod->is_fav = 1;
                        }else{
                            $prod->is_fav = 0;
                        }
                        $prod->pally_url = URL::to('shop/pally/detail/'.$prod->slug.'/'.$prod->product_id.'/'.$prod->pally_id);
                        $product_images = json_decode($prod->product_images);
                        $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                        $userPally = DB::table('open_pallys')
                                        ->select('users.user_image','users.social_image')
                                        ->join('users', 'users.user_id', '=', 'open_pallys.user_id')
                                        ->where('open_pallys.product_id' , $prod->product_id)
                                        ->groupby('open_pallys.user_id')
                                        ->orderby('open_pallys.created_at' , 'DESC')
                                        ->limit(4)
                                        ->get();
                        $pallyUserArray = [];
                        if(count($userPally) > 0){
                            foreach($userPally as $pally){
                                if($pally->user_image == ''){
                                    if($pally->social_image != ''){
                                        $pally->user_image = $pally->social_image;
                                    }else{
                                        $pally->user_image = URL::to('/public/front/images/dummy_round.png');
                                    }
                                }else{
                                    $pally->user_image = URL::to('/') . '/public/users/'.$pally->user_image;
                                }
                                $pallyUserArray[] = $pally;
                            }
                        }
                        $prod->pally_by = $pallyUserArray;
                        $productArray[] = $prod;
                    //}
                }
                return response()->json(['status'=>"success","message"=>'Pally products data found.','data' => $productArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getClosePallyProducts(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            //Product::select('product_id','product_title','product_price','product_description','product_images')->where('open_pally' , 1)->get(); 
            $products = DB::table('open_pallys')
                                ->select('products.product_id','products.is_season','products.bulk_price','open_pallys.pally_id','open_pallys.pally_count','open_pallys.created_at','products.product_title','products.slug','products.product_price','products.product_unit','products.product_description','products.product_images','open_pallys.number_of_person')
                                ->join('products', 'open_pallys.product_id', '=', 'products.product_id')
                                ->where('open_pallys.status' , 0)
                                ->where('open_pallys.pally_type' , 'Close')
                                ->orderby('open_pallys.created_at' , 'DESC')
                                ->get();
            //dd($products);
            $productArray = [];
            if(count($products) > 0){
                foreach($products as $prod){
                    $close_pally_check = DB::table('close_pally_users')->select('user_id')->where('user_id' , $jsonrequest['user_id'])->where('pally_id' , $prod->pally_id)->where('status' , 0)->first();
                    if($close_pally_check){
                        $diffrence = $prod->bulk_price - $prod->product_price;
                        $product_off = $diffrence / $prod->bulk_price * 100;
                        $prod->price_tag = round($product_off);
                        $prod->pally_count = $prod->number_of_person - $prod->pally_count;
                        $prod->product_price = $prod->product_price / $prod->number_of_person;
                        $current_time = strtotime(date('Y-m-d H:i:s', strtotime("-72 hours")));
                        $second_time = $prod->created_at;
                        $seconds =  $second_time - $current_time;
                        if($seconds > 0){
                            $prod->pally_timer = $seconds;
                        }else{
                            $prod->pally_timer = 0;
                        }
                        $close_pally_users = DB::table('close_pally_users')->select('user_id')->where('user_id' , $jsonrequest['user_id'])->where('pally_id' , $prod->pally_id)->first();
                        if($close_pally_users){
                            $prod->already_pally = 1;
                        }else{
                            $prod->already_pally = 0;
                        }
                        $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                        if($ratings == null){
                            $prod->ratings = 0;
                        }else{
                            $prod->ratings = $ratings;
                        }
                        $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $prod->product_id)->first();
                        if($bookmark){ 
                            $prod->is_fav = 1;
                        }else{
                            $prod->is_fav = 0;
                        }
                        $prod->pally_url = URL::to('shop/pally/detail/'.$prod->slug.'/'.$prod->product_id.'/'.$prod->pally_id);
                        $product_images = json_decode($prod->product_images);
                        $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                        $userPally = DB::table('open_pallys')
                                        ->select('users.user_image','users.social_image')
                                        ->join('users', 'users.user_id', '=', 'open_pallys.user_id')
                                        ->where('open_pallys.product_id' , $prod->product_id)
                                        ->groupby('open_pallys.user_id')
                                        ->orderby('open_pallys.created_at' , 'DESC')
                                        ->limit(4)
                                        ->get();
                        $pallyUserArray = [];
                        if(count($userPally) > 0){
                            foreach($userPally as $pally){
                                if($pally->user_image == ''){
                                    if($pally->social_image != ''){
                                        $pally->user_image = $pally->social_image;
                                    }else{
                                        $pally->user_image = URL::to('/public/front/images/dummy_round.png');
                                    }
                                }else{
                                    $pally->user_image = URL::to('/') . '/public/users/'.$pally->user_image;
                                }
                                $pallyUserArray[] = $pally;
                            }
                        }
                        $prod->pally_by = $pallyUserArray;
                        $productArray[] = $prod;
                    }
                }
                return response()->json(['status'=>"success","message"=>'Pally products data found.','data' => $productArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function addProductToPally(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                //$open_pallys = DB::table('open_pallys')->where('user_id' , $jsonrequest['user_id'])->where('product_id' , $jsonrequest['product_id'])->where('pally_type' , $jsonrequest['pally_type'])->where('status' , 0)->first();
                //if($open_pallys){
                    //return response()->json(['status'=>"success","message"=>'You already pally this product.'],200);
                //}else{
                    $pally_id = str_random(8);
                    $pallyArray = [];
                    $pallyArray['pally_id'] = $pally_id;
                    $pallyArray['user_id'] = $jsonrequest['user_id'];
                    $pallyArray['pally_type'] = $jsonrequest['pally_type'];
                    $pallyArray['product_id'] = $jsonrequest['product_id'];
                    $pallyArray['number_of_person'] = $jsonrequest['number_of_person'];
                    $pallyArray['created_at'] = time();
                    $pallyArray['updated_at'] = time();
                    DB::table('open_pallys')->insert($pallyArray);
//                    if($jsonrequest['pally_type'] == 'Open'){
//                        $users = User::get();
//                        if(count($users) > 0){
//                            foreach($users as $row){
//                                $users = UserLoginData::select('deviceToken')->where('userId' , $row->user_id)->where('tokenStatus',0)->get();
//                                $device_token = '';
//                                if($users){
//                                    foreach($users as $user){
//                                        if($user->userId != $jsonrequest['user_id']){
//                                            $device_token = $user->deviceToken;
//                                            $message =  array(
//                                                            'type' => 'notification',
//                                                            'title' => 'New Pally Request',
//                                                            'body' => 'Opened a open pally to share this discounted wholesale product with you.',
//                                                            'username' => $row->first_name,
//                                                            'pally_id' => $pally_id,
//                                                            'type1' => 'open',
//                                                            'sound'=> 'default',
//                                                            'content-available'=> true,
//                                                            'icon' => 'chat1'
//                                                        );
//                                            $this->firebase($device_token,$message);
//                                        }
//                                    }
//                                }
//                            }
//                        }
//                    }
                    $products = DB::table('products')
                                ->select('products.product_id','open_pallys.pally_id','open_pallys.pally_type','open_pallys.number_of_person','products.product_title','products.slug','products.product_price','products.product_description','products.product_images')
                                ->join('open_pallys', 'open_pallys.product_id', '=', 'products.product_id')
                                ->where('open_pallys.pally_id' , $pally_id)
                                ->first();
                    if($products){
                        
                        $ratings = Review::where('product_id' , $products->product_id)->avg('rating');
                        if($ratings == null){
                            $products->ratings = 0;
                        }else{
                            $products->ratings = $ratings;
                        }
                        $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $products->product_id)->first();
                        if($bookmark){ 
                            $products->is_fav = 1;
                        }else{
                            $products->is_fav = 0;
                        }
                        $products->pally_url = URL::to('shop/pally/detail/'.$products->slug.'/'.$products->product_id.'/'.$products->pally_id);
                        $product_images = json_decode($products->product_images);
                        $products->product_images = $prodImageUrl.$product_images[0]->imagePath;
                    }
                    
                    return response()->json(['status'=>"success","message"=>'Product has been added to pally list successfully!','data' => $products],200,[],JSON_NUMERIC_CHECK);
                //}  
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function saveBookmarkProducts(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $jsonrequest['product_id'])->first();
                if($bookmark){
                    Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $jsonrequest['product_id'])->delete();
                    return response()->json(['status'=>"success","message"=>'Product has been removed from favourites list successfully!'],200);
                }else{
                    $bookmarks = new Bookmark();
                    $bookmarks->user_id = $jsonrequest['user_id'];
                    $bookmarks->product_id = $jsonrequest['product_id'];
                    $bookmarks->save();
                    return response()->json(['status'=>"success","message"=>'Product has been added to favourites list successfully!'],200);
                }  
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getBookmarkProducts(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            //Product::select('product_id','product_title','product_price','product_description','product_images')->where('open_pally' , 1)->get(); 
            $products = DB::table('bookmarks')
                                ->select('products.product_id','products.is_season','products.product_title','products.product_price','products.product_description','products.product_images')
                                ->join('products', 'products.product_id', '=', 'bookmarks.product_id')
                                ->where('bookmarks.user_id' , $jsonrequest['user_id'])
                                //->where('products.status' , 0)
                                ->get();
            //dd($products);
            $productArray = [];
            if(count($products) > 0){
                foreach($products as $prod){
                    $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                    if($ratings == null){
                        $prod->ratings = 0;
                    }else{
                        $prod->ratings = $ratings;
                    }
                     
                    $product_images = json_decode($prod->product_images);
                    $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                    $userPally = DB::table('open_pallys')
                                    ->select('users.user_image','users.social_image')
                                    ->join('users', 'users.user_id', '=', 'open_pallys.user_id')
                                    ->where('open_pallys.product_id' , $prod->product_id)
                                    ->groupby('open_pallys.user_id')
                                    ->orderby('open_pallys.created_at' , 'DESC')
                                    ->limit(4)
                                    ->get();
                    $pallyUserArray = [];
                    if(count($userPally) > 0){
                        foreach($userPally as $pally){
                            if($pally->user_image == ''){
                                if($pally->social_image != ''){
                                    $pally->user_image = $pally->social_image;
                                }else{
                                    $pally->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $pally->user_image = URL::to('/') . '/public/users/'.$pally->user_image;
                            }
                            $pallyUserArray[] = $pally;
                        }

                    }

                    $prod->pally_by = $pallyUserArray;
                    $productArray[] = $prod;
                }
                return response()->json(['status'=>"success","message"=>'Pally products data found.','data' => $productArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getProductDetail(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $products = Product::select('product_id','cat_id','is_season','bulk_price','product_title','product_price','pally_size','product_description','product_images','product_unit')->where('product_id' , $jsonrequest['product_id'])->first(); 
            $productArray = [];
            $imagesArray = [];
            $reviewArray = [];
            if($products){
                $diffrence = $products->bulk_price - $products->product_price;
                $product_off = $diffrence / $products->bulk_price * 100;
                $products->price_tag = round($product_off);
                $product_images = json_decode($products->product_images);
                $category = Category::where('id' , $products->cat_id)->first();
                if($category){
                    $products->category = $category->title;
                }else{
                    $products->category = '';
                }
                foreach($product_images as $image){
                    $image->imagePath = $prodImageUrl.$image->imagePath;
                    $imagesArray[] = $image;
                }
                $products->product_images = $imagesArray;
                $open_pallys = DB::table('open_pallys')->where('product_id' , $products->product_id)->first();
                if($open_pallys){
                    $pally_id = $open_pallys->pally_id;
                }else{
                    $pally_id = 0;
                }
                $products->pally_id = "";
                $ratings = Review::where('product_id' , $products->product_id)->avg('rating');
                if($ratings == null){
                    $products->ratings = 0;
                }else{
                    $products->ratings = $ratings;
                }
                $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $products->product_id)->first();
                if($bookmark){ 
                    $products->is_fav = 1;
                }else{
                    $products->is_fav = 0;
                }
                $reviews = Review::select('id','product_id','user_id','rating','review')->where('product_id' , $products->product_id)->limit(1)->get();
                if($reviews){
                    foreach($reviews as $row){
                        $users = User::select('user_id','first_name','last_name','user_image')->where('user_id' , $row->user_id)->first();
                        if($users){
                            if($users->user_image == ''){
                                if($users->social_image != ''){
                                    $users->user_image = $users->social_image;
                                }else{
                                    $users->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $users->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                            }   
                        }
                        $row->user_info = $users;
                        $reviewArray[] = $row;
                    }
                }
                $products->reviews = $reviewArray;
                $reviews = Review::where('product_id' , $products->product_id)->count();
                $products->review_count = $reviews;
                return response()->json(['status'=>"success","message"=>'products data found.','data' => $products],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function pallyProductDetail(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $pally_id = $jsonrequest['pally_id'];
            $pally = DB::table('open_pallys')->where('pally_id' , $pally_id)->where('status' , 0)->first();
            
            if($pally == null){
                return response()->json(['status'=>"failed","message"=>'This link has been expired.',"user_access" => 1],200);
            }else{
                if($pally->pally_type == 'Close'){
                    
                    $pallyUser = DB::table('close_pally_users')->where('pally_id' , $pally_id)->where('user_id' , $jsonrequest['user_id'])->where('status' , 0)->first();
                    //dd($pallyUser);
                    if($pallyUser ==  null){
                        return response()->json(['status'=>"failed","message"=>'This link has been expired.',"user_access" => 1],200);
                    }
                }
            }
            $product_id = $pally->product_id;
            $products = Product::select('product_id','is_season','bulk_price','product_title','product_price','product_description','product_images','product_unit')->where('product_id' , $product_id)->first(); 
            $productArray = [];
            $imagesArray = [];
            $reviewArray = [];
            if($products){
                $diffrence = $products->bulk_price - $products->product_price;
                $product_off = $diffrence / $products->bulk_price * 100;
                $products->price_tag = round($product_off);
                $product_images = json_decode($products->product_images);
                foreach($product_images as $image){
                    $image->imagePath = $prodImageUrl.$image->imagePath;
                    $imagesArray[] = $image;
                }
                $products->product_images = $imagesArray;
                $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally_id)->first();
                if($open_pallys){
                    $pally_id = $open_pallys->pally_id;
                    $number_of_person = $open_pallys->number_of_person;
                    $pally_count = $number_of_person - $open_pallys->pally_count;
                    $current_time = strtotime(date('Y-m-d H:i:s', strtotime("-72 hours")));
                    $second_time = $open_pallys->created_at;
                    $seconds =  $second_time - $current_time;
                }else{
                    $pally_id = 0;
                    $number_of_person = 0;
                    $pally_count = 0;
                    $seconds = 0;
                }
               
                if($seconds > 0){
                    $products->pally_timer = $seconds;
                }else{
                    $products->pally_timer = 0;
                }
                $products->pally_id = $pally_id;
                $products->number_of_person = $number_of_person;
                $products->pally_count = $pally_count;
                $products->product_price = $products->product_price / $number_of_person;
                $close_pally_users = DB::table('close_pally_users')->select('user_id')->where('user_id' , $jsonrequest['user_id'])->where('pally_id' , $open_pallys->pally_id)->first();
                if($close_pally_users){
                    $products->already_pally = 1;
                }else{
                    $products->already_pally = 0;
                }
                $ratings = Review::where('product_id' , $products->product_id)->avg('rating');
                if($ratings == null){
                    $products->ratings = 0;
                }else{
                    $products->ratings = $ratings;
                }
                $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $products->product_id)->first();
                if($bookmark){ 
                    $products->is_fav = 1;
                }else{
                    $products->is_fav = 0;
                }
                $reviews = Review::select('id','product_id','user_id','rating','review')->where('product_id' , $products->product_id)->limit(1)->get();
                if($reviews){
                    foreach($reviews as $row){
                        $users = User::select('user_id','first_name','last_name','user_image')->where('user_id' ,$row->user_id)->first();
                        if($users){
                            if($users->user_image == ''){
                                if($users->social_image != ''){
                                    $users->user_image = $users->social_image;
                                }else{
                                    $users->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $users->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                            }   
                        }
                        $row->user_info = $users;
                        $reviewArray[] = $row;
                    }
                }
                $products->reviews = $reviewArray;
                $reviews = Review::where('product_id' , $products->product_id)->count();
                $products->review_count = $reviews;
                return response()->json(['status'=>"success","message"=>'products data found.','data' => $products],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getAllReview(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $limit = 20;
            $offset = $jsonrequest['offset'];
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $reviewArray = [];
                $reviews = Review::select('id','product_id','user_id','rating','review')->where('product_id' , $jsonrequest['product_id'])->offset($offset)->limit($limit)->get();
                if($reviews){
                    foreach($reviews as $row){
                        $users = User::select('user_id','first_name','last_name','user_image')->where('user_id' , $row->user_id)->first();
                        if($users){
                            if($users->user_image == ''){
                                if($users->social_image != ''){
                                    $users->user_image = $users->social_image;
                                }else{
                                    $users->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $users->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                            }   
                        }
                        $row->user_info = $users;
                        $reviewArray[] = $row;
                    }
                    return response()->json(['status'=>"success","message"=>'reviews data found.','offset' => $offset + $limit,'data' => $reviewArray],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"failed","message"=>'reviews not found.','offset' => $offset,'data' => $reviewArray,"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getFilterProducts(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $limit = 20;
            $offset = $jsonrequest['offset'];
            $paramsArr = [];
            
            $cat_idArray = $jsonrequest['cat_id'];
            //dd($cat_idArray);
            $sub_cat_idArray = $jsonrequest['sub_cat_id'];
            if(count($cat_idArray) > 0){
                $paramsArr['products.cat_id'] = $cat_idArray;
            }
            if(count($sub_cat_idArray) > 0){
                $paramsArr['products.sub_cat_id'] = $sub_cat_idArray;
            }
            if(count($paramsArr) > 0){
                $products = DB::table('products')
                                ->select('product_id','is_season','bulk_price','cat_id','sub_cat_id','product_title','product_price','pally_size','product_description','product_images')
                                ->where(function($q) use ($paramsArr)
                                {
                                    foreach($paramsArr as $key => $value)
                                    {
                                        if($key == 'products.cat_id'){
                                            $q->whereIn('cat_id', $value);
                                        }elseif($key == 'products.sub_cat_id'){
                                            $q->whereIn('sub_cat_id', $value);
                                        }
                                        //else{
                                            //$q->where($key, '=', $value);
                                        //}
                                    }
                                })
                                ->where('status' , 1)
                                ->orderBy('created_at','DESC')
                                //->orderBy($order,$type)
                                ->offset($offset)
                                ->limit($limit)
                                ->get();
            }else{
                $products = DB::table('products')
                                ->select('product_id','is_season','bulk_price','cat_id','sub_cat_id','product_title','product_price','pally_size','product_description','product_images')
                                ->where('status' , 1)
                                //->orderBy($order,$type)
                                ->orderBy('created_at','DESC')
                                ->offset($offset)
                                ->limit($limit)
                                ->get();
            }
            $productArray = [];
            if(count($products) > 0){
                foreach($products as $prod){
                    $diffrence = $prod->bulk_price - $prod->product_price;
                    $product_off = $diffrence / $prod->bulk_price * 100;
                    $prod->price_tag = round($product_off);
                    $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                    if($ratings == null){
                        $prod->ratings = 0;
                    }else{
                        $prod->ratings = $ratings;
                    }
                    $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $prod->product_id)->first();
                    if($bookmark){ 
                        $prod->is_fav = 1;
                    }else{
                        $prod->is_fav = 0;
                    }
                    $product_images = json_decode($prod->product_images);
                    $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                    $userPally = DB::table('open_pallys')
                                    ->select('users.user_image','users.social_image')
                                    ->join('users', 'users.user_id', '=', 'open_pallys.user_id')
                                    ->where('open_pallys.product_id' , $prod->product_id)
                                    ->groupby('open_pallys.user_id')
                                    ->orderby('open_pallys.created_at' , 'DESC')
                                    ->limit(4)
                                    ->get();
                    $pallyUserArray = [];
                    if(count($userPally) > 0){
                        foreach($userPally as $pally){
                            if($pally->user_image == ''){
                                if($pally->social_image != ''){
                                    $pally->user_image = $pally->social_image;
                                }else{
                                    $pally->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $pally->user_image = URL::to('/') . '/public/users/'.$pally->user_image;
                            }
                            $pallyUserArray[] = $pally;
                        }

                    }

                    $prod->pally_by = $pallyUserArray;
                    $productArray[] = $prod;
                }
                return response()->json(['status'=>"success","message"=>'Pally products data found.','offset' => $offset + $limit,'data' => $productArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','offset' => $offset,'data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getSearchProducts(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $paramsArr = [];
            
            if($jsonrequest['keyword'] != ''){
                $paramsArr['products.product_title'] = $jsonrequest['keyword'];
            }
            if(count($paramsArr) > 0){
                $products = DB::table('products')
                                ->select('product_id','is_season','bulk_price','cat_id','sub_cat_id','product_title','product_price','pally_size','product_description','product_images')
                                ->join('categories' , 'products.cat_id','=','categories.id')
                                ->where(function($q) use ($paramsArr)
                                {
                                    foreach($paramsArr as $key => $value)
                                    {
                                        //if($key == 'products.cat_id'){
                                            $q->where('products.product_title', 'like', '%'.$value.'%');
                                        //}elseif($key == 'products.sub_cat_id'){
                                            //$q->whereIn('sub_cat_id', $value);
                                        //}
                                        //else{
                                            //$q->where($key, '=', $value);
                                        //}
                                    }
                                })
                                ->where('categories.status' , 0)
                                ->where('products.status' , 1)
                                ->orderBy('products.created_at','DESC')
                                ->get();
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
            $productArray = [];
            if(count($products) > 0){
                foreach($products as $prod){
                    $diffrence = $prod->bulk_price - $prod->product_price;
                    $product_off = $diffrence / $prod->bulk_price * 100;
                    $prod->price_tag = round($product_off);
                    $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                    if($ratings == null){
                        $prod->ratings = 0;
                    }else{
                        $prod->ratings = $ratings;
                    }
                    $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $prod->product_id)->first();
                    if($bookmark){ 
                        $prod->is_fav = 1;
                    }else{
                        $prod->is_fav = 0;
                    }
                    $product_images = json_decode($prod->product_images);
                    $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                    $userPally = DB::table('open_pallys')
                                    ->select('users.user_image','users.social_image')
                                    ->join('users', 'users.user_id', '=', 'open_pallys.user_id')
                                    ->where('open_pallys.product_id' , $prod->product_id)
                                    ->groupby('open_pallys.user_id')
                                    ->orderby('open_pallys.created_at' , 'DESC')
                                    ->limit(4)
                                    ->get();
                    $pallyUserArray = [];
                    if(count($userPally) > 0){
                        foreach($userPally as $pally){
                            if($pally->user_image == ''){
                                if($pally->social_image != ''){
                                    $pally->user_image = $pally->social_image;
                                }else{
                                    $pally->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $pally->user_image = URL::to('/') . '/public/users/'.$pally->user_image;
                            }
                            $pallyUserArray[] = $pally;
                        }

                    }

                    $prod->pally_by = $pallyUserArray;
                    $productArray[] = $prod;
                }
                return response()->json(['status'=>"success","message"=>'Pally products data found.','data' => $productArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getRecommendedProducts(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $products = DB::table('products')
                            ->select('product_id','cat_id as category','product_title','product_price','product_description','product_images')
                            ->join('categories' , 'products.cat_id','=','categories.id')
                            ->where('products.is_recommended', 1)
                            ->where('categories.status' , 0)
                            ->where('products.status' , 1)
                            ->orderBy('products.updated_at','DESC')
                            ->get();
           
            $productArray = [];
            if(count($products) > 0){
                foreach($products as $prod){
                    $category = Category::where('id' , $prod->category)->first();
                    if($category){
                        $prod->category = $category->title;
                    }else{
                        $prod->category = '';
                    }
                    $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                    if($ratings == null){
                        $prod->ratings = 0;
                    }else{
                        $prod->ratings = $ratings;
                    }
                    
                    $product_images = json_decode($prod->product_images);
                    $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                    $productArray[] = $prod;
                }
                return response()->json(['status'=>"success","message"=>'Recommended products data found.','data' => $productArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function closePallyProduct(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $prodImageUrl = URL::to('').'/public/products/';
                $products = Product::select('product_id','is_season','bulk_price','slug','product_title','product_price','pally_size','product_description','product_images','product_unit')->where('product_id' , $jsonrequest['product_id'])->first(); 
                $productArray = [];
                $imagesArray = [];
                $reviewArray = [];
                if($products){
                    $diffrence = $products->bulk_price - $products->product_price;
                    $product_off = $diffrence / $products->bulk_price * 100;
                    $products->price_tag = round($product_off);
                    $open_pallys = DB::table('open_pallys')->where('pally_id' , $jsonrequest['pally_id'])->first();
                    if($open_pallys){
                        $pally_id = $open_pallys->pally_id;
                    }else{
                        $pally_id = 0;
                    }
                    $products->pally_id = $pally_id;
                    $products->product_price = $products->product_price / $open_pallys->number_of_person;
                    $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $products->product_id)->first();
                    if($bookmark){ 
                        $products->is_fav = 1;
                    }else{
                        $products->is_fav = 0;
                    }
                    $product_images = json_decode($products->product_images);
                    foreach($product_images as $image){
                        $image->imagePath = $prodImageUrl.$image->imagePath;
                        $imagesArray[] = $image;
                    }
                    $products->product_images = $imagesArray;
                    $ratings = Review::where('product_id' , $products->product_id)->avg('rating');
                    if($ratings == null){
                        $products->ratings = 0;
                    }else{
                        $products->ratings = $ratings;
                    }
                    $reviews = Review::where('product_id' , $products->product_id)->count();
                    $products->review_count = $reviews;
                    $products->pally_url = URL::to('shop/pally/detail/'.$products->slug.'/'.$products->product_id.'/'.$pally_id);
                    $user_follower = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $jsonrequest['user_id'])->where('is_follow' , 1)->get();
                    if(count($user_follower) > 0){
                        $friendArray = [];
                        foreach($user_follower as $row){
                            $user = User::where('user_id' , $row->user_id)->first();
                            //dd($user);
                            if($user){
                                $row->first_name = $user->first_name;
                                if($user->last_name == null){
                                    $row->last_name = '';
                                }else{
                                    $row->last_name = $user->last_name;
                                }
                                if($user->user_image == ''){
                                    if($user->social_image != ''){
                                        $row->user_image = $user->social_image;
                                    }else{
                                        $row->user_image = URL::to('/public/front/images/dummy_round.png');
                                    }
                                }else{
                                    $row->user_image = URL::to('/') . '/public/users/'.$user->user_image;
                                }
                                $row->follower = Follower::select('to_user_id as user_id','is_follow')->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                                $row->following = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $row->user_id)->where('is_follow' , 1)->count();

                                $friendArray[] = $row;
                            }
                        }
                        $products->user_followers = $friendArray;
                    }else{
                        $products->user_followers = [];
                    }
                    return response()->json(['status'=>"success","message"=>'products data found.','data' => $products],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function closePallyProductDetail(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $prodImageUrl = URL::to('').'/public/products/';
                $products = Product::select('product_id','is_season','bulk_price','slug','product_title','product_price','pally_size','product_description','product_images','product_unit')->where('product_id' , $jsonrequest['product_id'])->first(); 
                $productArray = [];
                $imagesArray = [];
                $reviewArray = [];
                if($products){
                    $diffrence = $products->bulk_price - $products->product_price;
                    $product_off = $diffrence / $products->bulk_price * 100;
                    $products->price_tag = round($product_off);
                    $open_pallys = DB::table('open_pallys')->where('product_id' , $products->product_id)->first();
                    if($open_pallys){
                        $products->pally_count = $open_pallys->number_of_person - $open_pallys->pally_count;
                        $products->number_of_person = $open_pallys->number_of_person;
                        $pally_id = $open_pallys->pally_id;
                    }else{
                        $pally_id = 0;
                        $products->pally_count = 0;
                        $products->number_of_person = 0;
                    }
                    $products->pally_id = $pally_id;
                    $products->product_price = $products->product_price / $open_pallys->number_of_person;
                    $bookmark = Bookmark::where('user_id' , $jsonrequest['user_id'])->where('product_id' , $products->product_id)->first();
                    if($bookmark){ 
                        $products->is_fav = 1;
                    }else{
                        $products->is_fav = 0;
                    }
                    $product_images = json_decode($products->product_images);
                    foreach($product_images as $image){
                        $image->imagePath = $prodImageUrl.$image->imagePath;
                        $imagesArray[] = $image;
                    }
                    $products->product_images = $imagesArray;
                    $ratings = Review::where('product_id' , $products->product_id)->avg('rating');
                    if($ratings == null){
                        $products->ratings = 0;
                    }else{
                        $products->ratings = $ratings;
                    }
                    $reviews = Review::where('product_id' , $products->product_id)->count();
                    $products->review_count = $reviews;
                    $products->pally_url = URL::to('shop/pally/detail/'.$products->slug.'/'.$products->product_id.'/'.$pally_id);
                    $close_pally_users = DB::table('close_pally_users')->select('user_id')->where('pally_id' , $pally_id)->get();
                    if(count($close_pally_users) > 0){
                        $friendArray = [];
                        foreach($close_pally_users as $row){
                            $user = User::where('user_id' , $row->user_id)->first();
                            //dd($user);
                            $row->first_name = $user->first_name;
                            if($user->last_name == null){
                                $row->last_name = '';
                            }else{
                                $row->last_name = $user->last_name;
                            }
                            if($user->user_image == ''){
                                if($user->social_image != ''){
                                    $row->user_image = $user->social_image;
                                }else{
                                    $row->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $row->user_image = URL::to('/') . '/public/users/'.$user->user_image;
                            }
                            
                            
                            $friendArray[] = $row;
                        }
                        $products->pally_users = $friendArray;
                    }else{
                        $products->pally_users = [];
                    }
                    return response()->json(['status'=>"success","message"=>'products data found.','data' => $products],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function sendRequestToclosePallyUsers(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $close_pallys = ClosePallyUser::where('pally_id' , $jsonrequest['pally_id'])->first();
                if($close_pallys){
                    return response()->json(['status'=>"success","message"=>'You already send request to users for this product.'],200);
                }else{
                    $user_idsArray = $jsonrequest['user_ids'];
                    //dd($user_idsArray);
                    
                    if(count($user_idsArray) > 0){
                        foreach($user_idsArray as $key => $val){
                            $pallyArray = [];
                            $pallyArray['pally_id'] = $jsonrequest['pally_id'];
                            $pallyArray['user_id'] = $val;
                            $pallyArray['created_at'] = time();
                            $pallyArray['updated_at'] = time();
                            DB::table('close_pally_users')->insert($pallyArray);
//                            $usersname = User::where('user_id' , $val)->first();
//                            $users = UserLoginData::select('deviceToken')->where('userId' , $val)->where('tokenStatus',0)->get();
//                            $device_token = '';
//                            if($users){
//                                foreach($users as $user){
//                                    if($user->userId != $jsonrequest['user_id']){
//                                        $device_token = $user->deviceToken;
//                                        $message =  array(
//                                                'type' => 'notification',
//                                                'title' => 'New Pally Request',
//                                                'body' => 'Opened a close pally to share this discounted wholesale product with you.',
//                                                'username' => $usersname->first_name,
//                                                'pally_id' => $jsonrequest['pally_id'],
//                                                'type1' => 'close',
//                                                'sound'=> 'default',
//                                                'content-available'=> true,
//                                                'icon' => 'chat1'
//                                            );
//                                        $this->firebase($device_token,$message);
//                                    }
//                                }
//                            }
                        }
                        return response()->json(['status'=>"success","message"=>'Product pally request has been send to users successfully!'],200);
                    }else{
                        return response()->json(['status'=>"failed","message"=>'Please select user to send request.',"user_access" => 1],200);
                    }
                }  
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function saveUserAddress(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $userAddress = new UserAddress();
                $userAddress->user_id = $jsonrequest['user_id'];
                $userAddress->house_name = $jsonrequest['house_name'];
                $userAddress->lable = $jsonrequest['lable'];
                $userAddress->street = $jsonrequest['street'];
                $userAddress->town = $jsonrequest['town'];
                //$userAddress->county = $jsonrequest['county'];
                //$userAddress->postcode = $jsonrequest['postcode'];
                $userAddress->area_id = $jsonrequest['area_id'];
                //$userAddress->latitude = $jsonrequest['latitude'];
                //$userAddress->longitude = $jsonrequest['longitude'];
                $userAddress->save();
                $address_id = $userAddress->address_id;
                $UserAddress = UserAddress::where('address_id' , $address_id)->first();
                $areas = Area::select('name as area_name','value1','value2','value3')->where('id' , $UserAddress->area_id)->first();
                if($areas){
                    $UserAddress->area_name = $areas->area_name;
                    $UserAddress->value1 = $areas->value1;
                    $UserAddress->value2 = $areas->value2;
                    $UserAddress->value3 = $areas->value3;
                }else{
                    $UserAddress->area_name = '';
                    $UserAddress->value1 = '';
                    $UserAddress->value2 = '';
                    $UserAddress->value3 = '';
                }
                return response()->json(['status'=>"success","message"=>'User address has been added successfully!','data' => $UserAddress],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
                
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function editUserAddress(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                UserAddress::where('address_id' , $jsonrequest['address_id'])
                    ->update([
                            "house_name" => $jsonrequest['house_name'],
                            "lable" => $jsonrequest['lable'],
                            "street" => $jsonrequest['street'],
                            "town" => $jsonrequest['town'],
                            //"county" => $jsonrequest['county'],
                            "area_id" => $jsonrequest['area_id'],
                            //"postcode" => $jsonrequest['postcode'],
                            //"latitude" => $jsonrequest['latitude'],
                            //"longitude" => $jsonrequest['longitude']
                        ]);
                return response()->json(['status'=>"success","message"=>'User address has been Updated successfully!'],200);
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function deleteUserAddress(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                UserAddress::where('address_id' , $jsonrequest['address_id'])->delete();
                return response()->json(['status'=>"success","message"=>'User address has been deleted successfully!'],200);
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getUserAddress(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $addressArray = [];
                $UserAddress = UserAddress::where('user_id' , $jsonrequest['user_id'])->orderby('address_id', 'DESC')->get();
                if($UserAddress){
                    foreach($UserAddress as $row){
                        $areas = Area::select('name as area_name','value1','value2','value3')->where('id' , $row->area_id)->where('status' , 1)->first();
                        
                        if($areas){
                            $row->area_name = $areas->area_name;
                            $row->value1 = $areas->value1;
                            $row->value2 = $areas->value2;
                            $row->value3 = $areas->value3;
                            $addressArray[] = $row;
                        }
                    }
                    return response()->json(['status'=>"success","message"=>'UserAddress data found.','data' => $addressArray],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"failed","message"=>'UserAddress not found.',"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getAddress(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $UserAddress = UserAddress::where('address_id' , $jsonrequest['address_id'])->first();
                if($UserAddress){
                    $areas = Area::select('name as area_name','value1','value2','value3')->where('id' , $UserAddress->area_id)->first();
                    if($areas){
                        $UserAddress->area_name = $areas->area_name;
                        $UserAddress->value1 = $areas->value1;
                        $UserAddress->value2 = $areas->value2;
                        $UserAddress->value3 = $areas->value3;
                    }else{
                        $UserAddress->area_name = '';
                        $UserAddress->value1 = '';
                        $UserAddress->value2 = '';
                        $UserAddress->value3 = '';
                    }
                    return response()->json(['status'=>"success","message"=>'UserAddress data found.','data' => $UserAddress],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"failed","message"=>'UserAddress not found.',"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function writeReview(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $reviews = new Review();
                $reviews->user_id = $jsonrequest['user_id'];
                $reviews->product_id = $jsonrequest['product_id'];
                $reviews->review = $jsonrequest['review'];
                $reviews->rating = $jsonrequest['rating'];
                $reviews->created_at = time();
                $reviews->updated_at = time();
                $reviews->save();
                return response()->json(['status'=>"success","message"=>'User review has been added successfully!'],200);
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function findFriends(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $limit = 20;
            $offset = $jsonrequest['offset'];
            $friendArray = [];
            if($jsonrequest['keyword'] == ''){
                $users = User::select('user_id' ,'first_name' , 'last_name' , 'user_image','social_image')->offset($offset)->limit($limit)->orderby('first_name','ASC')->get();
            }else{
                $users = User::select('user_id' ,'first_name' , 'last_name' , 'user_image','social_image')->where('first_name', 'like', '%'.$jsonrequest['keyword'].'%')->orWhere('first_name', 'like', '%'.$jsonrequest['keyword'].'%')->orWhere('email', 'like', '%'.$jsonrequest['keyword'].'%')->offset($offset)->limit($limit)->orderby('first_name','ASC')->get();
            }
            if(count($users) > 0){
                foreach($users as $row){
                    if($jsonrequest['user_id'] != $row->user_id){
                        if($row->last_name == null){
                            $row->last_name = '';
                        }
                        if($row->user_image == ''){
                            if($row->social_image != ''){
                                $row->user_image = $row->social_image;
                            }else{
                                $row->user_image = URL::to('/public/front/images/dummy_round.png');
                            }
                        }else{
                            $row->user_image = URL::to('/') . '/public/users/'.$row->user_image;
                        }

                        $row->follower = Follower::select('to_user_id as user_id','is_follow')->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $row->following = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $userFollowing = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $jsonrequest['user_id'])->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                        if($userFollowing){
                            $row->is_follow = 1;
                        }else{
                            $row->is_follow = 0;
                        }
                        $friendArray[] = $row;
                    }
                }
                return response()->json(['status'=>"success","message"=>'User friend list found','offset' => $offset + $limit, 'data' => $friendArray],200,[],JSON_NUMERIC_CHECK);
            }else{
                return response()->json(['status'=>"success","message"=>'No data found.' ,'offset' => $offset, 'data' => []],200); 
            }
            
        }catch(\Exception $e){ 
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function userFallow(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $fallower = Follower::where('from_user_id' , $jsonrequest['user_id'])->where('to_user_id' , $jsonrequest['fallower_id'])->first();
                //dd($fallower);
                if($fallower){
                    //return $fallower->is_follow;
                    if($fallower->is_follow == 1){
                        Follower::where('from_user_id' , $jsonrequest['user_id'])->where('to_user_id' , $jsonrequest['fallower_id'])->update(['is_follow' => 0]);
                        return response()->json(['status'=>"success","message"=>'User unfollow this user successfully!!'],200);
                    }else{
                        Follower::where('from_user_id' , $jsonrequest['user_id'])->where('to_user_id' , $jsonrequest['fallower_id'])->update(['is_follow' => 1]);
                        $username = User::where('user_id' , $jsonrequest['user_id'])->first()->first_name;
                        $users = UserLoginData::select('deviceToken','userId')->where('userId' , $jsonrequest['fallower_id'])->where('tokenStatus',0)->get();    
                        $device_token = '';
                        if($users){
                            foreach($users as $user){
                                $device_token = $user->deviceToken;
                                $message =  array(
                                                'type' => 'notification',
                                                'title' => 'Pally follow',
                                                'body' => $username.' just followed you on PricePally, follow back',
                                                'username' => $username,
                                                'user_id' => $jsonrequest['user_id'],
                                                'type1' => 'follow',
                                                'sound'=> 'default',
                                                'content-available'=> true,
                                                'icon' => 'chat1'
                                            );
                                $this->firebase($device_token,$message);
                            }
                        }
                        return response()->json(['status'=>"success","message"=>'User follow the following user successfully!'],200);
                    }
                }else{
                    $userFollow = new Follower();
                    $userFollow->from_user_id = $jsonrequest['user_id'];
                    $userFollow->to_user_id = $jsonrequest['fallower_id'];
                    $userFollow->created_at = date('Y-m-d H:i:s');
                    $userFollow->updated_at = date('Y-m-d H:i:s');
                    $userFollow->save();
                    $username = User::where('user_id' , $jsonrequest['user_id'])->first()->first_name;
                    $users = UserLoginData::select('deviceToken','userId')->where('userId' , $jsonrequest['fallower_id'])->where('tokenStatus',0)->get();
                    
                    
                    $device_token = '';
                    if($users){
                        foreach($users as $user){
                            $device_token = $user->deviceToken;
                            $message =  array(
                                            'type' => 'notification',
                                            'title' => 'Pally follow',
                                            'body' => $username.' just followed you on PricePally, follow back',
                                            'username' => $username,
                                            'user_id' => $jsonrequest['user_id'],
                                            'type1' => 'follow',
                                            'sound'=> 'default',
                                            'content-available'=> true,
                                            'icon' => 'chat1'
                                        );
                            $this->firebase($device_token,$message);
                        }
                    }
                    return response()->json(['status'=>"success","message"=>'User follow the following user successfully!!'],200); 
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){ 
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getUserFollowing(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $limit = 20;
            $offset = $jsonrequest['offset'];
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $fallower = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $jsonrequest['user_id'])->where('is_follow' , 1)->offset($offset)->limit($limit)->get();
                if(count($fallower) > 0){
                    $fallowArray = [];
                    foreach($fallower as $row){
                        $users = User::where('user_id' , $row->user_id)->first();
                        if($users){
                            $row->first_name = $users->first_name;
                            $row->last_name = $users->last_name;
                            if($users->user_image == ''){
                                if($users->social_image != ''){
                                    $row->user_image = $users->social_image;
                                }else{
                                    $row->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $row->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                            }
                        }else{
                            $row->first_name = "";
                            $row->last_name = "";
                            $row->user_image = "";
                        }
                        $userFollowing = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $jsonrequest['user_id'])->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                        if($userFollowing){
                            $row->is_follow = 1;
                        }else{
                            $row->is_follow = 0;
                        }
                        $row->follower = Follower::select('to_user_id as user_id','is_follow')->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $row->following = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $fallowArray[] = $row;
                    }
                    
                    return response()->json(['status'=>"success","message"=>'User following list found','offset' => $offset + $limit, 'data' => $fallowArray],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.' ,'offset' => $offset, 'data' => []],200); 
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){ 
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getUserFollowers(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $limit = 20;
            $offset = $jsonrequest['offset'];
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $fallower = Follower::select('from_user_id as user_id','is_follow')->where('to_user_id' , $jsonrequest['user_id'])->where('is_follow' , 1)->offset($offset)->limit($limit)->get();
                if(count($fallower) > 0){
                    $fallowArray = [];
                    foreach($fallower as $row){
                        $users = User::where('user_id' , $row->user_id)->first();
                        if($users){
                            $row->first_name = $users->first_name;
                            $row->last_name = $users->last_name;
                            if($users->user_image == ''){
                                if($users->social_image != ''){
                                    $row->user_image = $users->social_image;
                                }else{
                                    $row->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $row->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                            }
                        }else{
                            $row->first_name = "";
                            $row->last_name = "";
                            $row->user_image = "";
                        }
                        $userFollowing = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $jsonrequest['user_id'])->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                        if($userFollowing){
                            $row->is_follow = 1;
                        }else{
                            $row->is_follow = 0;
                        }
                        $row->follower = Follower::select('to_user_id as user_id','is_follow')->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $row->following = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $fallowArray[] = $row;
                    }
                    
                    return response()->json(['status'=>"success","message"=>'User following list found', 'offset' => $offset + $limit,'data' => $fallowArray],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.' , 'offset' => $offset,'data' => []],200); 
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){ 
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getOtherUserFollowing(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $limit = 20;
            $offset = $jsonrequest['offset'];
            
                $fallower = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $jsonrequest['user_id'])->where('is_follow' , 1)->offset($offset)->limit($limit)->get();
                if(count($fallower) > 0){
                    $fallowArray = [];
                    foreach($fallower as $row){
                        $users = User::where('user_id' , $row->user_id)->first();
                        if($users){
                            $row->first_name = $users->first_name;
                            $row->last_name = $users->last_name;
                            if($users->user_image == ''){
                                if($users->social_image != ''){
                                    $row->user_image = $users->social_image;
                                }else{
                                    $row->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $row->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                            }
                        }else{
                            $row->first_name = "";
                            $row->last_name = "";
                            $row->user_image = "";
                        }
                        $userFollowing = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $jsonrequest['user_id'])->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                        if($userFollowing){
                            $row->is_follow = 1;
                        }else{
                            $row->is_follow = 0;
                        }
                        $row->follower = Follower::select('to_user_id as user_id','is_follow')->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $row->following = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $fallowArray[] = $row;
                    }
                    
                    return response()->json(['status'=>"success","message"=>'User following list found','offset' => $offset + $limit, 'data' => $fallowArray],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.' ,'offset' => $offset, 'data' => []],200); 
                }
            
        }catch(\Exception $e){ 
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getOtherUserFollowers(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $limit = 20;
            $offset = $jsonrequest['offset'];
            
                $fallower = Follower::select('from_user_id as user_id','is_follow')->where('to_user_id' , $jsonrequest['user_id'])->where('is_follow' , 1)->offset($offset)->limit($limit)->get();
                //dd($fallower);
                if(count($fallower) > 0){
                    $fallowArray = [];
                    foreach($fallower as $row){
                        $users = User::where('user_id' , $row->user_id)->first();
                        if($users){
                            $row->first_name = $users->first_name;
                            $row->last_name = $users->last_name;
                            if($users->user_image == ''){
                                if($users->social_image != ''){
                                    $row->user_image = $users->social_image;
                                }else{
                                    $row->user_image = URL::to('/public/front/images/dummy_round.png');
                                }
                            }else{
                                $row->user_image = URL::to('/') . '/public/users/'.$users->user_image;
                            }
                        }else{
                            $row->first_name = "";
                            $row->last_name = "";
                            $row->user_image = "";
                        }
                        $userFollowing = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $jsonrequest['user_id'])->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                        if($userFollowing){
                            $row->is_follow = 1;
                        }else{
                            $row->is_follow = 0;
                        }
                        $row->follower = Follower::select('to_user_id as user_id','is_follow')->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $row->following = Follower::select('to_user_id as user_id','is_follow')->where('from_user_id' , $row->user_id)->where('is_follow' , 1)->count();
                        $fallowArray[] = $row;
                    }
                    
                    return response()->json(['status'=>"success","message"=>'User following list found', 'offset' => $offset + $limit,'data' => $fallowArray],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.' , 'offset' => $offset,'data' => []],200); 
                }
           
        }catch(\Exception $e){ 
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getUserOrders(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $limit = 20;
            $offset = $jsonrequest['offset'];
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                //$orders = Order::where('status' , 'Pending')->get();
                //if(count($orders) > 0){
                    //foreach($orders as $row){
                        //$order_id = $row->order_id;
                        //$order_details = DB::table('order_details')->where('order_id' , $order_id)->delete();
                    //}
                //}
                //$orders = Order::where('status' , 'Pending')->delete();
                $prodImageUrl = URL::to('').'/public/products/';
                //Product::select('product_id','product_title','product_price','product_description','product_images')->where('open_pally' , 1)->get(); 
                $orders = Order::select('order_id','order_total','dilivery_date as dlivered_on','created_at as order_on')->where('user_id' , $jsonrequest['user_id'])->where('order_type' , $jsonrequest['order_type'])->where('status' , '!=' , 'Pending')->orderby('created_at','desc')->offset($offset)->limit($limit)->get();
                //dd($products);
                $orderArray = [];
                if(count($orders) > 0){
                    foreach($orders as $row){
                        $order_id = $row->order_id;
                        $order_details = DB::table('order_details')->where('order_id' , $order_id)->first();
                        if($order_details){
                            $product_id = $order_details->product_id;
                            $product = Product::where('product_id' , $product_id)->first();
                            if($product){
                                $product_images = json_decode($product->product_images);
                                $row->product_images = $prodImageUrl.$product_images[0]->imagePath;
                            }else{
                                $row->product_images = '';
                            }
                            $pally_id = $order_details->pally_id;
                            $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally_id)->first();
                            if($open_pallys){
                                $row->pallied_by = $open_pallys->number_of_person;
                            }else{
                                $row->pallied_by = 0;
                            }
                        }else{
                            $row->pallied_by = 0;
                            $row->product_images = '';
                        }
                        $row->order_on = date('d-m-Y' , strtotime($row->order_on));
                        $row->dlivered_on = date('d-m-Y' , strtotime($row->dlivered_on));
                        $orderArray[] = $row;
                    }
                    return response()->json(['status'=>"success","message"=>'Orders data found.','offset' => $offset + $limit,'data' => $orderArray],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.','offset' => $offset,'data' => []],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getOrderDetail(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/public/products/';
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $orders = Order::select('order_id','order_total','shipping_cost','address','status','created_at as order_date')->where('order_id' , $jsonrequest['order_id'])->first();
                //dd($products);
                $orderArray = [];
                if($orders){
                    $address = DB::table('user_address')
                                    ->select('user_address.*','areas.*')
                                    ->join('areas', 'areas.id', '=', 'user_address.area_id' , 'left')
                                    ->where('address_id' , $orders->address)
                                    ->first();
                    if($address){
                        $orders->delivery_address = $address->house_name.' '.$address->street.' '.$address->town.' '.$address->name;
                    }else{
                        $orders->delivery_address = "";
                    }
                    $orders->order_date = date('M d Y h:i A' , strtotime($orders->order_date));
                    $order_details = DB::table('order_details')->select('product_id','prod_title' , 'quantity' , 'price','pally_id')->where('order_id' , $orders->order_id)->get();
                    //$orders->order_details = $order_details;
                    foreach($order_details as $row){
                        $products = Product::select('product_id','slug','product_images')->where('product_id' , $row->product_id)->first();
                        if($products){
                            $row->pally_url = URL::to('shop/pally/detail/'.$products->slug.'/'.$products->product_id.'/'.$row->pally_id);
                            $product_images = json_decode($products->product_images);
                            if(count($product_images) > 0){
                                $row->product_image = $prodImageUrl.$product_images[0]->imagePath;
                            }else{
                                $row->product_image = '';
                            }
                        }else{
                            $row->product_images = '';
                        }
                        
                        if($row->pally_id == null){
                            $row->pally_id = '';
                        }
                        
                        $orderArray[] = $row;
                    }
                    $orders->order_details = $orderArray;
                    return response()->json(['status'=>"success","message"=>'Orders data found.','data' => $orders],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function updateOrderStatus(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            return response()->json(['status'=>"success","message"=>'Orders status has been updated successfully.'],200);
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getPallyProductDetail(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            $pally_id = $jsonrequest['pally_id'];
            if($checkUser){
                $pally = DB::table('open_pallys')->select('pally_id','product_id','pally_type','number_of_person as pally_size')->where('pally_id' , $jsonrequest['pally_id'])->first();
                $usersArray = [];
                if($pally){
                    $product = Product::where('product_id' , $pally->product_id)->first();
                    if($product){
                        $pally->pally_url = URL::to('shop/pally/detail/'.$product->slug.'/'.$product->product_id.'/'.$pally_id);
                    }else{
                        $pally->pally_url = '';
                    }
                    
                    if($pally->pally_type == 'Open'){
                        $pally_users =DB::table('open_pallys')
                                ->select('close_pally_users.user_id','close_pally_users.status as order_status','users.first_name','users.last_name','users.user_image','users.social_image')
                                ->join('close_pally_users', 'open_pallys.pally_id', '=', 'close_pally_users.pally_id')
                                ->join('users', 'users.user_id', '=', 'close_pally_users.user_id')
                                ->where('close_pally_users.pally_id' , $pally_id)
                                ->get();
                        //dd($pally_users);
                        if(count($pally_users) > 0){
                            foreach($pally_users as $ord){
                                if($ord->order_status == 1){
                                    $ord->order_status = 'Paid';
                                }else{
                                    $ord->order_status = 'UnPaid';
                                }
                                if($ord->last_name == null){
                                    $ord->last_name =  '';
                                }

                                if($ord->user_image == ''){
                                    if($ord->social_image != ''){
                                        $ord->user_image = $ord->social_image;
                                    }else{
                                        $ord->user_image = URL::to('/public/front/images/dummy_round.png');
                                    }
                                }else{
                                    $ord->user_image = URL::to('/') . '/public/users/'.$ord->user_image;
                                }
                                $usersArray[] = $ord;
                            }
                        }
                        $pally->pallyusers = $usersArray;
                    }else{
                        $pally_users =DB::table('open_pallys')
                                ->select('close_pally_users.user_id','close_pally_users.status','users.first_name','users.last_name','users.user_image','users.social_image')
                                ->join('close_pally_users', 'open_pallys.pally_id', '=', 'close_pally_users.pally_id')
                                ->join('users', 'users.user_id', '=', 'close_pally_users.user_id')
                                ->where('close_pally_users.pally_id' , $pally_id)
                                ->get();
                        //dd($pally_users);
                        if(count($pally_users) > 0){
                            foreach($pally_users as $ord){
                                if($ord->status == 1){
                                    $ord->order_status = 'Paid';
                                }else{
                                    $ord->order_status = 'UnPaid';
                                }
                                if($ord->last_name == null){
                                    $ord->last_name =  '';
                                }

                                if($ord->user_image == ''){
                                    if($ord->social_image != ''){
                                        $ord->user_image = $ord->social_image;
                                    }else{
                                        $ord->user_image = URL::to('/public/front/images/dummy_round.png');
                                    }
                                }else{
                                    $ord->user_image = URL::to('/') . '/public/users/'.$ord->user_image;
                                }
                                $usersArray[] = $ord;
                            }
                        }
                        $pally->pallyusers = $usersArray;
                    }
                    return response()->json(['status'=>"success","message"=>'pally product data found.','data' => $pally],200,[],JSON_NUMERIC_CHECK);
                }else{
                    return response()->json(['status'=>"success","message"=>'No data found.','data' => []],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function redirectToMonnify(Request $request){
        try{
            //$jsonrequest = $_POST;$_POST
            $user_id = $request->user_id;
            $deviceType = User::where('user_id',$user_id)->first()->deviceType;
            $product_ids = $request->product_id;
            $address_id = $request->address_id;
            $responceRes = $request->response;
            $reference =  $request->paymentReference;
            $normalProductsArray = json_decode($request->normalProductArray);
            $pallyProductsArray = json_decode($request->pallyproductArray);
            $pallyPriceTotal = 0;
            $normalPriceTotal = 0;
            if(count($normalProductsArray) > 0){
                foreach ($normalProductsArray as $normal) {
                    $normalPriceTotal = $normalPriceTotal + $normal->price * $normal->quantity;
                }
            }
            if(count($pallyProductsArray) > 0){
                foreach ($pallyProductsArray as $pally) {
                    $pallyPriceTotal = $pallyPriceTotal + $pally->price;
                }
            }
            
            if(count($pallyProductsArray) > 0 && count($normalProductsArray) > 0){
                $discount_amount = $request->discount_amount / 2;
            }elseif(count($pallyProductsArray) > 0){
                $discount_amount = $request->discount_amount;
            }elseif(count($normalProductsArray) > 0){
                $discount_amount = $request->discount_amount;
            }
            //return $discount_amount;
            if(count($pallyProductsArray) > 0){
                $pally_order_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);
                Session::put('pally_order_id' , $pally_order_id);
                $total_amount = $request->amount;
                $transArray = [];
                $transArray['deviceType'] = $deviceType;
                $transArray['order_id'] = $pally_order_id;
                $transArray['order_type'] = 2;
                $transArray['user_id'] = $user_id;
                $transArray['status'] = 'Pending';
                $transArray['order_total'] = $pallyPriceTotal;
                $transArray['discount_amount'] = $discount_amount;
                $transArray['coupon_code'] = $request->coupon_code;
                $transArray['address'] = $address_id;
                $transArray['contact_type'] = $request->contact_type;
                $transArray['shipping_cost'] = $request->p_shipping_cost;
                $transArray['reference'] = $reference;
                $transArray['paystck_responce'] = $request->response;
                $transArray['dilivery_date'] = date("Y-m-d h:i:s", strtotime(" +2 days"));
                $transArray['created_at'] = date('Y-m-d h:i:s');
                $transArray['updated_at'] = date('Y-m-d h:i:s');
                DB::table('orders')->insert($transArray);
                foreach($pallyProductsArray as $pally){
                    $itemsPallyArray = [];
                    $itemsPallyArray['order_id'] = $pally_order_id;
                    $itemsPallyArray['product_id'] = $pally->product_id;
                    $itemsPallyArray['prod_title'] = $pally->product_title;
                    $itemsPallyArray['quantity'] = $pally->quantity;
                    $itemsPallyArray['price'] = $pally->price;
                    $itemsPallyArray['pally_id'] = $pally->pally_id;
                    $itemsPallyArray['type'] = 'pally';
                    DB::table('order_details')->insert($itemsPallyArray);
                }
            }else{
                $pally_order_id = 0;
            }
            
            if(count($normalProductsArray) > 0){
                $normal_order_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);;
                Session::put('normal_order_id' , $normal_order_id);
                $total_amount = $request->amount;
                $transArrayNormal = [];
                $transArrayNormal['deviceType'] = $deviceType;
                $transArrayNormal['order_id'] = $normal_order_id;
                $transArrayNormal['order_type'] = 1;
                $transArrayNormal['user_id'] = $user_id;
                $transArrayNormal['status'] = 'Pending';
                $transArrayNormal['order_total'] = $normalPriceTotal;
                $transArrayNormal['discount_amount'] = $discount_amount;
                $transArrayNormal['coupon_code'] = $request->coupon_code;
                $transArrayNormal['address'] = $address_id;
                $transArrayNormal['contact_type'] = $request->contact_type;
                $transArrayNormal['shipping_cost'] = $request->n_shipping_cost;
                $transArrayNormal['reference'] = $reference;
                $transArrayNormal['paystck_responce'] = $request->response;
                $transArrayNormal['dilivery_date'] = date("Y-m-d h:i:s", strtotime(" +2 days"));
                $transArrayNormal['created_at'] = date('Y-m-d h:i:s');
                $transArrayNormal['updated_at'] = date('Y-m-d h:i:s');
                DB::table('orders')->insert($transArrayNormal);
                foreach($normalProductsArray as $normal){
                    $itemsNormalArray = [];
                    $itemsNormalArray['order_id'] = $normal_order_id;
                    $itemsNormalArray['product_id'] = $normal->product_id;
                    $itemsNormalArray['prod_title'] = $normal->product_title;
                    $itemsNormalArray['quantity'] = $normal->quantity;
                    $itemsNormalArray['price'] = $normal->price;
                    $itemsNormalArray['pally_id'] = $normal->pally_id;
                    $itemsNormalArray['type'] = 'normal';
                    DB::table('order_details')->insert($itemsNormalArray);
                }
            }else{
                $normal_order_id = 0;
            }
            $this->updateOrder($normal_order_id,$pally_order_id,$request->response,$reference,$user_id);
            $data_res['monnify_info'] = $request->response;
            $data_res['pally_order_id'] = $pally_order_id;
            $data_res['normal_order_id'] = $normal_order_id;
            return response()->json(['status'=>"success","message"=>'Order place successfully.','data' => $data_res],200);
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function updateOrder($nor_order_id,$pally_order_id,$paymentDetails,$reference,$user_id)
    {
        Log::useFiles(storage_path().'/logs/myPaymentsLogs.log');
        $data['title'] = 'Payment Success';
        $data['class'] = 'success';
        //try{
            //Update Reference No
            $order_details = DB::table('order_details')->where('order_id' , $pally_order_id)->get();
            if(count($order_details) > 0){
                foreach($order_details as $row){
                    $pally_id = $row->pally_id;
                    $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally_id)->first();
                    if($open_pallys){
                        $open_pallys->pally_type;
                        $number_of_person = $open_pallys->number_of_person;
                        $pally_count = $open_pallys->pally_count;
                        $users_name = User::where('user_id' , $user_id)->first();
                        if($users_name){
                            $users_name = $users_name->first_name;
                        }else{
                            $users_name = '';
                        }
                        if($open_pallys->pally_type == 'Open'){
                            if($pally_count == 0){
                                
//                                    $users = User::get();
//                                    if(count($users) > 0){
//                                        foreach($users as $row){
//                                            $users = UserLoginData::select('userId','deviceToken')->where('userId' , $row->user_id)->where('tokenStatus',0)->get();
//                                            $device_token = '';
//                                            if($users){
//                                                foreach($users as $user){
//                                                    if($user->userId != $user_id){
//                                                        $device_token = $user->deviceToken;
//                                                        $message =  array(
//                                                                        'type' => 'notification',
//                                                                        'title' => 'New Pally Request',
//                                                                        'body' => $users_name.' is inviting you to share a wholesale product via a open pally',
//                                                                        'username' => $row->first_name,
//                                                                        'pally_id' => $pally_id,
//                                                                        'type1' => 'open',
//                                                                        'sound'=> 'default',
//                                                                        'content-available'=> true,
//                                                                        'icon' => 'chat1'
//                                                                    );
//                                                        $this->firebase($device_token,$message);
//                                                    }
//                                                }
//                                            }
//                                        }
//                                    }
                            }
                            $pally_users = DB::table('close_pally_users')->where('pally_id' , $pally_id)->get();
                            if(count($pally_users) > 0){
                                foreach($pally_users as $row1){
                                    $usersname = User::where('user_id' , $row1->user_id)->first();
                                    $users = UserLoginData::select('deviceToken','userId')->where('userId' , $row1->user_id)->where('tokenStatus',0)->get();
                                    $device_token = '';
                                    if($users){
                                        foreach($users as $user){
                                            if($user->userId != $user_id){
                                                $device_token = $user->deviceToken;
                                                $message =  array(
                                                        'type' => 'notification',
                                                        'title' => 'Open Pally Paid',
                                                        'body' => $users_name.' has paid his share of the pally',
                                                        'username' => $usersname->first_name,
                                                        'pally_id' => $pally_id,
                                                        'type1' => 'pally_friend',
                                                        'sound'=> 'default',
                                                        'content-available'=> true,
                                                        'icon' => 'chat1'
                                                    );
                                                $this->firebase($device_token,$message);
                                            }
                                        }
                                    }
                                }
                            }
                            if($pally_count <= $number_of_person){
                                $pallyCurrentUserArray = [];
                                $pallyCurrentUserArray['type'] = 'Open';
                                $pallyCurrentUserArray['pally_id'] = $pally_id;
                                $pallyCurrentUserArray['user_id'] = $user_id;
                                $pallyCurrentUserArray['status'] = 1;
                                $pallyCurrentUserArray['created_at'] = time();
                                $pallyCurrentUserArray['updated_at'] = time();
                                DB::table('close_pally_users')->insert($pallyCurrentUserArray);
                                $close_pally_users = DB::table('close_pally_users')->where('user_id' , $user_id)->where('pally_id' , $pally_id)->first();
                                if($close_pally_users){
                                    DB::table('close_pally_users')->where('id' , $close_pally_users->id)->update(['status' => 1]);
                                }
                                $order_details_res = DB::table('order_details')->where('order_id' , $row->order_id)->where('pally_id' , $pally_id)->first();
                                DB::table('close_pally_users')->where('user_id' , $user_id)->where('pally_id' , $pally_id)->update(['status' => 1]);
                                $pally_count_increment = $pally_count + $order_details_res->quantity;
                                if($number_of_person == $pally_count_increment){
                                    DB::table('open_pallys')->where('pally_id' , $pally_id)->update(['pally_count' => $pally_count_increment,'status' => 1]);
                                }else{
                                    DB::table('open_pallys')->where('pally_id' , $pally_id)->update(['pally_count' => $pally_count_increment,'status' => 0]);
                                }
                            }
                        }else{
                            //return '2222';
                            if($pally_count == 0){
                                $pally_users = DB::table('close_pally_users')->where('pally_id' , $pally_id)->get();
                                //dd($pally_users);
                                if(count($pally_users) > 0){
                                    foreach($pally_users as $row){
                                        $usersname = User::where('user_id' , $row->user_id)->first();
                                        $users = UserLoginData::select('userId','deviceToken')->where('userId' , $row->user_id)->where('tokenStatus',0)->get();
                                        $device_token = '';
                                        //dd($users);
                                        if($users){
                                            foreach($users as $user){
                                                if($user->userId != $user_id){
                                                    $device_token = $user->deviceToken;
                                                    $message =  array(
                                                            'type' => 'notification',
                                                            'title' => 'New Pally Request',
                                                            'body' => $users_name.' is inviting you to share a wholesale product via a closed pally',
                                                            'username' => $usersname->first_name,
                                                            'pally_id' => $pally_id,
                                                            'type1' => 'close',
                                                            'sound'=> 'default',
                                                            'content-available'=> true,
                                                            'icon' => 'chat1'
                                                        );
                                                    $this->firebase($device_token,$message);
                                                }
                                            }
                                        }
                                    }
                                } 
                                
                                
                            }
                            $pally_users = DB::table('close_pally_users')->where('pally_id' , $pally_id)->get();
                            //dd($pally_users);
                            if(count($pally_users) > 0){
                                foreach($pally_users as $row){
                                    $usersname = User::where('user_id' , $row->user_id)->first();
                                    $users = UserLoginData::select('deviceToken','userId')->where('userId' , $row->user_id)->where('tokenStatus',0)->get();
                                    $device_token = '';
                                    if($users){
                                        foreach($users as $user){
                                            if($user->userId != $user_id){
                                                $device_token = $user->deviceToken;
                                                $message =  array(
                                                        'type' => 'notification',
                                                        'title' => 'Close Pally Paid',
                                                        'body' => $users_name.' has paid his share of the pally',
                                                        'username' => $usersname->first_name,
                                                        'pally_id' => $pally_id,
                                                        'type1' => 'pally_friend',
                                                        'sound'=> 'default',
                                                        'content-available'=> true,
                                                        'icon' => 'chat1'
                                                    );
                                                $this->firebase($device_token,$message);
                                            }
                                            //return 'dddddd';
                                        }
                                    }
                                }
                            }
                            if($pally_count <= $number_of_person){
                                DB::table('close_pally_users')->where('user_id' , $user_id)->where('pally_id' , $pally_id)->update(['status' => 1]);
                                $pally_count_increment = $pally_count + 1;
                                if($number_of_person == $pally_count_increment){
                                    DB::table('open_pallys')->where('pally_id' , $pally_id)->update(['pally_count' => $pally_count_increment,'status' => 1]);
                                }else{
                                    $pally_count_increment = $pally_count + 1;
                                    DB::table('open_pallys')->where('pally_id' , $pally_id)->update(['pally_count' => $pally_count_increment,'status' => 0]);
                                }
                            }
                        }
                    }
                }
            }
            DB::table('orders')->where('reference' , $reference)->update(['status' => 'In Progress' , 'paystck_responce' => json_encode($paymentDetails)]);
            
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
            
            Log::info(response()->json(['status'=>1,"message"=>'Order place successfully.','paystck_responce' => $paymentDetails,'reference' => $reference],200));
            $cartItem = CartItem::where('user_id' , $user_id)->delete();
            $ord_code = DB::table('orders')->where('reference' , $reference)->first();
            if($ord_code->coupon_code != ''){
                $couponUser = new CouponUser();
                $couponUser->user_id = $ord_code->user_id;
                $couponUser->coupon_code = $ord_code->coupon_code;
                $couponUser->save();
            }
            Session::flash('success_msg', 'Order place successfully.'); 
            return redirect('payment/success');
            
        //}catch(\Exception $e){
            //DB::table('orders')->where('reference' , $reference)->update([ 'paystck_responce' => json_encode($paymentDetails)]);
            //Log::info(response()->json(['status'=>0,"message"=>$e->getMessage(),'paystck_responce' => $paymentDetails,'reference' => $reference],200));
            //return redirect('payment/failed');
        //}
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
            $message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
        });
    }
    
    public function redirectToGateway(Request $request)
    {
        try{
            //$jsonrequest = $_POST;$_POST
            $user_id = $request->user_id;
            $product_ids = $request->product_id;
            $address_id = $request->address_id;
            $pally_order_id = '';
            $normal_order_id = '';
            $normalProductsArray = json_decode($request->normalProductArray);
            $pallyProductsArray = json_decode($request->pallyproductArray);
            $pallyPriceTotal = 0;
            $normalPriceTotal = 0;
            if(count($normalProductsArray) > 0){
                foreach ($normalProductsArray as $normal) {
                    $normalPriceTotal = $normalPriceTotal + $normal->price * $normal->quantity;
                }
            }
            if(count($pallyProductsArray) > 0){
                foreach ($pallyProductsArray as $pally) {
                    $pallyPriceTotal = $pallyPriceTotal + $pally->price  * $pally->quantity;
                }
            }
            if(isset($request->discount_amount)){
                $coupon_code = $request->coupon_code;
                if(count($pallyProductsArray) > 0 && count($normalProductsArray) > 0){
                    $discount_amount = $request->discount_amount / 2;
                }elseif(count($pallyProductsArray) > 0){
                    $discount_amount = $request->discount_amount;
                }elseif(count($normalProductsArray) > 0){
                    $discount_amount = $request->discount_amount;
                }
            }else{
                $discount_amount = 0.00;
                $coupon_code = '';
            }
            
            if(isset($request->deviceType)){
                $deviceType = $request->deviceType;
            }else{
                $deviceType = "do not";
            }
            
            if(count($pallyProductsArray) > 0){
                $pally_order_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);
                Session::put('pally_order_id' , $pally_order_id);
                $total_amount = $request->amount;
                $transArray = [];
                $transArray['order_id'] = $pally_order_id;
                $transArray['order_type'] = 2;
                $transArray['deviceType'] = $deviceType;
                $transArray['user_id'] = $user_id;
                $transArray['status'] = 'Pending';
                $transArray['order_total'] = $pallyPriceTotal;
                $transArray['discount_amount'] = $discount_amount;
                $transArray['coupon_code'] = $coupon_code;
                $transArray['address'] = $address_id;
                $transArray['contact_type'] = $request->contact_type;
                $transArray['shipping_cost'] = $request->p_shipping_cost;
                $transArray['dilivery_date'] = date("Y-m-d h:i:s", strtotime(" +2 days"));
                $transArray['created_at'] = date('Y-m-d h:i:s');
                $transArray['updated_at'] = date('Y-m-d h:i:s');
                DB::table('orders')->insert($transArray);
                foreach($pallyProductsArray as $pally){
                    $itemsPallyArray = [];
                    //Add Open Pally User
//                    $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally->pally_id)->first();
//                    if($open_pallys){
//                        $open_pallys->pally_type;
//                        if($open_pallys->pally_type == 'Open'){
//                            $close_pally_users = DB::table('close_pally_users')->where('user_id' , $user_id)->where('pally_id' , $pally->pally_id)->first();
//                            if(!$close_pally_users){
//                                $pallyCurrentUserArray = [];
//                                $pallyCurrentUserArray['type'] = 'Open';
//                                $pallyCurrentUserArray['pally_id'] = $pally->pally_id;
//                                $pallyCurrentUserArray['user_id'] = $user_id;
//                                $pallyCurrentUserArray['status'] = 0;
//                                $pallyCurrentUserArray['created_at'] = time();
//                                $pallyCurrentUserArray['updated_at'] = time();
//                                DB::table('close_pally_users')->insert($pallyCurrentUserArray);
//                            }
//                        }
//                    }
                    //End Pally User
                    $itemsPallyArray['order_id'] = $pally_order_id;
                    $itemsPallyArray['product_id'] = $pally->product_id;
                    $itemsPallyArray['prod_title'] = $pally->product_title;
                    $itemsPallyArray['quantity'] = $pally->quantity;
                    $itemsPallyArray['price'] = $pally->price;
                    $itemsPallyArray['pally_id'] = $pally->pally_id;
                    $itemsPallyArray['type'] = 'pally';
                    DB::table('order_details')->insert($itemsPallyArray);
                }
            }
            
            if(count($normalProductsArray) > 0){
                $normal_order_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);;
                Session::put('normal_order_id' , $normal_order_id);
                $total_amount = $request->amount;
                $transArrayNormal = [];
                $transArrayNormal['order_id'] = $normal_order_id;
                $transArrayNormal['deviceType'] = $deviceType;
                $transArrayNormal['order_type'] = 1;
                $transArrayNormal['user_id'] = $user_id;
                $transArrayNormal['status'] = 'Pending';
                $transArrayNormal['order_total'] = $normalPriceTotal;
                $transArrayNormal['discount_amount'] = $discount_amount;
                $transArrayNormal['coupon_code'] = $coupon_code;
                $transArrayNormal['address'] = $address_id;
                $transArrayNormal['contact_type'] = $request->contact_type;
                $transArrayNormal['shipping_cost'] = $request->n_shipping_cost;
                $transArrayNormal['dilivery_date'] = date("Y-m-d h:i:s", strtotime(" +2 days"));
                $transArrayNormal['created_at'] = date('Y-m-d h:i:s');
                $transArrayNormal['updated_at'] = date('Y-m-d h:i:s');
                DB::table('orders')->insert($transArrayNormal);
                foreach($normalProductsArray as $normal){
                    $itemsNormalArray = [];
                    $itemsNormalArray['order_id'] = $normal_order_id;
                    $itemsNormalArray['product_id'] = $normal->product_id;
                    $itemsNormalArray['prod_title'] = $normal->product_title;
                    $itemsNormalArray['quantity'] = $normal->quantity;
                    $itemsNormalArray['price'] = $normal->price;
                    $itemsNormalArray['pally_id'] = $normal->pally_id;
                    $itemsNormalArray['type'] = 'normal';
                    DB::table('order_details')->insert($itemsNormalArray);
                }
            }
            $totalAmount = $pallyPriceTotal + $normalPriceTotal +$request->n_shipping_cost + $request->p_shipping_cost - $request->discount_amount;
            
//            if($totalAmount > 2500){
//                $processing_fee = $totalAmount * 1.5 / 100 + 100;
//            }else{
//                $processing_fee = $totalAmount * 1.5 / 100;
//            }
//            if($processing_fee > 2000){
//                $processing_fee = 2000;
//            }
            //$totalAmount = $totalAmount + $processing_fee;
            $responce = $this->getPaystackLink($totalAmount,$request->email);
            if($responce['status'] == 'success'){
//            $users = User::where('user_id' , $user_id)->first();
//            $link = array_merge($normalProductsArray,$pallyProductsArray);
//            //dd($link);
//            //echo '<pre>'; print_r($link);exit;
//            $email_subject = 'Order Detail';
//            $user_name = $users->first_name;
//            $email_from = 'hello@pricepally.com';
//            $this->order_send_email($users->email, $user_name, $email_subject, $email_from,$transArray['shipping_cost'], $link,'order_email');
                $result = json_decode($responce['data']);
                //echo '<pre>'; print_r($result);exit;
                $paystack_info = $result->data;
                $reference = $paystack_info->reference;
                DB::table('orders')->where('order_id' , $pally_order_id)->update(['reference' => $reference]);
                DB::table('orders')->where('order_id' , $normal_order_id)->update(['reference' => $reference]);
                $data_res['paystack_info'] = $result->data;
                $data_res['pally_order_id'] = $pally_order_id;
                $data_res['normal_order_id'] = $normal_order_id;
                return response()->json(['status'=>"success","message"=>'Authorization URL created.','data' => $data_res],200);
            }else{
                return response()->json(['status'=>"failed","message"=>'Authorization URL not created.','data' => $responce['data'],"user_access" => 1],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function getPaystackLink($totalAmount,$email){
        $settings = Setting::where('settings_id' , 1)->first();
        //dd($account);
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
        $reference = str_random(20);
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
    
    public function reportReview(Request $request) {
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $checkUser = UserLoginData::where('userId',$jsonrequest['user_id'])->where('loginToken',$jsonrequest['loginToken'])->where('tokenStatus',0)->first();
            if($checkUser){
                $checkUser = User::where('user_id',$jsonrequest['user_id'])->first();
                if ($checkUser) {
                    $review = Review::where('id' , $jsonrequest['review_id'])->first()->review;
                    $user_name = $checkUser->first_name;
                    $link = $user_name." Report to this review ".$review;
                    $email_subject = "Report Comment";
                    $email_from = 'hello@pricepally.com';
                    $this->send_email_report($checkUser->email, $user_name, $email_subject, $email_from, $link, 'report_email');
                    return response()->json(['status'=>"success","message"=>'Thank you for reporting this comment!'],200);
                } else {
                    return response()->json(['status'=>"failed","message"=>'user does not exist.',"user_access" => 1],200);
                }
            }else{
                return response()->json(['status'=>"failed","message"=>"Session expired please login again.","user_access" => 0],200);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>"failed","message"=>$e->getMessage(),"user_access" => 1],200);
        }
    }
    
    public function send_email_report($email, $user_name, $email_subject, $email_from, $link,$view_name) {
        $res['userName'] = $user_name;
        $res['activationLink'] = $email;
        $res['usermessage'] = $link;
        Mail::send('email/'.$view_name , $res, function ($message) use ($email_from, $email, $user_name, $email_subject) {
            $message->from($email_from, $name = 'Pricepally');
            $message->to($email, $user_name)->subject($email_subject);
            $message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
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
	
    public function firebase1() {
    // Message should contain key and value. It should be an array like =====  message=>'Hi test'
    $url = 'https://fcm.googleapis.com/fcm/send';
            $notfi = array(
                                      "body"=> "Enter your message",
                                      "sound"=> "default"
                               );
    $fields = array(
        'to' => 'e41JdX3MhPI:APA91bH9S0xKO_cWfJQV86V3etsLWg9_o-MtDfA-_mWOzmmU96OK8h4SA_eG5yrlO8dy2uPsh02B0HeoJuYMbjAyMUD_KAMOGyRc-MjWTPl3MbacV3GtDqGqXZqcIdDsBqsAUoS1IHBm',
        'content_available' => true,
                    'mutable_content' => true,
        'data' => $notfi,
                    "notification" =>  $notfi
    );
            echo json_encode($fields);exit;
    // Authentication..... Identification for project on firebase

    $header = array(
        'Authorization:key = AAAABYmCEVM:APA91bGsh37Y48HlLFke5kIGFB4vqsPGk3nSkzxOsy18u8F7ZgkhkJ59OOH9rSE0M6PtCzfKhbaioUz8JI45ieHd-7uX9mRrlc8cLoClXLsYWID_IYbX3b5I2dVmLQQtaz8wAmdam5lo',
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
	
}