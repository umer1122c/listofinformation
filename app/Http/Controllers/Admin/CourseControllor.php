<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\CartItem;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Image;
use File;
use Illuminate\Support\Str;

class CourseControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function view(){
       $data['title'] = 'Manage Courses';
        $data['class'] = 'courses';
        $data['table'] = 'Manage Courses';
        return view("admin.courses.list",$data);
    }



    public function listView(){
        return DataTables::of(Course::query())->addColumn('action', function($data){
            return "<a href='".url('/')."/admin/course/edit/".$data->id."'  class='all_button btn  btn-sm btn-success edit_button'  id='".$data->id."'>Edit</a>
            <a href='javascript:void(0)' class='all_button btn btn-sm btn-danger delete_btn'  id='".$data->id."'>Delete</a>";
        })->make(true);
    }

    public function create(Request $request){
        $data['title'] = 'Add Course';
        $data['class'] = 'courses';
        $data['table'] = 'Add Course';
        
        if ($request->isMethod('get')) {
            $data['categories'] = Category::where('parent_id' , 0)->orderby('title' , 'ASC')->get();
            return view('admin/courses/create' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                'course_image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/course/add')
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('course_image')) {
                    $image_name = '';
                    $imageTempName = $request->file('course_image')->getPathname();
                    $venue_img_extension = $request->course_image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/courses/';
                    $request->file('course_image')->move($path, $imageName);
                    $image = $imageName;
                }
                $courses = new Course();
                $courses->course_id = time();
                $courses->cat_id = $request->cat_id;
                $courses->course_title = $request->course_title;
                $courses->slug = Str::slug($request->course_title, '-').'-'.substr(str_shuffle('123456789123456789123456789321654987'),0,4);
                $courses->price = $request->price;
                $courses->mata_title = $request->mata_title;
                $courses->mata_description = $request->mata_description;
                $courses->course_description = strip_tags($request->course_description);
                $courses->html_description = $request->course_description;
                $courses->course_image = $image;
                $courses->created_at = time();
                $courses->updated_at = time();
                $courses->save();
                Session::flash('success_msg', 'Course has been added successfully'); 
                return redirect('admin/courses');
            }
        }
    }

    public function edit(Request $request , $id = ''){
        $data['title'] = 'Update Course';
        $data['class'] = 'courses';
        $data['table'] = 'Update Course';
        $data['product_id'] = $id;
        $product = Course::where('id' , $id)->get();
        if(count($product) == 0){
            Session::flash('success_msg', 'This course does not exist in database.'); 
            return redirect('admin/courses');
        }
        if ($request->isMethod('get')) {
            $data['categories'] = Category::where('parent_id' , 0)->orderby('title' , 'ASC')->get();
            $course = Course::where('id' , $id)->first();
            $data['course'] = $course;
            return view('admin/courses/edit' , $data);
        }else{
            
            $validator = Validator::make($request->all(), [
                                //'cat_title' => 'required',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/course/edit/'.$id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('course_image')) {
                    $image_name = '';
                    $Course_img = Course::where('id' , $id)->first();
                    if($Course_img){
                        @unlink(base_path() . '/public/courses/' .$Course_img->course_image);
                    }

                    $imageTempName = $request->file('course_image')->getPathname();
                    $venue_img_extension = $request->course_image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/courses/';
                    $request->file('course_image')->move($path, $imageName);
                    $image = $imageName;
                } else {
                    $Course_img = Course::where('id' , $id)->first();
                    if($Course_img){
                        $image = $Course_img->course_image;
                    }
                }
                
                Course::where('id', $id)
                    ->update([
                        'cat_id' => $request->cat_id,
                        'course_title' => $request->course_title,
                        'mata_title' => $request->mata_title,
                        'mata_description' => $request->mata_description,
                        'price' => $request->price,
                        'course_description' => strip_tags($request->course_description),
                        'html_description' => $request->course_description,
                        'course_image' => $image,
                        'updated_at' => time()
                    ]);
                Session::flash('success_msg', 'Course has been updated successfully.'); 
                return redirect('admin/courses');
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
