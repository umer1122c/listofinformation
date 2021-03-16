<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use App\Models\UserLoginData;
use App\Models\Subscription;
use Session;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use URL;
use Mail;
use Image;
use Validator;
use App\Models\Packege;
use App\Models\Notification;
use App\Classes\CommonLibrary;
use commonHelper;

class UserControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function view(){
        $data['title'] = 'Manage Users';
        $data['class'] = 'users';
        $data['table'] = 'Manage Users';
        return view("admin.users.list",$data);
    }



    public function listView(){
        
        return DataTables::of(User::query())
                ->editColumn('delete', function ($data) {
                    if($data->user_access == 0){
                        $btn = "<a href='".url('/')."/admin/users/1/".$data->user_id."'  class='btn btn-warning' style='padding: 5px 10px;' id='".$data->id."' title='Block'><i class='ti-na pdd-right-0'></i><span>Block</span></a>";
                    }else{
                        $btn = "<a href='".url('/')."/admin/users/0/".$data->user_id."'  class='btn btn-success' style='padding: 5px 10px;' id='".$data->id."' title='Access'><i class='ei-circle pdd-right-0'></i><span>Access</span></a>";
                    }
                    return $btn;
                })
                ->rawColumns(['delete' => 'delete','action' => 'action'])
                ->addColumn('action', function($data){
                    return "<a href='".url('/')."/admin/user/edit/".$data->user_id."'  class='all_button btn  btn-sm btn-info edit_button'  data-value='".$data."' id='".$data->user_id."'>Edit</a>";
                })->make(true);
    }
	
    public function index(){
            $data['title'] = 'Manage Users';
            $data['class'] = 'users';
            $data['table'] = 'Manage Users';
            $data['users'] = User::get();
            return view('admin/users/index' , $data);
    }
	
    public function create(Request $request){
            $data['title'] = 'Manage Users';
            $data['class'] = 'users';
            $data['table'] = 'Add User';
            if ($request->isMethod('get')) {
                    return view('admin/users/create' , $data);
            }else{
                    $validator = Validator::make($request->all(), [
                                            'first_name' => 'required',
                                            'last_name' => 'required',
                                            'email' => 'required|unique:users|max:255',
                                            'password' => 'required|same:confirm_password',
                                            'confirm_password' => 'required'
                                    ]);
                    if ($validator->fails()) {
                            return redirect('admin/user/add')
                                            ->withInput()
                                            ->withErrors($validator);
                    }else{
                            if ($request->hasFile('user_image')) {
                                    $image_name = '';
                                    $imageTempName = $request->file('user_image')->getPathname();
                                    $venue_img_extension = $request->user_image->extension();
                                    $imageName = uniqid() . '.' . $venue_img_extension;
                                    $path = base_path() . '/users/';
                                    $request->file('user_image')->move($path, $imageName);
                                    $image = $imageName;

                                    $path_thumbs = base_path() . '/users/thumbs';

                                    Image::make($path . $imageName, array(
                                            'width' => 200,
                                            'height' => 200,
                                            'grayscale' => false
                                    ))->save($path_thumbs . '/thumb_' . $imageName);
                            } else {
                                    $image = '';

                            }
                            $users = new User();
                            $users->first_name = $request->first_name;
                            $users->last_name = $request->last_name;
                            $users->email = $request->email;
                            $users->password = Hash::make($request->password);
                            $users->no_of_properties = $request->no_of_properties;
                            $users->phone = $request->phone;
                            $users->contact_info = $request->contact_info;
                            $users->business_phone = $request->business_phone;
                            $users->user_image = $image;
                            $users->created_at = date('Y-m-d h:i:s');
                            $users->updated_at = date('Y-m-d h:i:s');
                            $users->save();
                            Session::flash('success_msg', 'User has been added successfully'); 
                            return redirect('admin/users');
                    }
            }
    }

    public function edit(Request $request , $id = ''){
        $data['title'] = 'Manage Users';
        $data['class'] = 'users';
        $data['table'] = 'Edit User';
        if ($request->isMethod('get')) {
            $data['user'] = User::where('user_id' , $id)->first();
            return view('admin/users/edit' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                    'first_name' => 'required'
                            ]);
            if ($validator->fails()) {
                    return redirect('admin/user/edit/'.$id)
                                    ->withInput()
                                    ->withErrors($validator);
            }else{
                if ($request->hasFile('user_image')) {
                    $image_name = '';
                    $admin_img = User::where('user_id' , $id)->first();
                    if($admin_img){
                        @unlink(base_path() . '/users/' . $admin_img->user_image);
                    }
                    $imageTempName = $request->file('user_image')->getPathname();
                    $venue_img_extension = $request->user_image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/users/';
                    $request->file('user_image')->move($path, $imageName);
                    $image = $imageName;
                } else {
                    $image = '';
                    $admin_img = User::where('user_id' , $id)->first();
                    if($admin_img){
                        $image = $admin_img->user_image;
                    }
                }
                User::where('user_id', $id)
                                  ->update([
                                        'first_name' => $request->first_name,
                                        'last_name' => $request->last_name,
                                        'phone' => $request->phone,
                                        'business_name' => $request->business_name,
                                        'user_image' => $image,
                                        'social_image' => ''
                                    ]);
                Session::flash('success_msg', 'User has been updated successfully.'); 
                return redirect('admin/users');
            }
        }
    }

    public function updateStatus($status = '' , $user_id = '') {
        User::where('user_id', $user_id)->update(['user_access' => $status]);
        if($status == 0){
            Session::flash('success_msg', 'User has been inactive successfully.'); 
        }else{
            Session::flash('success_msg', 'User has been active successfully.'); 
        }
        return redirect('admin/users');
    }

    public function delete($user_id = '') {
        $delete = User::where('user_id' , $user_id)->delete();
        Session::flash('del_msg', 'User has been deleted successfully.'); 
        return redirect('admin/users');
    }
    
    public function sendPushNotification(Request $request){
            $data['title'] = 'Send Push Notification';
            $data['class'] = 'users';
            $data['table'] = 'Send Push Notification';
            if ($request->isMethod('get')) {
                $data['users'] = User::get();
                return view('admin/users/notification' , $data);
            }else{
                    $validator = Validator::make($request->all(), [
                                            'title' => 'required',
                                    ]);
                    if ($validator->fails()) {
                            return redirect('admin/send/notifications')
                                            ->withInput()
                                            ->withErrors($validator);
                    }else{
                        $title = $request->title;
                        $message_noti = $request->message;
                        $is_user = $request->is_user;
                        if($is_user == 'All'){
                            $users = User::get();
                        }else{
                            $user_ids = $request->user_id;
                            $users = User::whereIn('user_id' , $user_ids)->get();
                        }
                        if(count($users) > 0){
                            foreach($users as $row){
                                //Send Notification
                                $this->sendNotificationToUser($row->user_id ,0,$request->title,$request->message);
                                //End Notification
                            }
                        }
                        Session::flash('success_msg', 'Push Notification has been send to all users successfully'); 
                        return redirect('admin/send/notifications');
                    }
            }
    }
    
    public function sendNotificationToUser($user_id , $follower_id,$title,$body){
        
        $username = User::where('user_id' , $user_id)->first()->first_name;
        $users = UserLoginData::select('deviceToken','userId')->where('userId' , $user_id)->where('tokenStatus',0)->get();
        if(count($users) > 0){
            $deviceToken = [];
            foreach($users as $key => $val ){
                $deviceToken[] = $val->deviceToken;
                //$deviceToken[] = 'ep4JTpZFT-KsB8ejoM4NoI:APA91bHduV1A7Q3TtGI6PgX0GNEaZ-wPvEOQmctFxCqpf615h1v1JRLfx9lqinxF1n8_wYU0T9koz1g5k2H3Be6DAjwX-8o8qNg094O7tJ4WtW3aj9_jQSyhq7w3IAxa1-z89-d_kWXn';
            }
            $type = 'Admin';
            $message =  array(
                            'type' => 'notification',
                            'title' => $title,
                            'body' => $body,
                            'username' => $username,
                            'user_id' => 1,
                            'notification_userid' => $user_id,
                            'type1' => 'admin',
                            'time' => $this->getdate(time()),
                            'sound'=> 'default',
                            'content-available'=> true,
                            'icon' => 'chat1'
                        );
            commonHelper::firebase($deviceToken,$message);
            commonHelper::saveNotification($follower_id,$user_id,$username,$title,$body,$type,0);
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
}
