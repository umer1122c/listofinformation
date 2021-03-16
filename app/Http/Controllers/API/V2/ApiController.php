<?php
namespace App\Http\Controllers\API\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\UserLoginData;
use App\Models\CartItem;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Product;
use App\User;
use App\Models\Category;
use App\Models\Follower;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Validator;
use DB;
use Mail;
use URL;
use EmailVerifier;
use Illuminate\Support\Str;
use App\Classes\CommonLibrary;
use commonHelper;

class ApiController extends Controller
{
    
    public  function __construct()
    {

    }
    
    public function GenerateAccesstoken(Request $request){
        try{
            $jsonrequest = request()->all();
            $users = User::where('user_id' , request()->user_id)->first();
            if($users){
                $jsonrequest['loginToken'] =  Str::random(30);
                $user = new UserLoginData([
                            'userId' 	=> $users->user_id,
                            'deviceToken' 	=> $jsonrequest['deviceToken'],
                            'deviceType' 	=> $jsonrequest['deviceType'],
                            'appversion' 	=> $jsonrequest['appversion'],
                            'loginToken' 	=> $jsonrequest['loginToken'],
                            'tokenStatus' 	=> 0,
                            'timeZone' 	=> $jsonrequest['timeZone'],
                        ]);
                $user->save();
                $users_res = User::select('user_id','user_type','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image','user_access')->where('user_id' , request()->user_id)->first();
                if($users_res->user_image == ''){
                    if($users_res->social_image != ''){
                        $users_res->user_image = $users_res->social_image;
                    }else{
                        $users_res->user_image = URL::to('/front/dummy_round.png');
                    }
                }else{
                    $users_res->user_image = URL::to('/') . '/users/'.$users_res->user_image;
                }
                if($users_res->social_id == null){
                    $users_res->social_id = "";
                }
                $users_res->loginToken = $jsonrequest['loginToken'];
                $users_res->joined_date = date('F Y' , strtotime($users_res->joined_date));
                $access_token = $this->getAccessTokens($users_res);
                $users_res->access_token = $access_token['access_token'];
                $users_res->user_access = $users_res->user_access;
                return ['status'=>true,"message"=>'Login Successful!','user_access' => $users_res->user_access,'data'=>$users_res];
            }else{
                return ['status'=>false,"message"=>'Email or password is invalid.','user_access' => 0];
            }

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),'user_access' => 1];
        }
    }
    
    public function LoginUser(Request $request){
        try{
            $jsonrequest = request()->all();
            $users = User::where('email' , request()->email)->first();
            if($users){
                $hash_pass = $users->password;
                if(Hash::check(request()->password, $hash_pass)) {
                    $jsonrequest['loginToken'] =  Str::random(30);
                    $user = new UserLoginData([
                                'userId' 	=> $users->user_id,
                                'deviceToken' 	=> $jsonrequest['deviceToken'],
                                'deviceType' 	=> $jsonrequest['deviceType'],
                                'appversion' 	=> $jsonrequest['appversion'],
                                'loginToken' 	=> $jsonrequest['loginToken'],
                                'tokenStatus' 	=> 0,
                                'timeZone' 	=> $jsonrequest['timeZone'],
                            ]);
                    $user->save();
                    $users_res = User::select('user_id','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image','user_access')->where('email' , $jsonrequest['email'])->first();
                    if($users_res->user_image == ''){
                        if($users_res->social_image != ''){
                            $users_res->user_image = $users_res->social_image;
                        }else{
                            $users_res->user_image = URL::to('/front/dummy_round.png');
                        }
                    }else{
                        $users_res->user_image = URL::to('/') . '/users/'.$users_res->user_image;
                    }
                    if($users_res->social_id == null){
                        $users_res->social_id = "";
                    }
                    //$users_res->joined_date = date('F Y' , strtotime($users_res->joined_date));
                    //$users_res->loginToken = $jsonrequest['loginToken'];
                    $access_token = $this->getAccessTokens($users_res);
                    $users_res->access_token = $access_token['access_token'];
                    $users_res->user_access = $users_res->user_access;
                    return ['status'=>true,"message"=>'Login Successful!','user_access' => $users_res->user_access,'data'=>$users_res];
                }else{
                    return ['status'=>false,"message"=>'Email or password is invalid.','user_access' => 1];
                }
            }else{
                return ['status'=>false,"message"=>'Email or password is invalid.','user_access' => 1];
            }

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),'user_access' => 1];
        }
    }
    
    public function RegisterUser(Request $request){
        try{
            $jsonrequest = request()->all();
            if($jsonrequest['socialType'] == 1 || $jsonrequest['socialType'] == 2 || $jsonrequest['socialType'] == 3){
                $jsonrequest['loginToken'] = Str::random(30);
                if($jsonrequest['socialId'] == ''){
                    return ['status'=>false,"message"=>'invalid social ID.',"user_access" => 0];
                }
                $users = User::where('social_id' , $jsonrequest['socialId'])->orWhere('email' , $jsonrequest['email'])->first();
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
                            ]);
                    $user->save();
                    $users_res = User::select('user_id','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image','user_access')->where('social_id' , $jsonrequest['socialId'])->orWhere('email' , $jsonrequest['email'])->first();
                    //dd($users_res);
                    if($users_res){
                        if($users_res->user_image == ''){
                            if($users_res->social_image != ''){
                                $users_res->user_image = $users_res->social_image;
                            }else{
                                $users_res->user_image = URL::to('/front/dummy_round.png');
                            }
                        }else{
                            $users_res->user_image = URL::to('/') . '/users/'.$users_res->user_image;
                        }
                        if($users_res->social_id == null){
                            $users_res->social_id = "";
                        }
                        //$users_res->joined_date = date('F Y' , strtotime($users_res->joined_date));
                        //$users_res->loginToken = $jsonrequest['loginToken'];
                        $access_token = $this->getAccessTokens($users_res);
                        $users_res->access_token = $access_token['access_token'];
                        $users_res->user_access = $users_res->user_access;
                        return ['status'=>true,"message"=>'Login Successfully!','user_access' => $users_res->user_access,'data'=>$users_res];
                        
                    }else{
                        return response()->json(['status'=>"failed","message"=>'An error occur during registration!',"user_access" => 1],200);
                    }
                }else{
                    $user_id = time();
                    $users = new User();
                    $users->user_id = $user_id;
                    $users->deviceType = $jsonrequest['deviceType'];
                    $users->social_type = $jsonrequest['socialType'];
                    $users->social_id = $jsonrequest['socialId'];
                    $users->first_name = $jsonrequest['firstName'];
                    $users->last_name = $jsonrequest['lastName'];
                    $users->phone = $jsonrequest['phoneNumber'];
                    $users->email = $jsonrequest['email'];
                    $users->password = Hash::make($jsonrequest['password']);
                    $users->social_image = $jsonrequest['userImage'];
                    $users->created_at = date('Y-m-d h:i:s');
                    $users->updated_at = date('Y-m-d h:i:s');
                    $users->save();
                    $jsonrequest['loginToken'] = Str::random(30);
                    $user = new UserLoginData([
                                'userId' 	=> $users->user_id,
                                'deviceToken' 	=> $jsonrequest['deviceToken'],
                                'deviceType' 	=> $jsonrequest['deviceType'],
                                'appversion' 	=> $jsonrequest['appversion'],
                                'loginToken' 	=> $jsonrequest['loginToken'],
                                'tokenStatus' 	=> 0,
                                'timeZone' 	=> $jsonrequest['timeZone']
                            ]);
                    $user->save();
                    $users_res = User::select('user_id','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image','user_access')->where('user_id' , $user_id)->first();
                    if($users_res){
                        if($users_res->user_image == ''){
                            if($users_res->social_image != ''){
                                $users_res->user_image = $users_res->social_image;
                            }else{
                                $users_res->user_image = URL::to('/front/dummy_round.png');
                            }
                        }else{
                            $users_res->user_image = URL::to('/') . '/users/'.$users_res->user_image;
                        }
                        if($users_res->social_id == null){
                            $users_res->social_id = "";
                        }
                        //$users_res->joined_date = date('F Y' , strtotime($users_res->joined_date));
                        //$users_res->loginToken = $jsonrequest['loginToken'];
                        $access_token = $this->getAccessTokens($users_res);
                        $users_res->access_token = $access_token['access_token'];
                        $users_res->user_access = $users_res->user_access;
                        return ['status'=>true,"message"=>'User registration successful!','user_access' => $users_res->user_access,'data'=>$users_res];
                    }else{
                        return response()->json(['status'=>FALSE,"message"=>'An error occur during registration!',"user_access" => 1],200);
                    }
                }
            }else{
                $res_email = User::where('email' , $jsonrequest['email'])->first();
                if($res_email){
                    return ['status'=>false,"message"=>'This email or username already exists in our database. Please try another!',"user_access" => 1];
                }else{
                    if(isset($jsonrequest['referral_code']) && $jsonrequest['referral_code'] != ''){
                        $referral_code_exists = User::where('referral_code',$jsonrequest['referral_code'])->first();
                        if($referral_code_exists == null){
                            return ['status'=>false,"message"=>'User enterd wrong referral code',"user_access" => 0];
                        }
                    }
                    $first_name = $jsonrequest['firstName'];
                    $ref_code = substr(str_shuffle('123456789123456789123456789321654987'),0,4);
                    $referral_code = strtolower(substr($first_name, 0, 4)).$ref_code;
                    $user_id = time();
                    $users = new User();
                    $users->user_id = $user_id;
                    $users->deviceType = $jsonrequest['deviceType'];
                    $users->social_type = $jsonrequest['socialType'];
                    $users->social_id = $jsonrequest['socialId'];
                    $users->first_name = $jsonrequest['firstName'];
                    $users->last_name = $jsonrequest['lastName'];
                    $users->phone = $jsonrequest['phoneNumber'];
                    $users->email = $jsonrequest['email'];
                    $users->password = Hash::make($jsonrequest['password']);
                    $users->social_image = $jsonrequest['userImage'];
                    $users->referral_code = $referral_code;
                    $users->created_at = date('Y-m-d h:i:s');
                    $users->updated_at = date('Y-m-d h:i:s');
                    $users->save();
                    $jsonrequest['loginToken'] = Str::random(30);
                    $user = new UserLoginData([
                                'userId' 	    => $user_id,
                                'deviceToken'   => $jsonrequest['deviceToken'],
                                'deviceType'    => $jsonrequest['deviceType'],
                                'appversion'    => $jsonrequest['appversion'],
                                'loginToken'    => $jsonrequest['loginToken'],
                                'tokenStatus'   => 0,
                                'timeZone' 	    => $jsonrequest['timeZone']
                            ]);
                    $user->save();
                    if(isset($jsonrequest['referral_code']) && $jsonrequest['referral_code'] != ''){
                        $ref_user_id = $referral_code_exists->user_id;
                        $amount = Setting::first()->referral_amount; 
                        DB::table('transactions')->insert(['from_user_id' => $ref_user_id ,'to_user_id' => $user_id , 'amount'=> $amount,'cash_flow_type'=> 1,'trans_type'=> 0,'status'=> 1,  'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]);
                        DB::table('transactions')->insert(['from_user_id' => $user_id ,'to_user_id' => $ref_user_id ,'amount'=> $amount,'cash_flow_type'=> 0,'trans_type'=> 0,'status'=> 0,  'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]);

                        $wallets1 = DB::table('wallets')->where('user_id' , $user_id)->first();
                        if($wallets1){
                            $total_amount = $wallets1->total_amount + $amount;
                            DB::table('wallets')->where('user_id', $user_id)->update(['total_amount' => $total_amount]);
                            //$this->send_wallet_email($jsonrequest['userFullname'],$jsonrequest['userEmail'],$amount);
                        }else{
                            DB::table('wallets')->insert(['user_id' => $user_id , 'total_amount' => $amount]);
                            //$this->send_wallet_email($jsonrequest['userFullname'],$jsonrequest['userEmail'],$amount);
                        }

                        $wallets2 = DB::table('wallets')->where('user_id' , $ref_user_id)->first();
                        if($wallets2){
                            $total_amount = $wallets2->total_amount + $amount;
                            DB::table('wallets')->where('user_id', $ref_user_id)->update(['total_amount' => $total_amount]);
                            //$this->send_wallet_email($ref_userFullname,$ref_userEmail,$amount);
                        }else{
                            DB::table('wallets')->insert(['user_id' => $ref_user_id , 'total_amount' => $amount]);
                            //$this->send_wallet_email($ref_userFullname,$ref_userEmail,$amount);
                        }
                        
                        //Send Notification
                        $this->sendNotificationToUser( $user_id , $ref_user_id , $amount);
                        //End Notification
                    }
                    $email_subject = 'Welcome Email';
                    $user_name = $jsonrequest['firstName'];
                    $email_from = 'info@husdng.com';
                    //$this->send_email_register($jsonrequest['email'], $user_name, $email_subject, $email_from, 'register_email');

                    $users_res = User::select('user_id','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image','user_access')->where('user_id' , $user_id)->first();
                    //dd($users_res);
                    if($users_res){
                        if($users_res->user_image == ''){
                            if($users_res->social_image != ''){
                                $users_res->user_image = $users_res->social_image;
                            }else{
                                $users_res->user_image = URL::to('/front/dummy_round.png');
                            }
                        }else{
                            $users_res->user_image = URL::to('/') . '/users/'.$users_res->user_image;
                        }
                        if($users_res->social_id == null){
                            $users_res->social_id = "";
                        }
                        //$users_res->joined_date = date('F Y' , strtotime($users_res->joined_date));
                        //$users_res->loginToken = $jsonrequest['loginToken'];
                        $access_token = $this->getAccessTokens($users_res);
                        $users_res->access_token = $access_token['access_token'];
                        $users_res->user_access = $users_res->user_access;
                        return ['status'=>true,"message"=>'User registration successful!','user_access' => $users_res->user_access,'data'=>$users_res];
                    }else{
                        return ['status'=>false,"message"=>'An error occurred during registration!','user_access' => 1];
                    }
                }
            }
        }catch(\Exception $e){ 
            return ['status'=>false,"message"=>$e->getMessage(),'user_access' => 1];
        }
    }

    public function GetProfile(Request $request){
        try{
            $user_id = $request->user()->user_id;
            $validator = Validator::make(request()->all(), [
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),'user_access' => 1];
            }//..... end if() .....//
            $fallowerArr = [];
            $fallowingArr = [];
            $users_res = User::select('user_id','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image','user_access')->where('user_id' , request()->user_id)->first();
            if($users_res->social_id == null){
                    $users_res->social_id = "";
                }
            if($users_res->user_image == ''){
                if($users_res->social_image != ''){
                    $users_res->user_image = $users_res->social_image;
                }else{
                    $users_res->user_image = URL::to('/front/dummy_round.png');
                }
            }else{
                $users_res->user_image = URL::to('/') . '/users/'.$users_res->user_image;
            }
            
            //End Following
            //$users_res->joined_date = date('F Y' , strtotime($users_res->joined_date));
            //$users_res->loginToken = "";
            $access_token = $this->getAccessTokens($users_res);
            $users_res->access_token = $access_token['access_token'];
            $users_res->user_access = $users_res->user_access;
            return ['status'=>true,"message"=>'User data found.','user_access' => request()->user()->user_access,'data'=>$users_res];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),'user_access' => 1];
        }
    }

    public function EditProfile(Request $request){
        try{
            $user_id = $request->user()->id;
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $users = User::where('id' , $user_id)->first();
//            if($users->user_name != $jsonrequest['userName']){
//                $res_email = User::where('user_name' , $jsonrequest['userName'])->first();
//                if($res_email){
//                    return ['status'=>false,"message"=>'This username already exists in our database. Please try another!',"user_access" => 1];
//                }
//            }
            if($jsonrequest['userImage'] != ''){
                $path = base_path()."/public/users";
                if($users->user_image != ''){
                    array_map('unlink', glob("$path/".$users->user_image));
                }
                $text = str_replace(' ', '+', $jsonrequest['userImage']);
                $image = base64_decode($text);
                //$image = base64_decode($request->image);
                $image_name = uniqid() . '.jpeg';
                $path1 = $path .'/'. $image_name;
                file_put_contents($path1, $image);
                User::where('id', $user_id)
                    ->update([
                        'first_name' => $jsonrequest['firstName'],
                        'last_name' => $jsonrequest['lastName'],
                        'phone' => $jsonrequest['phoneNumber'],
                        'user_image' => $image_name,
                        'social_image' => ''
                    ]);
            }else{
                User::where('id', $user_id)
                    ->update([
                        'first_name' => $jsonrequest['firstName'],
                        'last_name' => $jsonrequest['lastName'],
                        'phone' => $jsonrequest['phoneNumber']
                    ]);
            }
            $users_res = User::select('user_id','social_id','social_type','first_name','last_name','user_name','email','phone','user_image','social_image','user_access')->where('id' , $user_id )->first();
            if($users_res){
                if($users_res->social_id == null){
                    $users_res->social_id = "";
                }
                if($users_res->user_image == ''){
                    if($users_res->social_image != ''){
                        $users_res->user_image = $users_res->social_image;
                    }else{
                        $users_res->user_image = URL::to('/front/dummy_round.png');
                    }
                }else{
                    $users_res->user_image = URL::to('/') . '/users/'.$users_res->user_image;
                }
                //$users_res->joined_date = date('F Y' , strtotime($users_res->joined_date));
                //$users_res->loginToken = "";
                $access_token = $this->getAccessTokens($users_res);
                $users_res->access_token = $access_token['access_token'];
                $users_res->user_access = $users_res->user_access;
                return ['status'=>true,"message"=>'User profile updated successfully!','data' => $users_res];
            }else{
                return ['status'=>false,"message"=>'No data found.',"user_access"=>request()->user()->user_access];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access" => 1];
        }
    }

    public function ChangePassword(Request $request){
        try{
            $user_id = $request->user()->id;
            $user_password = User::where('id' , $user_id)->first()->password;
            if(Hash::check(request()->oldpass, $user_password)){
                User::where('id', $user_id)
                        ->update([
                            'password' => Hash::make(request()->newpass) 
                        ]);
                return ['status'=>true,"message"=>'Password has been updated successfully!',"user_access"=>request()->user()->user_access];
            }else{
                return ['status'=>false,"message"=>"Old password does not matched.","user_access"=>request()->user()->user_access];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>0];
        }
    }

    public function ForgotPassword(Request $request) {
        //try{
            if(request()->email == ''){
                return ['status'=>false,"message"=>'email  is required!'];
            }
            $users = User::where('email', request()->email)->first();
            if ($users) {
                $user_email = request()->email;
                $user_name = $users->first_name.' '.$users->last_name;
                $pin = base64_encode(request()->email);
                $link = URL::to('/') . '/user/reset_password/' . $pin;
                $logo = URL::to('/') . '/front/assets/images/Hushdng.png';
                $subject = 'Password Reset Request';
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $message = '
                <html>
                	<head>
                		<title>Forgotten Password</title>
                	</head>
                	<body>
                		<div style="max-width:500px;margin:0px auto;font-family:arial;">
                			<div style="width:100%;float:left;display:table;border-bottom:1px solid #ddd;padding:10px;">
                				<img src="'.$logo.'" width="150" style="display:block;">
                			</div>
                			<div style="font-size:17px;font-family:arial;color:#555;display:table;width:100%;padding:10px;margin-top:15px;min-height:200px;">
                				<p>Hi '.$user_name.',</p>
                				<p>We received a request to reset your HushD password. Click the button below.</p>
                				<br>
                				<p>
                				<a href="'.$link.'" style="background: #ff117b; color: #fff !important;text-decoration:none;padding:10px 20px 10px 20px;">Set New Password</a>
                				</p>
                			</div>
                			<div style="font-size:17px;font-family:arial;color:#555;display:table;width:100%;padding:10px;margin-top:15px;border-bottom:1px solid #ddd;">
                				<p><b>Didn\'t request this change?</b><br>
                				If you didn\'t request a new password... Ignore this mail.</p>
                			</div>
                			<p style="font-size:10px;color:#999;font-family:arial">
                				This message was sent to '.$user_email.' at your request.<br>
                				The HushD Team!.
                			</p>
                		
                		</div>
                	</body>
                </html>';
                $user_email = 'rizwan@decodershub.com';
                if(mail($user_email,$subject,$message,$headers)){
                    return ['status'=>true,"message"=>'Please check email to reset password.',"user_access"=>1];
                }else{
                    return ['status'=>true,"message"=>'Something went wrong!! Try again later',"user_access"=>1];
                }
                
                //$pin = base64_encode(request()->email);
                //$link = URL::to('/') . '/user/reset_password/' . $pin;
                //$email_subject = 'Forgot Password';
                //$user_name = $users->email;
                //$email_from = 'website@hushdng.com';
                //$this->send_email(request()->email, $user_name, $email_subject, $email_from, $link, 'forget_email');
                //return ['status'=>true,"message"=>'Please check email to reset password.',"user_access"=>1];
            } else {
                return ['status'=>true,"message"=>'Email does not exist.',"user_access"=>1];
            }
        //}catch(\Exception $e){
            //return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        //}
    }

    public function send_email_register($email, $user_name, $email_subject, $email_from,$view_name) {
        $res['userName'] = $user_name;
        $res['url'] = url('/');
        Mail::send('email/'.$view_name , $res, function ($message) use ($email_from, $email, $user_name, $email_subject) {
            $message->from($email_from, $name = 'HushD');
            $message->to($email, $user_name)->subject($email_subject);
            //$message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
        });
    }
    
    public function Logout(Request $request){
        try{
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $user = $request->user();
            UserLoginData::where('loginToken',$jsonrequest['loginToken'])->delete();
            request()->user()->token()->revoke();
            return ['status'=>true,"message"=>"Logout successful.","user_access"=>1];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }

    public function GetDeviceToken(){
        try{
            $checkUser = UserLoginData::select('deviceToken as device_token')->where('userId',request()->user_id)->where('tokenStatus',0)->get();
            if($checkUser){
                return ['status'=>true,"message"=>"user tokens Data found.","user_access"=>request()->user()->user_access,'user_token' => $checkUser];
            }else{
                return ['status'=>false,"message"=>"No data found","user_access"=>request()->user()->user_access,"user_token" => []];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>0];
        }
    }
    
    public function GetYouTubeKey(Request $request){
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
                return ['status'=>true,"message"=>"Youtube Key found.",'youtube_key' => $settings->youtube_key,'payment_mod' => $settings->payment_mod,'api_key' => $api_key,'contract' => $contract];
            }else{
                return ['status'=>true,"message"=>"Youtube Key found.",'youtube_key' => $settings->youtube_key,'payment_mod' => '','api_key' => '','contract' => ''];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage()];
        }
    }
    
    public function GetHomeData(Request $request){
        try{
            $user = $request->user();
            $data = (object) array();
            $validator = Validator::make(request()->all(), [
                'device_type' => 'required',
                'app_version' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $settings = DB::table('settings')->first();
            if($settings){
                if($settings->payment_mod == 1){
                    $api_key = $settings->api_key_live;
                    $contract = $settings->contract_live;
                }else{
                    $api_key = $settings->api_key_local;
                    $contract = $settings->contract_local;
                }
                $data->youtube_key = $settings->youtube_key;
                $data->api_key = $api_key;
                $data->contract = $contract;
                if(request()->device_type == 1){
                    if(request()->app_version < $settings->android_version){
                        $data->is_update = 1;
                    }else{
                        $data->is_update = 0;
                    }
                }else{
                    if(request()->app_version < $settings->ios_version){
                        $data->is_update = 1;
                    }else{
                        $data->is_update = 0;
                    }
                }
                
                $data->cart_count = CartItem::where('user_id' , $user->user_id)->count();
//                $data->notification_count =Notification::where('reciever_user_id', $user->user_id)->where('status' , 0)->count();
//                $available_balance = DB::table('transactions')->where('to_user_id' , $user->user_id)->whereIn('trans_type' , array(0,2))->where('status' , 1)->sum('amount');
//                $withdrawal_balance = DB::table('transactions')->where('to_user_id' , $user->user_id)->where('trans_type' , 1)->sum('amount');
//                $data->available_wallet_amount = number_format($available_balance - $withdrawal_balance, 2, '.', '');
//                $pd_balance = DB::table('transactions')->where('to_user_id' , $user->user_id)->where('trans_type' , 0)->where('status' , 0)->sum('amount');
//                $data->pending_wallet_amount = number_format($pd_balance, 2, '.', '');
//                $data->referral_url = url('/signup?referral_code='.$user->referral_code);
//                $data->referral_amount = Setting::first()->referral_amount;
                return ['status'=>true,"message"=>"Home data found.","user_access"=>request()->user()->user_access,'data' => $data];
            }else{
                return ['status'=>true,"message"=>"No data found.","user_access"=>request()->user()->user_access,'data' => $data];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function SearchUsers(Request $request){
        try{
            $validator = Validator::make(request()->all(), [
                'keyword' => 'required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $usersArr = [];
            $users = User::select('user_id','user_type','first_name','last_name', 'email','user_name','user_image','social_image','referral_code')->where('first_name', 'like', '%'.request()->keyword.'%')->get();
            if(count($users) > 0){
                foreach($users as $row){
                    if($row->user_image == ''){
                        $row->user_image = !empty($row->social_image) ? url("/")."/".$row->social_image : "url('/front/dummy_round.png')";
                    }else{
                        $row->user_image = url('/') . '/users/'.$row->user_image;
                    }
                    $userFollowing = Follower::select('is_follow')->where('from_user_id' , request()->user_id)->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                    if($userFollowing){
                        $row->is_follow = 1;
                    }else{
                        $row->is_follow = 0;
                    }
                    $usersArr[] = $row;
                }
                return ['status'=>true,"message"=>'User data found.',"user_access"=>1,'users'=>$usersArr];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>1,'users'=>$usersArr];
            }
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function updateProductMataInfo(Request $request){
        try{
            $usersArr = [];
            $products = Product::select('product_id','product_title','product_description')->get();
            if(count($products) > 0){
                foreach($products as $row){
                    $mata_title = $row->product_title;
                    $mata_description = $row->product_description;
                    Product::where('product_id' , $row->product_id)->update(['mata_title' => $mata_title,'mata_description' => $mata_description]);
                }
                return ['status'=>true,"message"=>'Product mata information updated successfully.'];
            }else{
                return ['status'=>true,"message"=>'No data found.'];
            }
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage()];
        }
    }
    
    public function updateRefferal(Request $request){
        try{
            $usersArr = [];
            $users = User::select('user_id','first_name','last_name')->get();
            if(count($users) > 0){
                foreach($users as $row){
                    $first_name = $row->first_name;
                    $ref_code = substr(str_shuffle('123456789123456789123456789321654987'),0,4);
                    $referral_code = strtolower(substr($first_name, 0, 4)).$ref_code;
                    User::where('user_id' , $row->user_id)->update(['referral_code' => $referral_code]);
                }
                return ['status'=>true,"message"=>'Referal code updated.'];
            }else{
                return ['status'=>true,"message"=>'No data found.'];
            }
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage()];
        }
    }
    
    function getDatesFromRange($start, $end){
        $dates = array($start);
        while(end($dates) < $end){
            $dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
        }
        return $dates;
    }
    
    public function sendNotificationToUser($ref_user_id , $user_id , $amount){
        
        $refusername = User::where('user_id' , $user_id)->first()->first_name;
        $othusername = User::where('user_id' , $ref_user_id)->first()->first_name;
        $users = UserLoginData::select('deviceToken','userId')->where('userId' , $user_id)->where('tokenStatus',0)->get();
        if(count($users) > 0){
            $deviceToken = [];
            foreach($users as $key => $val ){
                $deviceToken[] = $val->deviceToken;
                //$deviceToken[] = 'ep4JTpZFT-KsB8ejoM4NoI:APA91bHduV1A7Q3TtGI6PgX0GNEaZ-wPvEOQmctFxCqpf615h1v1JRLfx9lqinxF1n8_wYU0T9koz1g5k2H3Be6DAjwX-8o8qNg094O7tJ4WtW3aj9_jQSyhq7w3IAxa1-z89-d_kWXn';
            }
            $title = 'Referral code redeemed';
            $body = 'Hey '.$refusername.' your referral code has been redeemed by '.$othusername.' successfully.You will get ₦'.$amount.' once '.$othusername.' makes the first order.';
            $type = 'Referral';
            $message =  array(
                            'type' => 'notification',
                            'title' => $title,
                            'body' => $body,
                            'username' => $refusername,
                            'user_id' => $user_id,
                            'notification_userid' => $ref_user_id,
                            'type1' => 'referral',
                            'sound'=> 'default',
                            'content-available'=> true,
                            'icon' => 'chat1'
                        );
            commonHelper::firebase($deviceToken,$message);
            commonHelper::saveNotification($ref_user_id,$user_id,$othusername,$title,$body,$type,0);
        }
    }
    
    public function send_email($email, $user_name, $email_subject, $email_from, $link,$view_name) {
        $res['userName'] = $user_name;
        $res['activationLink'] = $link;
        $res['url'] = url('/');
        Mail::send('email/'.$view_name , $res, function ($message) use ($email_from, $email, $user_name, $email_subject) {
            $message->from($email_from, $name = 'HushD');
            $message->to($email, $user_name)->subject($email_subject);
            $message->to('naqeeb@decodershub.com', $user_name)->subject($email_subject);
        });
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
            if($year == 1){
                return  $string .= "$year Year ago";
            }
            return  $string .= "$year Years ago";
        }
        if($momth > 0){
            if($momth == 1){
                return  $string .= "$momth Month ago";
            }
            return  $string .= "$momth Months ago";
        }
        if($weeks > 0){
            if($weeks == 1){
                return  $string .= "$weeks week ago";
            }
            return  $string .= "$weeks weeks ago";
        }
//        if($hours > 24 && $hours < 48){
//                return $string .= "Yesterday";
//        }
//        if($hours > 1 && $hours < 24){
//                return $string .= "Today";
//        }
        if($days > 0){
            if($days == 1){
                return  $string .= "$days day ago";
            }
                return  $string .= "$days days ago";
        }
        if($hours > 0){
            if($hours == 1){
                return $string .= "$hours hour ago";
            }
            return $string .= "$hours hours ago";
        }
        if($minutes > 0){
            if($minutes == 1){
                return $string .= "$minutes minute ago";
            }
            return $string .= "$minutes minutes ago";
        }
        if ($seconds < 59){
                return $string .= "Just now";
        }
    }

    private function getAccessTokens(User $user)
    {
        return ['access_token' => $user->createToken($user->email)->accessToken];
    }//..... end of getAccessTokens() ......//
}
