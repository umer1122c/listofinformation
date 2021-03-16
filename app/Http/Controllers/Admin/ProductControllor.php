<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CartItem;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Image;
use File;
use Illuminate\Support\Str;

class ProductControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function view(){
       $data['title'] = 'Manage Products';
        $data['class'] = 'products';
        $data['table'] = 'Manage Products';
        return view("admin.products.list",$data);
    }



    public function listView(){
        return DataTables::of(Product::query())->addColumn('action', function($data){
            return "<a href='".url('/')."/admin/product/edit/".$data->id."'  class='all_button btn  btn-sm btn-success edit_button'  id='".$data->id."'>Edit</a>
            <a href='javascript:void(0)' class='all_button btn btn-sm btn-danger delete_btn'  id='".$data->id."'>Delete</a>";
        })->make(true);
    }
	
    public function index($parent_id = ''){
        
        $data['title'] = 'Manage Products';
        $data['class'] = 'products';
        $data['table'] = 'Manage Products';
        $data['products'] = Product::get();
        return view('admin/products/index' , $data);
    }
    
    public function indexRecommended(){
        
        $data['title'] = 'New Arrivals Products';
        $data['class'] = 'products';
        $data['table'] = 'New Arrivals Products';
        $data['products'] = Product::where('is_recommended' , 1)->get();
        return view('admin/products/recommended' , $data);
    }

    public function create(Request $request){
        $data['title'] = 'Add Product';
        $data['class'] = 'products';
        $data['table'] = 'Add Product';
        
        if ($request->isMethod('get')) {
            $data['categories'] = Category::where('parent_id' , 0)->orderby('title' , 'ASC')->get();
            return view('admin/products/create' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                'product_images' => 'required'
                            ]);
            if ($validator->fails()) {
                return redirect('admin/product/add')
                                ->withInput()
                                ->withErrors($validator);
            }else{
                $product_images = $request->product_images;
                $imgArray = explode(',' , $product_images);
                $imagesArray = array();
                if(count($imgArray) > 0){
                    foreach($imgArray as $key=>$val)
                    {
                        if($val != ''){
                            $imagesArray[$key]['imagePath']= $val;
                            $imagesArray[$key]['imageId'] = substr(str_shuffle('123456789123456789123456789321654987'),0,12);
                        }
                    }
                }else{
                    Session::flash('success_msg', 'Please upload product images first.'); 
                    return redirect('admin/product/add');
                }
                $is_recommended = isset($request->is_recommended) ? '1' : '0';
                $products = new Product();
                $products->product_id = time();
                $products->cat_id = $request->cat_id;
                $products->sub_cat_id = 0;
                $products->product_title = $request->product_title;
                $products->slug = Str::slug($request->product_title, '-').'-'.substr(str_shuffle('123456789123456789123456789321654987'),0,4);
                $products->product_price = $request->product_price;
                $products->mata_title = $request->mata_title;
                $products->mata_description = $request->mata_description;
                $products->weight = $request->weight;
                $products->is_recommended = $is_recommended;
                $products->status = $request->status;
                $products->product_discount = 2;
                $products->product_description = strip_tags($request->product_description);
                $products->html_description = $request->product_description;
                $products->product_images = json_encode($imagesArray);
                $products->created_at = time();
                $products->updated_at = time();
                $products->save();
                Session::flash('success_msg', 'Product has been added successfully'); 
                return redirect('admin/products');
            }
        }
    }

    public function edit(Request $request , $id = ''){
        $data['title'] = 'Update Product';
        $data['class'] = 'products';
        $data['table'] = 'Update Product';
        $data['product_id'] = $id;
        $product = Product::where('id' , $id)->get();
        if(count($product) == 0){
            Session::flash('success_msg', 'This product does not exist in database.'); 
            return redirect('admin/products');
        }
        if ($request->isMethod('get')) {
            $data['categories'] = Category::where('parent_id' , 0)->orderby('title' , 'ASC')->get();
            $product = Product::where('id' , $id)->first();
            $data['product'] = $product;
            $cat_id = $product->cat_id;
            $data['sub_categories'] = Category::where('parent_id' , $cat_id)->orderby('title' , 'ASC')->get();
            $data['ProductImages'] = json_decode($product->product_images);
            return view('admin/products/edit' , $data);
        }else{
            
            $validator = Validator::make($request->all(), [
                                //'cat_title' => 'required',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/image/edit/'.$id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                $is_recommended = isset($request->is_recommended) ? '1' : '0';
                $cars = Product::where('id', $id)->first();
                if($cars){
                    $ImageUrl = json_decode($cars->product_images,true);
                }
                //dd($ImageUrl);
                $product_image = $request->product_images;
                if($product_image != null){
                    $imgArray = explode(',' , $product_image);
                    $imagesArray = array();
                    if($imgArray){
                        foreach($imgArray as $key=>$val)
                        {
                            if($val != ''){
                                $imagesArray[$key]['imagePath']= $val;
                                $imagesArray[$key]['imageId'] = substr(str_shuffle('123456789123456789123456789321654987'),0,12);
                            }
                        }
                        if($ImageUrl == null){
                            $ImageUrl = $imagesArray;
                        }else{
                            $ImageUrl = array_merge($ImageUrl,$imagesArray);
                            $ImageUrl = array_values($ImageUrl);
                        }
                    }
                }
                
                Product::where('id', $id)
                    ->update([
                        'cat_id' => $request->cat_id,
                        'sub_cat_id' => 0,
                        'product_title' => $request->product_title,
                        //'slug' => Str::slug($request->product_title, '-'),
                        'mata_title' => $request->mata_title,
                        'mata_description' => $request->mata_description,
                        'product_price' => $request->product_price,
                        'weight' => $request->weight,
                        'status' => $request->status,
                        'is_recommended' => $is_recommended,
                        'product_discount' => 0,
                        'product_description' => strip_tags($request->product_description),
                        'html_description' => $request->product_description,
                        'product_images' => json_encode($ImageUrl),
                        'updated_at' => time()
                    ]);
                $product = Product::where('id' , $id)->first();
                CartItem::where('type_id' , $product->product_id)->update(['price' => $request->product_price]);
                Session::flash('success_msg', 'Product has been updated successfully.'); 
                return redirect('admin/products');
            }
        }
    }
    public function delete(){
        Product::where(["id"=>request()->id])->delete();
        return ["status" => true,"message" =>"Record deleted successfully"];
    }
    

//    public function delete($id = '') {
//        $delete = Product::where('id' , $id)->delete();
//        Session::flash('success_msg', 'Product has been deleted successfully.'); 
//        return redirect('admin/products');
//    }
    
    public function remove($id = '') {
        $delete = Product::where('id' , $id)->update(['is_recommended' => 0]);
        Session::flash('success_msg', 'Product has been removed from new arival products successfully.'); 
        return redirect('admin/products/newarival');               
    }
    
    public function uploadFile(Request $request)
    {
        if ($request->hasFile('file')) {
            $news_image = $request->file;
            $news_img_extension = $request->file->extension();
            $path = base_path() . '/public/products/';
            $img_name  =  uniqid().'.'.$news_img_extension;
            $request->file('file')->move($path, $img_name);
            return $img_name;
        }
    }
	
    public function deleteFile(Request $request)
    {
        $file_name = $request->filetodelete;
        $path = base_path() . '/products/'.$file_name;
        if(file_exists($path)){
            unlink($path);
        }
        return $file_name;
    }
    
    public function deleteImage($p_id = '' , $id = '') 
    {
        $product = Product::where('id', $p_id)->first();
        if($product){
            $ImageUrl = json_decode($product->product_images,true);
        }
        foreach($ImageUrl as $key=>$val){
            if($val['imageId']==$id){
                $imagePath = $val['imagePath'];
                unset($ImageUrl[$key]);
            }
        }
        if(isset($imagePath)){
            $path = public_path()."/products/".$imagePath;
            if (file_exists($path))  
            { 
                unlink($path);
            }
            $ImageUrl = array_values($ImageUrl);
            Product::where('id', $p_id)->update(['product_images' =>json_encode($ImageUrl)]);
        }
        Session::flash('success_msg', 'Product Image has been deleted successfully.'); 
        return redirect('admin/product/edit/'.$p_id);
    }
    
    public function updateStatus($status = '' , $user_id = '') {
        Product::where('id', $user_id)->update(['status' => $status]);
        if($status == 0){
            Session::flash('success_msg', 'This product is out of stock successfully.'); 
        }else{
            Session::flash('success_msg', 'This product is in stock successfully.'); 
        }
        return redirect('admin/products');
    }
    
    public function seasonStatus($status = '' , $user_id = '') {
        Product::where('id', $user_id)->update(['is_season' => $status]);
        if($status == 0){
            Session::flash('success_msg', 'This product is out of season successfully.'); 
        }else{
            Session::flash('success_msg', 'This product is in season successfully.'); 
        }
        return redirect('admin/products');
    }
	
}
