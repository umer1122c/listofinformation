<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Review;
use App\Models\UserLoginData;
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

class ProductControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    
    public function index(Request $request){
        $category = Category::orderBy('updated_at','DESC')->first();
        if($category){
            $data['category'] = $category;
            $data['title'] = $category->mata_title;
            $data['description'] = $category->mata_description;
        }else{
            $data['category'] = '';
            $data['title'] = 'All Products';
            $data['description'] = 'All Products';
        }
        $data['class'] = 'products';
        $data['table'] = 'Product Table';
        $data['top_category'] = '';
        $data['top_sub_category'] = '';
        $data['attributes'] = [];
        $data['foodItems'] = [];
        $data['prodImageUrl'] = URL::to('').'/products/';
        $status_active = 1; /* Status 1 for active clients*/
        $parent_id = 0; /* 0 for parent category*/
        $data['categories'] = Category::select('id','title','slug')->where('parent_id',$parent_id)->where('status',0)->get();
        $data['subCategoriesFooter'] = [];
        $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                //->where('products.status',$status_active)
                                ->orderBy('products.updated_at','DESC')
                                ->paginate(12);
        $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'products.weight','categories.title as categories_title','products.status')
                                //->where('products.status',$status_active)
                                ->orderBy('products.updated_at','DESC')
                                ->count();
        if ($request->ajax()) {
            $filterPrice = $request->filterPrice;
            $category = $request->category;
            $subCategory = $request->subCategory;
//            if($category != null) {
//                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
//                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'categories.title as categories_title','products.product_unit','products.bulk_price')
//                                        ->whereIn('products.cat_id',$request->category)
//                                        ->whereIn('products.sub_cat_id',$request->subCategory)
//                                        ->where('products.status',$status_active)
//                                        ->orderBy('products.product_price',$request->filterPrice)
//                                        ->paginate(12);
//                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
//                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'categories.title as categories_title','products.product_unit','products.bulk_price')
//                                        ->whereIn('products.cat_id',$request->category)
//                                        ->whereIn('products.sub_cat_id',$request->subCategory)
//                                        ->where('products.status',$status_active)
//                                        ->orderBy('products.product_price',$request->filterPrice)
//                                        ->count();
//                return view('front/products/productLoad',$data)->render(); 
//            }
//            if($category != null && $subCategory != null) {
//                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
//                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'categories.title as categories_title','products.product_unit','products.bulk_price')
//                                        ->whereIn('products.cat_id',$request->category)
//                                        ->whereIn('products.sub_cat_id',$request->subCategory)
//                                        ->where('products.status',$status_active)
//                                        ->orderBy('products.product_price',$request->filterPrice)
//                                        ->paginate(12);
//                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
//                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'categories.title as categories_title','products.product_unit','products.bulk_price')
//                                        ->whereIn('products.cat_id',$request->category)
//                                        ->whereIn('products.sub_cat_id',$request->subCategory)
//                                        ->where('products.status',$status_active)
//                                        ->orderBy('products.product_price',$request->filterPrice)
//                                        ->count();
//                return view('front/products/productLoad',$data)->render(); 
//            }
            if($category != null) {
                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('categories.id',$request->category)
                                        //->where('products.status',1)
                                        ->orderBy('products.updated_at','DESC')
                                        ->paginate(12);
                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('categories.id',$request->category)
                                        //->where('products.status',1)
                                        ->orderBy('products.updated_at','DESC')
                                        ->count();
                return view('front/products/productLoad',$data)->render();
            }
//            if($subCategory != null) {   
//                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
//                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.product_unit','products.bulk_price')
//                                        ->where('products.status',1)
//                                        ->whereIn('products.sub_cat_id',$request->subCategory)
//                                        ->orderBy('products.product_price', $request->filterPrice)
//                                        ->paginate(12);
//                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
//                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.product_unit','products.bulk_price')
//                                        ->where('products.status',1)
//                                        ->whereIn('products.sub_cat_id',$request->subCategory)
//                                        ->orderBy('products.product_price', $request->filterPrice)
//                                        ->count();
//                return view('front/products/productLoad',$data)->render();
//            }
//            if($filterPrice != null) {   
//                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
//                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.product_unit','products.bulk_price')
//                                        ->where('products.status',1)
//                                        ->orderBy('products.product_price', $request->filterPrice)
//                                        ->paginate(12);
//                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
//                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'products.weight','categories.title as categories_title','products.product_unit','products.bulk_price')
//                                        ->where('products.status',1)
//                                        ->orderBy('products.product_price', $request->filterPrice)
//                                        ->count();
//                return view('front/products/productLoad',$data)->render();
//            }
            $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                    ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'products.weight','categories.title as categories_title','products.status')
                                    //->where('products.status',1)
                                    ->orderBy('products.updated_at','DESC')
                                    ->paginate(12);
            $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                    ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                    //->where('products.status',1)
                                    ->orderBy('products.updated_at','DESC')
                                    ->count();
            return view('front/products/productLoad',$data)->render();
        }
        $data['new_arivals'] = DB::table('products')->select('products.*', 'categories.title as categories_title')->join('categories' , 'products.cat_id','=','categories.id')->where('products.is_recommended',1)->where('categories.status' , 0)->offset(0)->limit(10)->get();
        return view('front/products/index' , $data);
    }
    
    public function categoryProducts(Request $request, $slug){
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
        $data['class'] = 'products';
        $data['table'] = 'Product Table';
        $data['attributes'] = [];
        $data['foodItems'] = [];
        $data['prodImageUrl'] = URL::to('').'/products/';
        $data['category_name'] = $slug;
        $status_active = 1; /* Status 1 for active clients*/
        $parent_id = 0; /* 0 for parent category*/
        $catIds = Category::select('id')->where('slug',$slug)->where('status',0)->first();
        if($catIds){
            $cat_id_sub = $catIds->id;
        }else{
            $cat_id_sub = '';
        }
        $data['top_category'] = $cat_id_sub;
        $data['top_sub_category'] = '';
        $data['categories'] = Category::select('id','title','slug')->where('parent_id',$parent_id)->where('status',0)->get();
        $data['subCategoriesFooter'] = Category::select('id','title','slug')->where('parent_id',$cat_id_sub)->where('status',0)->get();
        $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                ->where('products.cat_id',$category->id)
                                //->where('products.status',1)
                                ->orderBy('products.updated_at','DESC')
                                ->paginate(12);
        $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                ->where('products.cat_id',$category->id)
                                //->where('products.status',1)
                                ->count();
        $data['new_arivals'] = DB::table('products')->select('products.*', 'categories.title as categories_title')->join('categories' , 'products.cat_id','=','categories.id')->where('products.is_recommended',1)->where('categories.status' , 0)->offset(0)->limit(10)->get();
        if ($request->ajax()) {
            //$filterPrice = $request->filterPrice;
            $category = $request->category;
            $subCategory = $request->subCategory;
            if($category != null && $subCategory != null) {
                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('products.cat_id',$request->category)
                                        ->whereIn('products.sub_cat_id',$request->subCategory)
                                        //->where('products.status',$status_active)
                                        ->orderBy('products.updated_at','DESC')
                                        ->paginate(12);
                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'products.weight','categories.title as categories_title','products.status')
                                        ->whereIn('products.cat_id',$request->category)
                                        ->whereIn('products.sub_cat_id',$request->subCategory)
                                        //->where('products.status',$status_active)
                                        ->orderBy('products.updated_at','DESC')
                                        ->count();
                return view('front/products/productLoad',$data)->render(); 
            }
            if($category != null && $subCategory != null) {
                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('products.cat_id',$request->category)
                                        ->whereIn('products.sub_cat_id',$request->subCategory)
                                        //->where('products.status',$status_active)
                                        ->orderBy('products.updated_at','DESC')
                                        ->paginate(12);
                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('products.cat_id',$request->category)
                                        ->whereIn('products.sub_cat_id',$request->subCategory)
                                        //->where('products.status',$status_active)
                                        ->orderBy('products.updated_at','DESC')
                                        ->count();
                return view('front/products/productLoad',$data)->render(); 
            }
            if($category != null) {
                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('categories.id',$request->category)
                                        //->where('products.status',1)
                                        ->orderBy('products.updated_at','DESC')
                                        ->paginate(12);
                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('categories.id',$request->category)
                                        //->where('products.status',1)
                                        ->orderBy('products.updated_at','DESC')
                                        ->count();
                return view('front/products/productLoad',$data)->render();
            }
            $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                    ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                    //->where('products.status',1)
                                    ->orderBy('products.updated_at','DESC')
                                    ->paginate(12);
            $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                    ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                    //->where('products.status',1)
                                    ->orderBy('products.updated_at','DESC')
                                    ->count();
            return view('front/products/productLoad',$data);
        }else{
            return view('front/products/index',$data);
        }
    }
    
    public function subCategoryProducts(Request $request, $catSlug,$subCatSlug){
        $category = Category::where('slug',$catSlug)->first();
        $subCategory = Category::where('slug',$subCatSlug)->first();
        //dd($subCategory);
        if($category){
            $data['category'] = $category;
            $data['title'] = $category->mata_title;
            $data['description'] = $category->mata_description;
        }else{
            $data['category'] = '';
            $data['title'] = 'All Products';
            $data['description'] = 'All Products';
        }
        $data['class'] = 'products';
        $data['table'] = 'Product Table';
        $data['attributes'] = [];
        $data['foodItems'] = [];
        $data['prodImageUrl'] = URL::to('').'/products/';
        $data['category_name'] = $catSlug;
        $data['sub_category_name'] = $subCatSlug;
        $status_active = 1; /* Status 1 for active clients*/
        $parent_id = 0; /* 0 for parent category*/
        $catIds = Category::select('id')->where('slug',$catSlug)->where('status',0)->first();
        //dd($catIds);
        if($catIds){
            $cat_id_sub = $catIds->id;
        }else{
            $cat_id_sub = '';
        }
        
        $subCatIds = Category::select('id')->where('slug',$subCatSlug)->where('status',0)->first();
        if($subCatIds){
            $sub_cat_id = $subCatIds->id;
        }else{
            $sub_cat_id = '';
        }
        $data['top_category'] = $cat_id_sub;
        $data['top_sub_category'] = $sub_cat_id;
        $data['categories'] = Category::select('id','title','slug')->where('parent_id',$parent_id)->where('status',0)->get();
        $data['subCategoriesFooter'] = Category::select('id','title','slug')->where('parent_id',$cat_id_sub)->where('status',0)->get();
        $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                ->where('products.cat_id',$category->id)
                                ->where('products.sub_cat_id',$subCategory->id)
                                ->orderBy('products.updated_at','DESC')
                                //->where('products.status',1)
                                ->paginate(12);
        $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'products.weight','categories.title as categories_title','products.status')
                                ->where('products.cat_id',$category->id)
                                ->where('products.sub_cat_id',$subCategory->id)
                                //->where('products.status',1)
                                ->count();
        $data['recommended'] = DB::table('products')->select('products.*')->select('products.*')->select('products.*')->join('categories' , 'products.cat_id','=','categories.id')->where('products.is_recommended', 1)->where('categories.status' , 0)->offset(0)->limit(10)->get();
        if ($request->ajax()) {
            $filterPrice = $request->filterPrice;
            $category = $request->category;
            $subCategory = $request->subCategory;
            if($category != null && $subCategory != null && $filterPrice != null) {
                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('products.cat_id',$request->category)
                                        ->whereIn('products.sub_cat_id',$request->subCategory)
                                        //->where('products.status',$status_active)
                                        ->orderBy('products.product_price',$request->filterPrice)
                                        ->paginate(12);
                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('products.cat_id',$request->category)
                                        ->whereIn('products.sub_cat_id',$request->subCategory)
                                        //->where('products.status',$status_active)
                                        ->orderBy('products.product_price',$request->filterPrice)
                                        ->count();
                return view('front/products/productLoad',$data)->render(); 
            }
            if($category != null && $subCategory != null) {
                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('products.cat_id',$request->category)
                                        ->whereIn('products.sub_cat_id',$request->subCategory)
                                        //->where('products.status',$status_active)
                                        ->orderBy('products.product_price',$request->filterPrice)
                                        ->paginate(12);
                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('products.cat_id',$request->category)
                                        ->whereIn('products.sub_cat_id',$request->subCategory)
                                        //->where('products.status',$status_active)
                                        ->orderBy('products.product_price',$request->filterPrice)
                                        ->count();
                return view('front/products/productLoad',$data)->render(); 
            }
            if($category != null) {
                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('categories.id',$request->category)
                                        //->where('products.status',1)
                                        ->orderBy('products.product_price',$request->filterPrice)
                                        ->paginate(12);
                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        ->whereIn('categories.id',$request->category)
                                        //->where('products.status',1)
                                        ->orderBy('products.product_price',$request->filterPrice)
                                        ->count();
                return view('front/products/productLoad',$data)->render();
            }
            if($filterPrice != null) {   
                $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        //->where('products.status',1)
                                        ->orderBy('products.product_price', $request->filterPrice)
                                        ->paginate(12);
                $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                        ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price','products.weight', 'categories.title as categories_title','products.status')
                                        //->where('products.status',1)
                                        ->orderBy('products.product_price', $request->filterPrice)
                                        ->count();
                return view('front/products/productLoad',$data)->render();
            }
            $data['products']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                    ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'products.weight','categories.title as categories_title','products.status')
                                    //->where('products.status',1)
                                    ->orderBy('products.product_price', $request->filterPrice)
                                    ->paginate(12);
            $data['productsCount']= Product::Join('categories', 'products.cat_id', '=', 'categories.id')
                                    ->select('products.id', 'products.product_id', 'products.product_title', 'products.slug', 'products.cat_id', 'products.product_images','products.product_price', 'products.weight','categories.title as categories_title','products.status')
                                    //->where('products.status',1)
                                    ->orderBy('products.product_price', $request->filterPrice)
                                    ->count();
            return view('front/products/productLoad',$data);
        }else{
            return view('front/products/index',$data);
        }
    }
    
    public function detail($slug= '')
    {
        $proSlug = Product::where('slug',$slug)->first();
        $data['title'] = $proSlug->mata_title;
        $data['description'] = $proSlug->mata_description;
        $data['class'] = 'detail';
        $data['prodImageUrl'] = URL::to('').'/products/';
        $product = Product::where('slug' , $slug)->first();
        $data['product'] = $product;
        $category = Category::where('id' , $product->cat_id)->first();
        if($category){
            $data['category'] = $category->title;
        }else{
            $data['category'] = '';
        }
        $data['offset'] = 0;
        $data['limit'] = 2;
        $data['total_reviews'] = Review::where('product_id' , $product->product_id)->count();
        $reviews = Review::where('product_id' , $product->product_id)->orderby('created_at' , 'DESC')->offset(0)->limit(2)->get();
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
        $store_rating = Review::where('product_id' , $product->product_id)->where('status' , 0)->avg('rating');
        if($store_rating == null){
            $data['overall_rating'] = 0.00;
        }else{
            $data['overall_rating'] = number_format($store_rating,2);
        }
        $data['rating_count1'] = Review::where('product_id' , $product->product_id)->where('rating' ,'>=', 1)->where('rating' ,'<', 2)->where('status' , 0)->count();
        $data['rating_count2'] = Review::where('product_id' , $product->product_id)->where('rating' ,'>=', 2)->where('rating' ,'<', 3)->where('status' , 0)->count();
        $data['rating_count3'] = Review::where('product_id' , $product->product_id)->where('rating' ,'>=', 3)->where('rating' ,'<', 4)->where('status' , 0)->count();
        $data['rating_count4'] = Review::where('product_id' , $product->product_id)->where('rating' ,'>=', 4)->where('rating' ,'<', 5)->where('status' , 0)->count();
        $data['rating_count5'] = Review::where('product_id' , $product->product_id)->where('status' , 0)->where('rating' ,'>=', 5)->count();
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
        $data['category_title'] = Category::where('id',$product->cat_id)->first()->title;
        $data['product_images'] = json_decode($product->product_images);
        $data['new_arivals'] = DB::table('products')->select('products.*', 'categories.title as categories_title')->join('categories' , 'products.cat_id','=','categories.id')->where('products.is_recommended',1)->where('categories.status' , 0)->offset(0)->limit(10)->get();
        //dd($data['related_products']);
        return view('front/products/detail' , $data);
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
