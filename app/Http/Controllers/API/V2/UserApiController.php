<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use App\User;
use App\Models\Follower;
use App\Models\BankInfo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Models\UserLoginData;
use App\Classes\CommonLibrary;
use commonHelper;

class UserApiController extends Controller
{
    public function GetUsers(Request $request){
        try{
            $user_id = $request->user_id;
            $validator = Validator::make(request()->all(), [
                'offset' => 'required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $offset = request()->offset + 20;
            $usersArr = [];
            $users = User::select('user_id','user_type','first_name','last_name', 'email','user_name','user_image','social_image','referral_code')->where('user_id' , '!=', $user_id)->orderby('first_name' , 'ASC')->skip(request()->offset)->take(20)->get();
            if(count($users) > 0){
                foreach($users as $row){
                    if($row->user_image == ''){
                        $row->user_image = !empty($row->social_image) ? $row->social_image : url('/front/dummy_round.png');
                    }else{
                        $row->user_image = url('/') . '/users/'.$row->user_image;
                    }
                    $userFollowing = Follower::select('is_follow')->where('from_user_id' , $user_id)->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                    if($userFollowing){
                        $row->is_follow = 1;
                    }else{
                        $row->is_follow = 0;
                    }
                    $usersArr[] = $row;
                }
                return ['status'=>true,"message"=>'User data found.',"user_access"=>1,"offset"=>$offset,'users'=>$usersArr];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>1,"offset"=>$offset,'users'=>$usersArr];
            }

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetFollowers(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $fallowerArr = [];
            $fallower = Follower::Join('users' , 'users.user_id', '=', 'followers.from_user_id')
                            ->select('from_user_id as user_id','users.user_type','users.first_name','users.last_name','users.email','users.user_name','users.user_image','users.social_image')
                            ->where('followers.to_user_id' , request()->user_id)
                            ->where('followers.is_follow' , 1)
                            ->get();
            if(count($fallower) > 0){
                foreach($fallower as $row){
                    if($row->user_image == ''){
                        $row->user_image = !empty($row->social_image) ? $row->social_image : url('/front/dummy_round.png');
                    }else{
                        $row->user_image = url('/') . '/users/'.$row->user_image;
                    }
                    $userFollowing = Follower::select('is_follow')->where('from_user_id' , $user->user_id)->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                    if($userFollowing){
                        $row->is_follow = 1;
                    }else{
                        $row->is_follow = 0;
                    }
                    $fallowerArr[] = $row;
                }
                return ['status'=>true,"message"=>'User data found.',"user_access"=>request()->user()->user_access,'users'=>$fallowerArr];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>request()->user()->user_access,'users'=>$fallowerArr];
            }
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetFollowing(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $fallowerArr = [];
            $fallower = Follower::Join('users' , 'users.user_id', '=', 'followers.to_user_id')
                            ->select('to_user_id as user_id','users.user_type','users.first_name','users.last_name','users.email','users.user_name','users.user_image','users.social_image')
                            ->where('from_user_id' , request()->user_id)
                            ->where('is_follow' , 1)
                            ->get();
            if(count($fallower) > 0){
                foreach($fallower as $row){
                    if($row->user_image == ''){
                        $row->user_image = !empty($row->social_image) ? $row->social_image : url('/front/dummy_round.png');
                    }else{
                        $row->user_image = url('/') . '/users/'.$row->user_image;
                    }
                    $userFollowing = Follower::select('is_follow')->where('from_user_id' , $user->user_id)->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                    if($userFollowing){
                        $row->is_follow = 1;
                    }else{
                        $row->is_follow = 0;
                    }
                    $fallowerArr[] = $row;
                }
                return ['status'=>true,"message"=>'User data found.',"user_access"=>request()->user()->user_access,'users'=>$fallowerArr];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>request()->user()->user_access,'users'=>$fallowerArr];
            }
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
        
    public function GetUserFollowers(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'offset' => 'required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $offset = request()->offset + 20;
            $fallowerArr = [];
            $fallower = Follower::Join('users' , 'users.user_id', '=', 'followers.from_user_id')
                            ->select('from_user_id as user_id','users.user_type','users.first_name','users.last_name','users.email','users.user_name','users.user_image','users.social_image')
                            ->where('followers.to_user_id' , request()->user_id)
                            ->where('followers.is_follow' , 1)
                            ->skip(request()->offset)->take(20)
                            ->get();
            if(count($fallower) > 0){
                foreach($fallower as $row){
                    if($row->user_image == ''){
                        $row->user_image = !empty($row->social_image) ? $row->social_image : url('/front/dummy_round.png');
                    }else{
                        $row->user_image = url('/') . '/users/'.$row->user_image;
                    }
                    $userFollowing = Follower::select('is_follow')->where('from_user_id' , $user->user_id)->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                    if($userFollowing){
                        $row->is_follow = 1;
                    }else{
                        $row->is_follow = 0;
                    }
                    $fallowerArr[] = $row;
                }
                return ['status'=>true,"message"=>'User data found.',"user_access"=>request()->user()->user_access,"offset"=>$offset,'users'=>$fallowerArr];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>request()->user()->user_access,"offset"=>$offset,'users'=>$fallowerArr];
            }
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetUserFollowing(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'offset' => 'required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $offset = request()->offset + 20;
            $fallowerArr = [];
            $fallower = Follower::Join('users' , 'users.user_id', '=', 'followers.to_user_id')
                            ->select('to_user_id as user_id','users.user_type','users.first_name','users.last_name','users.email','users.user_name','users.user_image','users.social_image')
                            ->where('from_user_id' , request()->user_id)
                            ->where('is_follow' , 1)
                            ->skip(request()->offset)->take(20)
                            ->get();
            if(count($fallower) > 0){
                foreach($fallower as $row){
                    if($row->user_image == ''){
                        $row->user_image = !empty($row->social_image) ? $row->social_image : url('/front/dummy_round.png');
                    }else{
                        $row->user_image = url('/') . '/users/'.$row->user_image;
                    }
                    $userFollowing = Follower::select('is_follow')->where('from_user_id' , $user->user_id)->where('to_user_id' , $row->user_id)->where('is_follow' , 1)->first();
                    if($userFollowing){
                        $row->is_follow = 1;
                    }else{
                        $row->is_follow = 0;
                    }
                    $fallowerArr[] = $row;
                }
                return ['status'=>true,"message"=>'User data found.',"user_access"=>request()->user()->user_access,"offset"=>$offset,'users'=>$fallowerArr];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>request()->user()->user_access,"offset"=>$offset,'users'=>$fallowerArr];
            }
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function FollowUser(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'follower_id' => 'required'
            ]);
            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $fallower = Follower::where('from_user_id' , $user->user_id)->where('to_user_id' , request()->follower_id)->first();
            //dd($fallower);
            if($fallower){
                //return $fallower->is_follow;
                if($fallower->is_follow == 1){
                    Follower::where('from_user_id' , $user->user_id)->where('to_user_id' , request()->follower_id)->update(['is_follow' => 0]);
                    return ['status'=>true,"message"=>'User unfollow this user successfully!',"user_access"=>request()->user()->user_access,'is_follow'=>0];
                }else{
                    Follower::where('from_user_id' , $user->user_id)->where('to_user_id' , request()->follower_id)->update(['is_follow' => 1]);
                    $fallowerOther = Follower::where('from_user_id' , request()->follower_id)->where('to_user_id' , $user->user_id)->where('is_follow' , 1)->first();
                    if($fallowerOther){
                        $body = ' just followed you, you are now able to chat or do a close pally together';
                    }else{
                        $body = ' just followed you, you can follow back to be able to chat or do a close pally together';
                    }
                    
                    //Send Notification
                    $this->sendNotificationToUser($user->user_id , request()->follower_id , $body);
                    //End Notification
                    return ['status'=>true,"message"=>'User follow the following user successfully!',"user_access"=>request()->user()->user_access,'is_follow'=>1];
                }
            }else{
                $userFollow = new Follower();
                $userFollow->from_user_id = $user->user_id;
                $userFollow->to_user_id = request()->follower_id;
                $userFollow->created_at = date('Y-m-d H:i:s');
                $userFollow->updated_at = date('Y-m-d H:i:s');
                $userFollow->save();
                $fallowerOther = Follower::where('from_user_id' , request()->follower_id)->where('to_user_id' , $user->user_id)->where('is_follow' , 1)->first();
                if($fallowerOther){
                    $body = ' just followed you, you are now able to chat or do a close pally together';
                }else{
                    $body = ' just followed you, you can follow back to be able to chat or do a close pally together';
                }
                //Send Notification
                $this->sendNotificationToUser($user->user_id , request()->follower_id , $body);
                //End Notification
                return ['status'=>true,"message"=>'User follow the following user successfully!',"user_access"=>request()->user()->user_access,'is_follow'=>1];
            }
            
        }catch(\Exception $e){ 
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetBankInfo(Request $request){
        try{
            $user = $request->user();
            $bankinfo = BankInfo::select('account_name','account_number','bank_name','extra_info')->first();
            if($bankinfo){
                return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"bank_detail"=>$bankinfo];
            }else{
                $bankinfo = (object) array();
                $bankinfo->account_name = '';
                $bankinfo->account_number = '';
                $bankinfo->bank_name = '';
                $bankinfo->extra_info = '';
                return ["status" => true,"message"=>"No data found","user_access"=>request()->user()->user_access,"bank_detail"=>$bankinfo];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function sendNotificationToUser($user_id , $follower_id , $message){
        
        $username = User::where('user_id' , $user_id)->first()->first_name;
        $users = UserLoginData::select('deviceToken','userId')->where('userId' , $follower_id)->where('tokenStatus',0)->get();
        if(count($users) > 0){
            $deviceToken = [];
            foreach($users as $key => $val ){
                $deviceToken[] = $val->deviceToken;
                //$deviceToken[] = 'ep4JTpZFT-KsB8ejoM4NoI:APA91bHduV1A7Q3TtGI6PgX0GNEaZ-wPvEOQmctFxCqpf615h1v1JRLfx9lqinxF1n8_wYU0T9koz1g5k2H3Be6DAjwX-8o8qNg094O7tJ4WtW3aj9_jQSyhq7w3IAxa1-z89-d_kWXn';
            }
            $title = 'Pally follow';
            $body = $username.$message;
            $type = 'follow';
            $message =  array(
                            'type' => 'notification',
                            'title' => $title,
                            'body' => $body,
                            'username' => $username,
                            'user_id' => $user_id,
                            'notification_userid' => $follower_id,
                            'type1' => 'follow',
                            'sound'=> 'default',
                            'content-available'=> true,
                            'icon' => 'chat1'
                        );
            commonHelper::firebase($deviceToken,$message);
            commonHelper::saveNotification($user_id,$follower_id,$username,$title,$body,$type,0);
        }
    }
}