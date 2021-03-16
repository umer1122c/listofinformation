<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Setting;
use App\Models\UserLoginData;
use App\Models\User;
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
use App\Classes\CommonLibrary;
use commonHelper;
use Cart;

class CheckoutControllor extends Controller
{
	
    public function __construct() {
        /** Paystack api context **/
        $settings = Setting::where('settings_id' , 1)->first();
        //dd($account);
        if($settings){
            if($settings->payment_mod == 1){
                config(['paystack.publicKey' => $settings->PAYSTACK_PUBLIC_KEY_LIVE]);
                config(['paystack.secretKey' => $settings->PAYSTACK_SECRET_KEY_LIVE]);
            }else{
                config(['paystack.publicKey' => $settings->PAYSTACK_PUBLIC_KEY_LOCAL]);
                config(['paystack.secretKey' => $settings->PAYSTACK_SECRET_KEY_LOCAL]);
            }
        }
    }
    
    
    public function index(Request $request){
        $data['title'] = 'Cart Checkout';
        $data['class'] = 'checkout';
        $data['table'] = 'Product Table';
        $data['courseImageUrl'] = URL::to('').'/courses/';
        $user_id = session('user_id');
        $qty = 0;
        $cartItems = Cart::content()->groupBy('id');
        //dd($cartItems);
        $itemArray = [];
        $price = 0;
        if(count($cartItems) > 0){
            foreach($cartItems as $row) {
                $row_item = (object) array();
                $items = json_decode($row);
                if(count($items) > 0){
                    $qty = 0;
                    $a=array();
                    foreach ($items as $item_row){
                        //return $item_row->rowId;
                        $rowId = array_push($a, $item_row->rowId);
                        $qty = $qty + $item_row->qty;
                    }
                    $row_item->rowId = implode(',',$a);
                    $row_item->qty = $qty;
                    $price= $price + $items[0]->price * $qty;
                }
                
                $course = Course::where('course_id' , $items[0]->id)->first();
                if($course){
                    $row_item->course_image = $course->course_image;
                }else{
                    $row_item->course_image = [];
                }
                $row_item->course_title = $items[0]->name;
                $row_item->course_id = $items[0]->id;
                $row_item->price = $items[0]->price;
                $row_item->cart_id = $items[0]->rowId;
                $itemArray[] = $row_item;
            }

        }
        //dd($itemArray);
        $data['total'] = $price;
        $data['items'] = $itemArray;
        return view('front/checkout/index' , $data);
    }
}
