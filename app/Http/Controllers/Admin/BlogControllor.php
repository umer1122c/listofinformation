<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;

class BlogControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function index(){
        
        $data['title'] = 'Manage Blog';
        $data['class'] = 'blogs';
        $data['table'] = 'Manage Blog';
        
        $data['blogs'] = Post::get();
        return view('admin/blogs/index' , $data);
    }

    public function create(Request $request){
       $data['title'] = 'Add Blog';
        $data['class'] = 'blogs';
        $data['table'] = 'Add Blog';
        
        if ($request->isMethod('get')) {
            $data['parent_categories'] = Category::where('parent_id' , 0)->get();
            return view('admin/blogs/create' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/blog/add')
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/blogs/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                }else{
                    session::flash('success_msg', 'Please select image first');
                    return redirect('admin/blog/add');
                }
                                $is_approved = isset($request->is_approved) ? '1' : '0';
                                $isIndex= isset($request->isIndex) ? '1' : '0';

                
                
                $blog= new Post();
                $blog->title = $request->title;
                $blog->seo_title = $request->seo_title;
                $blog->image = $image;
                $blog->description = $request->description;
                $blog->image_slug = $request->image_slug;
                $blog->content = $request->content;
                $blog->parent_cat_id = $request->parent_cat_id;
                $blog->cat_id = $request->cat_id;
                $blog->meta_keyword = $request->meta_keyword;
                $blog->tags = $request->tags;
                $blog->slug = $request->slug;
                $blog->is_approved = $is_approved;
                $blog->isIndex = $isIndex;
                $blog->save();
                Session::flash('success_msg', 'Blog has been added successfully');
                return redirect('admin/blogs');
            }
        }
    }

    public function edit(Request $request , $postid = ''){
       
        $data['title'] = 'Update Blog';
        $data['class'] = 'blogs';
        $data['table'] = 'Update Blog';
        
        if ($request->isMethod('get')) {
            $data['parent_categories'] = Category::where('parent_id' , 0)->get();
            $post = Post::where('postid' , $postid)->first();
            $data['post'] = $post;
            $parent_id = $post->parent_cat_id;
            $data['sub_categories'] = Category::where('parent_id' , $parent_id)->get();
            return view('admin/blogs/edit' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                //'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/blogs/edit/'.$postid)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $cat_img = Post::where('postid' , $postid)->first();
                    if($cat_img){
                        @unlink(base_path() . '/public/blogs/' .$cat_img->image);
                    }

                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/blogs/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                } else {
                    $image = '';
                    $cat_img = Post::where('postid' , $postid)->first();
                    if($cat_img){
                            $image = $cat_img->image;
                    }
                }
                
                                $is_approved = isset($request->is_approved) ? '1' : '0';
                                $isIndex= isset($request->isIndex) ? '1' : '0';
                Post::where('postid', $postid)
                        ->update([
                                'title' => $request->title,
                                //'slug' => Str::slug($request->title, '-').'-'.substr(str_shuffle('123456789123456789123456789321654987'),0,4),
                                'seo_title' => $request->seo_title,
                                'parent_cat_id' => $request->parent_cat_id,
                                'description' => $request->slug,
                                'image' => $image,
                                'image_slug' => $request->image_slug,
                                'content' => $request->content,
                                'parent_cat_id' => $request->parent_cat_id,
                                'cat_id' => $request->cat_id,
                                'meta_keyword' => $request->meta_keyword,
                                'tags' => $request->tags,
                                'slug' => $request->slug,
                                'is_approved' => $is_approved,
                                'isIndex' => $isIndex,
                                
                            ]);
                Session::flash('success_msg', 'Blog has been updated successfully.'); 
                return redirect('admin/blogs');
            }
        }
    }

    public function delete($postid = '') {
        $cat_img = Post::where('postid' , $postid)->first();
        if($cat_img){
            @unlink(base_path() . '/public/blogs/' .$cat_img->image);
        }
        $delete = Post::where('postid' , $postid)->delete();
        Session::flash('success_msg', 'Blog has been deleted successfully.'); 
        return redirect('admin/blogs');
        
        
    }
	
}
