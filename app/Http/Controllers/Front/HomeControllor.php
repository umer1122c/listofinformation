<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Attribute;
use App\Models\UserLoginData;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use App\Models\Slider;
use App\Models\User;
use App\Models\Award;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use File;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Mail;
use URL;
use Illuminate\Support\Facades\Log;

class HomeControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function home()
    {
        $data['title'] = 'Home';
        $data['description'] = 'Home';
        $data['class'] = 'home';
        $data['prodImageUrl'] = URL::to('').'/products/';
        $data['serviceImageUrl'] = URL::to('').'/service_categories/';
        $data['testimonialsImageUrl'] = URL::to('').'/testimonials/';
        $data['home_image'] = Slider::first();
        
        return view('front/home/index' , $data);
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
