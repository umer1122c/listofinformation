<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\User;
use App\Models\Bookmark;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use URL;

class ProductController extends Controller
{   
    public function GetProductAttributes(Request $request)
    {
        try{
            $attributeArray = [];
            $attribute = Attribute::select('id as attribute_id','attributeName as attribute_name','attributeCost as attribute_cost')->where('product_id' , request()->product_id)->get();
            if(count($attribute) > 0){
                foreach($attribute as $attr){
                    $attributeArray[] = $attr;
                }
                return ["status" => true,"message"=>"data found","user_access"=>1,"Attributes" => $attributeArray];
            }
            return ["status" => true,"message"=>"No data found","user_access"=>1,"Attributes" => $attributeArray];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function NewArrivalProducts(Request $request)
    {
        try{
            $validator = Validator::make(request()->all(), [
                'offset' => 'required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $offset = request()->offset + 20;
            $prodImageUrl = URL::to('').'/products/';
            $products = DB::table('products')
                            ->select('products.product_id','products.cat_id as category_id','categories.title as category_name','products.product_title as product_name','products.product_price','products.html_description as product_description','products.product_images','products.weight','products.status as in_stock')
                            ->join('categories' , 'products.cat_id','=','categories.id')
                            ->where('products.is_recommended', 1)
                            ->where('categories.status' , 0)
                            ->where('products.status' , 1)
                            ->orderBy('products.updated_at','DESC')
                            ->skip(request()->offset)->take(20)
                            ->get();
            $productArray = [];
            if(count($products) > 0){
                foreach($products as $prod){
                    $prod->total_reviews = Review::where('product_id' , $prod->product_id)->count();
                    $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                    if($ratings == null){
                        $prod->product_rating = 0;
                    }else{
                        $prod->product_rating = $ratings;
                    }
                    
                    $bookmark = Bookmark::where('user_id' , request()->user_id)->where('product_id' , $prod->product_id)->first();
                    if($bookmark){ 
                        $prod->is_fav = 1;
                    }else{
                        $prod->is_fav = 0;
                    }
                    $product_images = json_decode($prod->product_images);
                    $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                    $productArray[] = $prod;
                }
                return ["status" => true,"message"=>"data found","user_access"=>1,"offset"=>$offset,"newarivalproducts"=>$productArray];
            }else{
                return ["status" => true,"message"=>"data found","user_access"=>1,"offset"=>$offset,"newarivalproducts"=>$productArray];
            }
            

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function SearchProducts(Request $request)
    {
        try{
            $validator = Validator::make(request()->all(), [
                //'keyword' => 'required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $prodImageUrl = URL::to('').'/products/';
            $productArray = [];
            if(request()->keyword == ''){
                return ["status" => true,"message"=>"No data found","user_access"=>1,"products"=>$productArray];
            }
            $products = DB::table('products')
                            ->select('products.product_id','products.cat_id as category_id','categories.title as category_name','products.product_title as product_name','products.product_price','products.product_description','products.product_images','products.weight','products.status as in_stock')
                            ->join('categories' , 'products.cat_id','=','categories.id')
                            ->where('categories.status' , 0)
                            //->where('products.status' , 1)
                            ->where('products.product_title', 'like', '%'.request()->keyword.'%')
                            ->orderBy('products.updated_at','DESC')
                            ->get();
            
            if(count($products) > 0){
                foreach($products as $prod){
                    $prod->total_reviews = Review::where('product_id' , $prod->product_id)->count();
                    $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                    if($ratings == null){
                        $prod->product_rating = 0;
                    }else{
                        $prod->product_rating = $ratings;
                    }
                    $bookmark = Bookmark::where('user_id' , request()->user_id)->where('product_id' , $prod->product_id)->first();
                    if($bookmark){ 
                        $prod->is_fav = 1;
                    }else{
                        $prod->is_fav = 0;
                    }
                    $product_images = json_decode($prod->product_images);
                    $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                    $productArray[] = $prod;
                }
                return ["status" => true,"message"=>"data found","user_access"=>1,"products"=>$productArray];
            }else{
                return ["status" => true,"message"=>"data found","user_access"=>1,"products"=>$productArray];
            }
            

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function FilterProducts(Request $request)
    {
        $jsonrequest = json_decode(file_get_contents('php://input'), true);
        try{
            ///$validator = Validator::make(request()->all(), [
                //'offset' => 'required',
                //'user_id' => 'required',
            //]);

            //if ($validator->fails()) {
                //return ['status' => false, 'message' => implode(' ', $validator->errors()->all())];
            //}//..... end if() .....//
            $offset = $jsonrequest['offset'] + 20;
            $prodImageUrl = URL::to('').'/products/';
            $paramsArr = [];
            
            $cat_idArray = $jsonrequest['cat_id'];
            //dd($cat_idArray);
            //$sub_cat_idArray = $jsonrequest['sub_cat_id'];
            if(count($cat_idArray) > 0){
                $paramsArr['products.cat_id'] = $cat_idArray;
            }
//            if(count($sub_cat_idArray) > 0){
//                $paramsArr['products.sub_cat_id'] = $sub_cat_idArray;
//            }
            if(count($paramsArr) > 0){
                $products = DB::table('products')
                                ->select('products.product_id','products.cat_id as category_id','categories.title as category_name','products.product_title as product_name','products.product_price','products.html_description as product_description','products.product_images','products.weight','products.status as in_stock')
                                ->join('categories' , 'products.cat_id','=','categories.id')
                                ->where(function($q) use ($paramsArr)
                                {
                                    foreach($paramsArr as $key => $value)
                                    {
                                        if($key == 'products.cat_id'){
                                            $q->whereIn('cat_id', $value);
                                        }
//                                        elseif($key == 'products.sub_cat_id'){
//                                            $q->whereIn('sub_cat_id', $value);
//                                        }
                                    }
                                })
                                ->where('categories.status' , 0)
                                //->where('products.status' , 1)
                                ->orderBy('products.updated_at','DESC')
                                ->skip($jsonrequest['offset'])->take(20)
                                ->get();
            }else{
                $products = DB::table('products')
                                ->select('products.product_id','products.cat_id as category_id','categories.title as category_name','products.product_title as product_name','products.product_price','products.html_description as product_description','products.product_images','products.weight','products.status as in_stock')
                                ->join('categories' , 'products.cat_id','=','categories.id')
                                ->where('categories.status' , 0)
                                //->where('products.status' , 1)
                                ->orderBy('products.updated_at','DESC')
                                ->skip($jsonrequest['offset'])->take(20)
                                ->get();
            }
            $productArray = [];
            if(count($products) > 0){
                foreach($products as $prod){
                    $prod->total_reviews = Review::where('product_id' , $prod->product_id)->count();
                    $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                    if($ratings == null){
                        $prod->product_rating = 0;
                    }else{
                        $prod->product_rating = $ratings;
                    }
                    
                    $bookmark = Bookmark::where('user_id' , request()->user_id)->where('product_id' , $prod->product_id)->first();
                    if($bookmark){ 
                        $prod->is_fav = 1;
                    }else{
                        $prod->is_fav = 0;
                    }
                    $product_images = json_decode($prod->product_images);
                    $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                    $productArray[] = $prod;
                }
                return ["status" => true,"message"=>"data found","user_access"=>1,"offset"=>$offset,"products"=>$productArray];
            }else{
                return ["status" => true,"message"=>"data found","user_access"=>1,"offset"=>$offset,"products"=>$productArray];
            }
            

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function ProductsDetail(Request $request)
    {
        try{
            $validator = Validator::make(request()->all(), [
                'product_id' => 'required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $prodImageUrl = URL::to('').'/products/';
            $product = Product::select('products.product_id','products.cat_id as category_id','categories.title as category_name','products.product_title as product_name','products.product_price','products.html_description as product_description','products.product_images','products.weight','products.status as in_stock')
                            ->join('categories' , 'products.cat_id','=','categories.id')        
                            ->where('products.product_id' , request()->product_id)
                            ->orwhere('products.slug' , request()->product_id)
                            ->first(); 
            $productArray = [];
            $imagesArray = [];
            $reviewArray = [];
            $paramsArr = [];
            $productArray = [];
            $attributeArray = [];
            if($product){
                
                $product->total_reviews = Review::where('product_id' , $product->product_id)->count();
                $ratings = Review::where('product_id' , $product->product_id)->avg('rating');
                if($ratings == null){
                    $product->product_rating = 0;
                }else{
                    $product->product_rating = $ratings;
                }
                
                $bookmark = Bookmark::where('user_id' , request()->user_id)->where('product_id' , $product->product_id)->first();
                if($bookmark){ 
                    $product->is_fav = 1;
                }else{
                    $product->is_fav = 0;
                }
                $product_images = json_decode($product->product_images);
                foreach($product_images as $image){
                    $image->imagePath = $prodImageUrl.$image->imagePath;
                    $imagesArray[] = $image;
                }
                $reviews = Review::select('id','product_id','user_id','rating','review','created_at as review_date')->where('product_id' , $product->product_id)->limit(1)->get();
                if($reviews){
                    foreach($reviews as $row){
                        $row->review_date = date('d-m-Y' , strtotime($row->review_date));
                        $users = User::select('user_id','first_name','last_name','user_name','user_image')->where('user_id' , $row->user_id)->first();
                        if($users){
                            if($users->user_image == ''){
                                if($users->social_image != ''){
                                    $users->user_image = $users->social_image;
                                }else{
                                    $users->user_image = URL::to('/front/dummy_round.png');
                                }
                            }else{
                                $users->user_image = URL::to('/') . '/users/'.$users->user_image;
                            }   
                        }
                        $row->user_info = $users;
                        $reviewArray[] = $row;
                    }
                }
                return ["status" => true,"message"=>"data found","user_access"=>1,"product_detail"=>$product,"product_images"=>$imagesArray,"reviews" => $reviewArray];
            }else{
                return ["status" => true,"message"=>"data found","user_access"=>1,"product_detail"=>'',"product_images"=>$imagesArray,"reviews" => $reviewArray];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetFavouriteProducts(Request $request)
    {
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'offset' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $offset = request()->offset + 20;
            $prodImageUrl = URL::to('').'/products/';
            $products = DB::table('bookmarks')
                            ->select('products.product_id','products.cat_id as category_id','categories.title as category_name','products.product_title as product_name','products.product_price','products.html_description as product_description','products.product_images','products.weight','products.status as in_stock')
                            ->join('products', 'products.product_id', '=', 'bookmarks.product_id')
                            ->join('categories' , 'products.cat_id','=','categories.id')
                            ->where('categories.status' , 0)
                            //->where('products.status' , 1)
                            ->where('bookmarks.user_id' , $user->user_id)
                            ->orderBy('products.created_at','DESC')
                            ->skip(request()->offset)->take(20)
                            ->get();
            $productArray = [];
            if(count($products) > 0){
                foreach($products as $prod){
                    $prod->total_reviews = Review::where('product_id' , $prod->product_id)->count();
                    $ratings = Review::where('product_id' , $prod->product_id)->avg('rating');
                    if($ratings == null){
                        $prod->product_rating = 0;
                    }else{
                        $prod->product_rating = $ratings;
                    }
                    $bookmark = Bookmark::where('user_id' , $user->user_id)->where('product_id' , $prod->product_id)->first();
                    if($bookmark){ 
                        $prod->is_fav = 1;
                    }else{
                        $prod->is_fav = 0;
                    }
                    $product_images = json_decode($prod->product_images);
                    $prod->product_images = $prodImageUrl.$product_images[0]->imagePath;
                    $productArray[] = $prod;
                }
                return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"offset"=>$offset,"products"=>$productArray];
            }else{
                return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"offset"=>$offset,"products"=>$productArray];
            }
            

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function AddFavouriteProducts(Request $request)
    {
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'product_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $bookmark = Bookmark::where('user_id' , $user->user_id)->where('product_id' , request()->product_id)->first();
            if($bookmark){
                Bookmark::where('user_id' , $user->user_id)->where('product_id' , request()->product_id)->delete();
                return ["status" => true,"message"=>"Product has been removed from favourite list successfully!","user_access"=>request()->user()->user_access,"is_fav" => 0];
            }else{
                $bookmarks = new Bookmark();
                $bookmarks->user_id = $user->user_id;
                $bookmarks->product_id = request()->product_id;
                $bookmarks->save();
                return ["status" => true,"message"=>"Product has been added to favourite list successfully!","user_access"=>request()->user()->user_access,"is_fav" => 1];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
}