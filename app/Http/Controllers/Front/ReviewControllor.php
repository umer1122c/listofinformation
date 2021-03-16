<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Mail;
use URL;

class ReviewControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function index(Request $request , $product_id){
        $data['title'] = 'View all Reviews';
        $data['class'] = 'reviews';
        $data['table'] = 'View all Reviews';
        $user_id = session('user_id');
        $product = Product::where('product_id' , $product_id)->first();
        $data['product'] = $product;
        $data['reviews'] = Review::join('users' , 'users.user_id' ,'=', 'reviews.user_id')->select('reviews.*','users.first_name','users.last_name','users.user_image','users.social_image')->where('product_id' , $product_id)->orderby('reviews.created_at' , 'DESC')->paginate(10);
        if ($request->ajax()) {
            return view('front/reviews/reviewsLoad',$data)->render();
        }
        return view('front/reviews/index' , $data);
    }
    
    public function writeReview(Request $request , $id = '')
    {
        $data['title'] = 'Write A Review';
        $data['class'] = 'reviews';
        $data['product_id'] = $id;
        $data['prodImageUrl'] = URL::to('').'/products/';
        $user_id = Session::get('user_id');
        if ($request->isMethod('get')) {
            $product = Product::where('product_id' , $id)->first();
            $data['product'] = $product;
            return view('front/reviews/write_review' , $data);
        }else{
            $reviews = new Review();
            $reviews->product_id = $id;
            $reviews->user_id = $user_id;
            $reviews->rating = $request->rate_val;
            $reviews->title = $request->title;
            $reviews->review = $request->review;
            $reviews->save();
            return response()->json(['status'=>"success",'message' => 'Review has been submited successfully!!'],200);
        }
    }
    
    public function getReviews($product_id = '' , $offset = '')
    {
        $data['offset'] = $offset;
        $data['limit'] = 2 + $offset;
        $data['total_reviews'] = Review::where('product_id' , $product_id)->count();
        $reviews = Review::where('product_id' , $product_id)->orderby('created_at' , 'DESC')->offset($offset)->limit(2)->get();
        $reviewArray = [];
        if(count($reviews) > 0){
            foreach($reviews as $row){
                $row->created_at = $this->getdate($row->created_at);
                $users = User::select('first_name','last_name')->where('user_id' , $row->user_id)->first();
                if($users){
                    $row->first_name = $users->first_name;
                    $row->last_name = $users->last_name;
                }else{
                    $row->first_name = '';
                    $row->last_name = '';
                }
                $reviewArray[] = $row;
            }
        }
        $data['reviews'] = $reviewArray;
        return view('front/shop/ajaxReviews' , $data);
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
            return  $string .= "$year Year ago";
        }
        if($momth > 0){
            return  $string .= "$momth Month ago";
        }
        if($weeks > 0){
            return  $string .= "$weeks weeks ago";
        }
//        if($hours > 24 && $hours < 48){
//                return $string .= "Yesterday";
//        }
//        if($hours > 1 && $hours < 24){
//                return $string .= "Today";
//        }
        if($days > 0){
                return  $string .= "$days days ago";
        }
        if($hours > 0){
                return $string .= "$hours hours ago";
        }
        if($minutes > 0){
                return $string .= "$minutes minutes ago";
        }
        if ($seconds < 59){
                return $string .= "Just now";
        }
    }
}
