<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Session;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Image;
use File;
use DB;
use URL;
use PDF;
use Mail;

class LoginControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function showLoginForm(Request $request) { 
        $data['title'] = 'Login';
        if ($request->isMethod('get')) {
            return view('admin/login/login' , $data);
        }else{
            $admin_email = $request->email;
            $password = $request->password;
            $validator = Validator::make($request->all(), [
                                        'email' => 'required',
                                        'password' => 'required',
                                    ]);
            if ($validator->fails()) {
                return redirect('admin')
                            ->withInput()
                            ->withErrors($validator);
            }else{
                $users = Admin::where('admin_email', $admin_email)->where('admin_password', md5($password))->first();
                //dd($users);
                if($users){
                    $remember = isset($request->remember) ? '1':'0';
                    if($remember == 1){
                        setcookie ("username",$admin_email,time()+3600 * 24 * 365);
                        setcookie ("password",$password,time()+3600 * 24 * 365);
                    }else{
                        setcookie ("username" , '' , time()+ 3600);
                        setcookie ("password" , '' , time()+ 3600);
                    }
                    Session::put('admin_id', $users->admin_id);
                    Session::put('admin_name', $users->admin_name);
                    Session::put('avater', $users->admin_avatar);
                    Session::flash('error_msg', 'You are logined in successfully!'); 
                    return redirect('admin/dashboard');
                }else{
                    Session::flash('error_msg', 'Email or password is invalid!'); 
                    return redirect('admin');
                } 
            }
        }
    }
	
    public function logout(Request $request) {
        $request->session()->flush();
        //$request->session()->regenerate();
        return redirect('admin');
    }
    
    public function forgetPassword(Request $request){
         $data['title'] = 'Forgot Password';
        if ($request->isMethod('get')) {
            return view('admin/login/forgot_password' , $data);
        }else{
            $email = $request->forgot_email;
            $users = Admin::where('admin_email' , $email)->first();
            if ($users) {
//                $pin = base64_encode($email);
//                $link = URL::to('/') . '/admin/reset_password/' . $pin;
//                $email_subject = 'Forgot Password';
//                $user_name = $users->admin_name;
//                $email_from = 'hello@pricepally.com';
//                $this->send_email_forgot($email, $user_name, $email_subject, $email_from, $link, 'forget_email');
                
                $user_email = request()->email;
                $user_name = $users->admin_name;
                $pin = base64_encode(request()->email);
                $link = URL::to('/') . '/admin/reset_password/' . $pin;
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
                    Session::flash('error_msg', 'Please check email to reset password.'); 
                    return redirect('admin');
                }else{
                    Session::flash('error_msg', 'Something went wrong!! Try again later'); 
                    return redirect('admin/forget/password');
                }
           }else{
                Session::flash('error_msg', 'Email does not exist on our database!'); 
                return redirect('admin/forget/password');
           }
        }
    }
	
    public function resetUserPassword(Request $request , $email = '') {
        $data['title'] = 'Reset Password';
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

                Session::flash('error_msg', 'your password has been reset successfully'); 
                return redirect('login');
            }
        }
    }
    
    public function resetPassword(Request $request , $email = '') {
        $data['title'] = 'Reset Password';
        if ($request->isMethod('get')) {
            $data['email'] = $email;
            return view('admin/login/reset_password', $data);
        }else{
            $validator = Validator::make($request->all(), [
                    'new_password' => 'required|same:confirm_password',
                    'confirm_password' => 'required',
            ]);
            if ($validator->fails()) {
                    return redirect('reset_password/'.$email)
                            ->withInput()
                            ->withErrors($validator);
            }else{
                    $user_email = base64_decode($email);
                    $password = $request->input("new_password");

                    Admin::where('admin_email', $user_email)
                    ->update(['admin_password' => md5($password)]);

                    Session::flash('error_msg', 'your password has been reset successfully'); 
                    return redirect('admin/login');
            }
        }
    }
    
    public function send_email_forgot($email, $user_name, $email_subject, $email_from, $link,$view_name) {
        $res['userName'] = $user_name;
        $res['activationLink'] = $link;
        Mail::send('email/'.$view_name , $res, function ($message) use ($email_from, $email, $user_name, $email_subject) {
            $message->from($email_from, $name = 'Pricepally');
            $message->to($email, $user_name)->subject($email_subject);
            //$message->to('rizwan@decodershub.com', $user_name)->subject($email_subject);
        });
    }
}
