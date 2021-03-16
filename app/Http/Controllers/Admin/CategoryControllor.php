<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;
use Illuminate\Support\Str;

class CategoryControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function index($parent_id = ''){
        if($parent_id == 0){
            $data['title'] = 'Manage Categories';
            $data['class'] = 'category';
            $data['table'] = 'Manage Categories';
        }else{
            $data['title'] = 'Manage Sub Categories';
            $data['class'] = 'category';
            $data['table'] = 'Manage Sub Categories';
        }
        $data['parent_id'] = $parent_id;
        $data['categories'] = Category::where('parent_id' , $parent_id)->get();
        return view('admin/categories/index' , $data);
    }

    public function create(Request $request , $parent_id = ''){
        if($parent_id == 0){
            $data['title'] = 'Manage Category';
            $data['class'] = 'category';
            $data['table'] = 'Manage Category';
        }else{
            $data['title'] = 'Manage Sub Category';
            $data['class'] = 'category';
            $data['table'] = 'Manage Sub Category';
        }
        
        if ($request->isMethod('get')) {
            return view('admin/categories/create' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/category/add/'.$parent_id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/categories/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                }
                $categories = new Category();
                $categories->parent_id = $parent_id;
                $categories->cat_title = $request->cat_title;
                $categories->cat_type = $request->cat_type;
                $categories->slug = Str::slug($request->cat_title, '-').'-'.substr(str_shuffle('123456789123456789123456789321654987'),0,4);
                $categories->title = $request->title;
                $categories->description = $request->description;
                $categories->cat_image = $image;
                $categories->save();
                Session::flash('success_msg', 'Category has been added successfully');
                return redirect('admin/categories/'.$parent_id);
            }
        }
    }

    public function edit(Request $request ,$parent_id = '', $id = ''){
       
        if($parent_id == 0){
            $data['title'] = 'Edit Category';
            $data['class'] = 'category';
            $data['table'] = 'Edit Category';
        }else{
            $data['title'] = 'Edit Sub Category';
            $data['class'] = 'category';
            $data['table'] = 'Edit Sub Category';
        }
        
        if ($request->isMethod('get')) {
                $data['category'] = Category::where('cat_id' , $id)->first();
                return view('admin/categories/edit' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                //'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/category/edit/'.$parent_id.'/'.$id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $cat_img = Category::where('cat_id' , $id)->first();
                    if($cat_img){
                        @unlink(base_path() . '/public/categories/' .$cat_img->cat_image);
                    }

                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/categories/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                } else {
                    $cat_img = Category::where('cat_id' , $id)->first();
                    if($cat_img){
                        $image = $cat_img->cat_image;
                    }
                }
                Category::where('cat_id', $id)
                        ->update([
                                'cat_title' => $request->cat_title,
                                'cat_type' => $request->cat_type,
                                'title' => $request->title,
                                'description' => $request->description,
                                'cat_image' => $image,
                            ]);
                Session::flash('success_msg', 'Category has been updated successfully.'); 
                return redirect('admin/categories/'.$parent_id);
            }
        }
    }

    public function delete($id = '') {
        $parent_id = Category::where('id' ,  $id)->first()->parent_id;
        $cat_img = Category::where('id' , $id)->first();
        if($cat_img){
            @unlink(base_path() . '/public/categories/' .$cat_img->image);
            @unlink(base_path() . '/public/categories/'.$cat_img->banner_image);
        }
        $delete = Category::where('id' , $id)->delete();
        Session::flash('success_msg', 'Category has been deleted successfully.'); 
        return redirect('admin/categories/'.$parent_id);
        
        
    }
    
    public function updateStatus($status = '' , $cat_id = '' , $id = '') {
        Category::where('id', $id)->update(['status' => $status]);
        if($status == 0){
            Session::flash('success_msg', 'Category has been active successfully.'); 
        }else{
            Session::flash('success_msg', 'Category has been blocked successfully.'); 
        }
        return redirect('admin/categories/'.$cat_id);
    }
    
    public function getCategories($parent_id = ''){
        $data['categories'] = Category::where('parent_id' , $parent_id)->get();
        return view('admin/products/ajaxcategories' , $data);
    }
	
}
