<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Session;
use Validator;
use App\Models\User;
use App\Models\Newsletter;
use App\Models\UserLoginData;
use App\Models\UserInsurance;
use App\Models\Testimonial;
use App\Models\Partner;
use App\Models\PartnerPlan;
use App\Models\CartItem;
use App\Models\Setting;
use App\Models\Former;
use App\Classes\CommonLibrary;
use commonHelper;
use Illuminate\Support\Facades\Hash;
use URL;
use DB;
use Mail;


class LoginControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function showLoginForm(Request $request) { 
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email', $email)->first();
        if($user){
            $user_password = $user->password;
            if(Hash::check($request->password, $user_password)){
                
                Session::put('user_id', $user->user_id);
                Session::put('first_name', $user->first_name);
                Session::put('email', $user->email);
                Session::put('last_name', $user->last_name);
                return response()->json(['status'=>"success", "message" => 'You are logined in successfully!'],200);
            }else{
                return response()->json(['status'=>"failed", "message" => 'Email or password is invalid!'],200);
            }
        }else{
            return response()->json(['status'=>"failed", "message" => 'Email or password is invalid!'],200);  
        }
            
        
    }
    
    public function checkEmail(Request $request){
        try{
            $user = User::where('email', $request->email)->first();
            if($user){
                return response()->json(['status'=>"failed" , "message" => 'This email already exist in our database.'],200);
            }else{
                $user_id = time();
                $users = new User();
                $users->user_id = $user_id;
                $users->first_name = $request->first_name;
                $users->last_name = $request->last_name;
                $users->email = $request->email;
                $users->phone = $request->phone;
                $users->mobile = $request->mobile;
                $users->nationality = $request->nationality;
                $users->gender = $request->gender;
                $users->dob = $request->dob;
                $users->password = Hash::make($request->password);
                $users->skills = $request->skills;
                $users->why_join = $request->why_join;
                $users->employee_status = $request->employee_status;
                $users->save();
                Session::put('user_id', $user_id);
                Session::put('first_name', $request->first_name);
                Session::put('last_name', $request->last_name);
                return response()->json(['status'=>"success" , "message" => 'New account created successfully!'],200);  
            }
        } catch (Exception $ex) {
            return response()->json(['status'=>"failed" , "message" => 'Oops! went something wrong.'],200); 
        }
                
    }
    
    public function newsletter(Request $request){
        try{
            $user = Newsletter::where('email', $request->email)->first();
            if($user){
                return "allready_exist";
            }else{
                $users = new Newsletter();
                $users->email = $request->email;
                $users->created_at = time();
                $users->updated_at = time();
                $users->save();
                $link = '';
                $email_subject = 'Newsletter Email';
                $user_name = $request->email;
                $email_from = 'info@hushdng.com';
                //$this->send_email($request->email, $user_name, $email_subject, $email_from, $link, 'newslatter_email');
                return "success";
            }
        } catch (Exception $ex) {
            return "failed";
        }
                
    }
	
    public function logout(Request $request) {
        $user_id = Session::get('user_id');
        $request->session()->flush();
        Session::flash('logout_msg', 'Logged out successfully'); 
        return redirect('/');
    }
    
    public function forgetPassword(Request $request){
        if ($request->isMethod('get')) {
            return view('front/auth/forgot');
        }else{
            $email = $request->email;
            $users = User::where('email', $email)->first();
            if ($users) {
                //$pin = base64_encode($email);
                //$link = URL::to('/') . '/user/reset_password/' . $pin;
                //$email_subject = 'Forgotten password';
                //$user_name = $users->first_name;
                //$email_from = 'info@hushdng.com';
                //$this->send_email($email, $user_name, $email_subject, $email_from, $link, 'forget_email');
                //Session::flash('success_msg', 'Please check email to reset password.'); 
                //return response()->json(['status'=>"success"],200);
                
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
                if(mail($user_email,$subject,$message,$headers)){
                    return response()->json(['status'=>"success","message"=>'Please check email to reset password.',"user_access"=>1],200);
                }else{
                    return response()->json(['status'=>"notexist","message"=>'Something went wrong!! Try again later',"user_access"=>1],200);
                }
            }else{
                return response()->json(['status'=>"notexist","message"=>'Given email is not registed with us.',"user_access"=>1],200); 
            }
        }
    }
	
    public function send_email($email, $user_name, $email_subject, $email_from, $link,$view_name) {
        $res['userName'] = $user_name;
        $res['activationLink'] = $link;
        $res['url'] = url('/');
        //return view('email/'.$view_name , $res);
        Mail::send('email/'.$view_name , $res, function ($message) use ($email_from, $email, $user_name, $email_subject) {
            $message->from($email_from, $name = 'Hushdng');
            $message->to($email, $user_name)->subject($email_subject);
            $message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
        });
    }
	
    public function resetPassword(Request $request , $email = '') {
        if ($request->isMethod('get')) {
            $data['email'] = $email;
            return view('admin/login/reset_password', $data);
        }else{
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|same:confirm_password',
                'confirm_password' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect('user/reset_password/'.$email)
                        ->withInput()
                        ->withErrors($validator);

            }else{
                $user_email = base64_decode($email);
                $password = $request->input("new_password");

                User::where('email', $user_email)
                ->update(['password' => Hash::make($password)]);

                Session::flash('change_success_msg', 'your password has been reset successfully'); 
                return redirect('user/reset_password/'.$email);
            }
        }
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
                            'type1' => 'referral',
                            'sound'=> 'default',
                            'content-available'=> true,
                            'icon' => 'chat1'
                        );
            commonHelper::firebase($deviceToken,$message);
            commonHelper::saveNotification($ref_user_id,$user_id,$othusername,$title,$body,$type,0);
        }
    }
}