<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Area;
use App\Models\Follower;
use App\Models\UserAddress;
use App\Models\UserLoginData;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Mail;

class ProfileControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function index(Request $request,$user_id = ''){
        
        $data['title'] = 'User Profile';
        $data['class'] = 'users';
        $data['table'] = 'User Profile';
        $data['current_user_id'] = Session::get('user_id');
        $data['c_user_id'] = $user_id;
        $data['user'] = User::where('user_id' , $user_id)->first();
        //dd($data['user']);
        $data['fallowers'] = DB::table('followers')
                            ->select('followers.follower_id','followers.is_follow','followers.created_at','users.*')
                            ->join('users', 'users.user_id', '=', 'followers.from_user_id')
                            ->where('followers.to_user_id' , $user_id)
                            ->where('followers.is_follow',1)
                            ->paginate(10);
        
        $data['following'] = DB::table('followers')
                            ->select('followers.follower_id','followers.is_follow','followers.created_at','users.*')
                            ->join('users', 'users.user_id', '=', 'followers.to_user_id')
                            ->where('followers.from_user_id' , $user_id)
                            ->where('followers.is_follow',1)
                            //->groupby('users.user_id')
                            ->paginate(10);
        
        $data['address'] = DB::table('user_address')
                            ->select('user_address.*','areas.name')
                            ->join('areas', 'areas.id', '=', 'user_address.area_id')
                            ->where('user_address.user_id' , $user_id)
                            ->orderby('user_address.address_id','DESC')
                            ->first();
        //dd($data['followers']);
        $data['fallowing'] = Follower::where('from_user_id' , $user_id)->where('is_follow' , 1)->count();
        $data['fallower'] = Follower::where('to_user_id' , $user_id)->where('is_follow' , 1)->count();
        if ($request->ajax()) {
            $type = $request->type;
            if($type == 'follower'){
                return view('front/profiles_new/followersLoads',$data)->render(); 
            }else{
                return view('front/profiles_new/followingLoads',$data)->render(); 
            }
        }
        return view('front/profiles_new/index' , $data);
       
    }
    
    public function myProfile(){
        
        $data['title'] = 'User Profile';
        $data['class'] = 'users';
        $data['table'] = 'User Profile';
        $user_id = Session::get('user_id');
        $data['current_user_id'] = Session::get('user_id');
        $data['c_user_id'] = $user_id;
        $data['user'] = User::where('user_id' , $user_id)->first();
        $data['followers'] = DB::table('followers')
                            ->select('followers.follower_id','followers.is_follow','followers.created_at','users.*')
                            ->join('users', 'users.user_id', '=', 'followers.from_user_id')
                            ->where('followers.to_user_id' , $user_id)
                            ->where('followers.is_follow',1)
                            ->paginate(20);
        
        $data['followings'] = DB::table('followers')
                            ->select('followers.follower_id','followers.is_follow','followers.created_at','users.*')
                            ->join('users', 'users.user_id', '=', 'followers.to_user_id')
                            ->where('followers.from_user_id' , $user_id)
                            ->where('followers.is_follow' , 1)
                            //->groupby('users.user_id')
                            ->paginate(20);
        $data['address'] = DB::table('user_address')
                            ->select('user_address.*','areas.name')
                            ->join('areas', 'areas.id', '=', 'user_address.area_id')
                            ->where('user_address.user_id' , $user_id)
                            ->orderby('user_address.address_id','DESC')
                            ->first();
        //dd($data['followers']);
        $data['fallowing'] = Follower::where('from_user_id' , $user_id)->where('is_follow' , 1)->count();
        $data['fallower'] = Follower::where('to_user_id' , $user_id)->where('is_follow' , 1)->count();
        return view('front/profile/my_profile' , $data);
       
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
}
