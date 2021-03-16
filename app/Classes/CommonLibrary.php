<?php 
namespace App\Classes;
use App\Models\LevelVenues;
use Session;
use Redirect;
use App\Models\User;
use App\Models\Setting;
// use Illuminate\Http\Request;
use Request;
use Cookie;
use URL;
use DB;
use App\CommonModel;
use File;

class CommonLibrary {
    
    
    
    public static function textLimit($text,$limit){
        if(strlen($text) > $limit){
            return $text  = substr($text,0,$limit).'...';
        }else{
          return  $text;  
        }   
    }
	
    
    
    
    public static function getSettings(){
        return $settings = Setting::first();
    }
    
    public static function saveNotification($sender_user_id,$reciever_user_id,$username,$title,$body,$type,$pally_id){
        $notification = new Notification();
        $notification->sender_user_id = $sender_user_id;
        $notification->reciever_user_id = $reciever_user_id;
        $notification->pally_id = $pally_id;
        $notification->title = $title;
        $notification->body = $body;
        $notification->type = $type;
        $notification->created_at = date('Y-m-d H:i:s');
        $notification->updated_at = date('Y-m-d H:i:s');
        $notification->save();
    }
    
    public static function firebase($device_token,$message) {
	//echo '<pre>';print_r($message);exit;
        // Message should contain key and value. It should be an array like =====  message=>'Hi test'
        $url = 'https://fcm.googleapis.com/fcm/send';
		
        $fields = array(
            'registration_ids' => $device_token,
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

?>