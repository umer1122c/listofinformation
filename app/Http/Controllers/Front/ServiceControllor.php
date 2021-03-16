<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Bookmark;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Review;
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

class ServiceControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    
    public function index(Request $request){
        
        $data['serviceImageUrl'] = URL::to('').'/service_categories/';
        $data['prodImageUrl'] = URL::to('').'/products/';
        $data['title'] = 'Services';
        $data['class'] = 'services';
        $user_id = Session::get('user_id');
        $data['services'] = ServiceCategory::where('status' , 0)->orderby('updated_at' , 'DESC')->paginate(12);
        $data['new_arivals'] = DB::table('products')->select('products.*', 'categories.title as categories_title')->join('categories' , 'products.cat_id','=','categories.id')->where('products.is_recommended',1)->where('products.status',1)->where('categories.status' , 0)->offset(0)->limit(10)->get();
        //dd($data['favorites']);
        if ($request->ajax()) {
            return view('front/services/productLoad',$data)->render();
        }
        return view('front/services/index' , $data);
    }
    
    public function getServices(Request $request)
    {
        $data['prodImageUrl'] = URL::to('').'/products/';
        $data['services_cat'] = ServiceCategory::where('service_category_id',$request->service_id)->first();
        $data['services'] = Service::where('service_category_id',$request->service_id)->where('status' , 1)->get();
        return view('front/services/ServicePopupLoad' , $data);
    }
    
    public function detail($slug= '')
    {
        $proSlug = ServiceCategory::where('slug',$slug)->first();
        $data['title'] = $proSlug->mata_title;
        $data['description'] = $proSlug->mata_description;
        $data['class'] = 'detail';
        $data['prodImageUrl'] = URL::to('').'/products/';
        $data['serviceImageUrl'] = URL::to('').'/service_categories/';
        $service = ServiceCategory::where('slug' , $slug)->first();
        $data['service'] = $service;
        $data['offset'] = 0;
        $data['limit'] = 2;
        $data['total_reviews'] = Review::where('product_id' , $service->service_category_id)->count();
        $reviews = Review::where('product_id' , $service->service_category_id)->orderby('created_at' , 'DESC')->offset(0)->limit(2)->get();
        //dd($reviews);
        $reviewArray = [];
        if(count($reviews) > 0){
            foreach($reviews as $row){
                //$row->created_at = $this->getdate($row->created_at);
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
        $store_rating = Review::where('product_id' , $service->service_category_id)->where('status' , 0)->avg('rating');
        if($store_rating == null){
            $data['overall_rating'] = 0.00;
        }else{
            $data['overall_rating'] = number_format($store_rating,2);
        }
        $data['rating_count1'] = Review::where('product_id' , $service->service_category_id)->where('rating' ,'>=', 1)->where('rating' ,'<', 2)->where('status' , 0)->count();
        $data['rating_count2'] = Review::where('product_id' , $service->service_category_id)->where('rating' ,'>=', 2)->where('rating' ,'<', 3)->where('status' , 0)->count();
        $data['rating_count3'] = Review::where('product_id' , $service->service_category_id)->where('rating' ,'>=', 3)->where('rating' ,'<', 4)->where('status' , 0)->count();
        $data['rating_count4'] = Review::where('product_id' , $service->service_category_id)->where('rating' ,'>=', 4)->where('rating' ,'<', 5)->where('status' , 0)->count();
        $data['rating_count5'] = Review::where('product_id' , $service->service_category_id)->where('status' , 0)->where('rating' ,'>=', 5)->count();
        //dd($reviewArray);
        if($data['rating_count1'] > 0){
            $data['avg_rating_count1'] = number_format($data['rating_count1'] / $data['total_reviews'] * 100 , 2);
        }else{
            $data['avg_rating_count1'] = 0;
        }
        
        if($data['rating_count2'] > 0){
            $data['avg_rating_count2'] = number_format($data['rating_count2'] / $data['total_reviews'] * 100, 2);
        }else{
            $data['avg_rating_count2'] = 0;
        }
        
        if($data['rating_count3'] > 0){
            $data['avg_rating_count3'] = number_format($data['rating_count3'] / $data['total_reviews'] * 100 , 2);
        }else{
            $data['avg_rating_count3'] = 0;
        }
        
        if($data['rating_count4'] > 0){
            $data['avg_rating_count4'] = number_format($data['rating_count4'] / $data['total_reviews'] * 100, 2);
        }else{
            $data['avg_rating_count4'] = 0;
        }
        
        if($data['rating_count5'] > 0){
            $data['avg_rating_count5'] = number_format($data['rating_count5'] / $data['total_reviews'] * 100, 2);
        }else{
            $data['avg_rating_count5'] = 0;
        }
        //return $data['avg_rating_count1'];
        $data['reviews'] = $reviewArray;
        $data['product_images'] = $service->image;
        $data['new_arivals'] = DB::table('products')->select('products.*', 'categories.title as categories_title')->join('categories' , 'products.cat_id','=','categories.id')->where('products.is_recommended',1)->where('products.status',1)->where('categories.status' , 0)->offset(0)->limit(10)->get();
        //dd($data['related_products']);
        return view('front/services/detail' , $data);
    }
}