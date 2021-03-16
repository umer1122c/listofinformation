<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
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

class CourceControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    
    public function index(Request $request){
        $data['courseImageUrl'] = URL::to('').'/courses/';
        $data['top_category'] = '';
        $category = Category::orderBy('updated_at','DESC')->first();
        if($category){
            $data['category'] = $category;
            $data['title'] = $category->mata_title;
            $data['description'] = $category->mata_description;
        }else{
            $data['category'] = '';
            $data['title'] = 'All Course';
            $data['description'] = 'All Course';
        }
        $parent_id = 0; /* 0 for parent category*/
        $data['categories'] = Category::select('id','title','slug')->where('parent_id',$parent_id)->where('status',0)->get();
        $data['courses']= Course::orderBy('updated_at','DESC')->paginate(12);
        if ($request->ajax()) {
            $category = $request->category;
            $keyword = $request->keyword;
            if($category != null && $keyword != '') {
                $data['courses']= Course::whereIn('cat_id',$request->category)->where('course_title', 'like', '%'.$keyword.'%')->orderBy('updated_at','DESC')->paginate(12);
            }elseif($category != null){
                $data['courses']= Course::whereIn('cat_id',$request->category)->orderBy('updated_at','DESC')->paginate(12);
            }elseif($keyword != ''){
                $data['courses']= Course::where('course_title', 'like', '%'.$keyword.'%')->orderBy('updated_at','DESC')->paginate(12);
            }else{
                $data['courses']= Course::orderBy('updated_at','DESC')->paginate(12);
            }
            return view('front/courses/courseLoad',$data)->render();
        }
        return view('front/courses/index' , $data);
    }
    
    public function categoryCourse(Request $request, $slug){
        $data['courseImageUrl'] = URL::to('').'/courses/';
        $category = Category::where('slug',$slug)->first();
        if($category){
            $data['category'] = $category;
            $data['title'] = $category->mata_title;
            $data['description'] = $category->mata_description;
        }else{
            $data['category'] = '';
            $data['title'] = 'All Products';
            $data['description'] = 'All Products';
        }
        
        $data['category_name'] = $slug;
        $status_active = 1; /* Status 1 for active clients*/
        $parent_id = 0; /* 0 for parent category*/
        $data['categories'] = Category::select('id','title','slug')->where('parent_id',$parent_id)->where('status',0)->get();
        
        $data['top_category'] = $category->id;
        $data['courses']= Course::where('cat_id',$category->id)->orderBy('updated_at','DESC')->paginate(12);
        if ($request->ajax()) {
            $category = $request->category;
            if($category != null) {
                $data['courses']= Course::whereIn('cat_id',$request->category)->orderBy('updated_at','DESC')->paginate(12);
            }else{
                $data['courses']= Course::orderBy('updated_at','DESC')->paginate(12);
            }
            return view('front/courses/courseLoad',$data)->render();
        }else{
            return view('front/courses/index',$data);
        }
    }
    
    public function detail($slug= '')
    {
        $proSlug = Course::where('slug',$slug)->first();
        $data['title'] = $proSlug->mata_title;
        $data['description'] = $proSlug->mata_description;
        $data['class'] = 'detail';
        $data['courseImageUrl'] = URL::to('').'/courses/';
        $course = Course::where('slug' , $slug)->first();
        $data['course'] = $course;
        $category = Category::where('id' , $course->cat_id)->first();
        if($category){
            $data['category'] = $category->title;
        }else{
            $data['category'] = '';
        }
        return view('front/courses/detail' , $data);
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
    
    public function getProductDetail(Request $request)
    {
        $data['prodImageUrl'] = URL::to('').'/products/';
        $data['product'] = Product::where('product_id',$request->product_id)->first();
        $data['category_title'] = Category::where('id',$data['product']->cat_id)->first()->title;
        return view('front/products/ProductDetailPopupLoad' , $data);
    }
    
    public function getProducts($keyword = ''){
        $data['products'] = Product::select('products.*')->join('categories' , 'products.cat_id','=','categories.id')->where('categories.status' , 0)->where('products.product_title', 'like', '%'.$keyword.'%')->orderby('products.product_title' , 'ASC')->get();
        return view('front/products/ajaxproducts' , $data);
    }
    
    public function getProductsMobile($keyword = ''){
        $data['prodImageUrl'] = URL::to('').'/products/';
        $data['foodItems'] = Product::select('products.*')->join('categories' , 'products.cat_id','=','categories.id')->where('categories.status' , 0)->where('products.product_title', 'like', '%'.$keyword.'%')->orderby('products.product_title' , 'ASC')->get();
        return view('front/products/ajaxproductsMobile' , $data);
    }
    
    public function getProductsSubcategory(Request $request){
        $data['subCategoriesFooter'] = Category::where('status',0)->whereIn('parent_id', $request->category)->orderby('title' , 'ASC')->get();
        return view('front/categories/subCategoriesLoad' , $data);
    }
}
