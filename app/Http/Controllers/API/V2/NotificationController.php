<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;

use App\Models\Area;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use URL;

class NotificationController extends Controller
{
    public function GetNotifications(Request $request){
        try{
            $user = $request->user();
            $noificationArray = [];
            $notification = Notification::Join('users', 'users.user_id', '=', 'notifications.reciever_user_id')
                            ->select('notifications.notification_id','notifications.sender_user_id as user_id','notifications.title','notifications.body','notifications.type','notifications.pally_id','notifications.created_at as time')
                            ->where('notifications.reciever_user_id' , $user->user_id)
                            ->orderby('notifications.created_at' , 'DESC')
                            ->get();
            if(count($notification) > 0){
                foreach($notification as $row){
                    $row->time = $this->getdate(strtotime($row->time));
                    $users_res = User::select('user_image','social_image')->where('user_id' ,  $row->user_id)->first();
                    if($users_res){
                        if($users_res->user_image == ''){
                            if($users_res->social_image != ''){
                                $row->user_image = $users_res->social_image;
                            }else{
                                $row->user_image = URL::to('/front/dummy_round.png');
                            }
                        }else{
                            $row->user_image = URL::to('/') . '/users/'.$users_res->user_image;
                        }
                    }else{
                        $row->user_image = "";
                    }
                    $row->user_id = $row->user_id;
                    $row->r_name = "";
                    $row->s_name = "";
                    
                    $noificationArray[] = $row;
                }
                return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"notifications"=>$noificationArray];
            }
            return ["status" => true,"message"=>"No data found","user_access"=>request()->user()->user_access,"notifications"=>$noificationArray];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function ReadNotification(Request $request){
        try{
            $user = $request->user();
            $notification = Notification::where('reciever_user_id' , $user->user_id)->update(['status' => 1]);
            return ["status" => true,"message"=>"Notification status updated successfully.","user_access"=>request()->user()->user_access];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
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
