<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Category;
use App\Models\Country;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;

class DataListingControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function index($type = ""){
        
        $data['title'] = 'Manage Listing';
        $data['class'] = 'listings';
        $data['table'] = 'Manage Listing';
        $data['type']  =   $type;
        $data['listings'] = Listing::where('parent_cat_id',$type)->get();

        return view('admin/listings/index' , $data);
    }

    public function create(Request $request, $type = ""){
       $data['title'] = 'Add Listing';
        $data['class'] = 'listings';
        $data['table'] = 'Add Listing';

        
        if ($request->isMethod('get')) {
            $data['countries'] = Country::get();
            $data['parent_categories'] = Category::where('parent_id' , 0)->get();

            return view('admin/listings/create' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                // 'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/listing/add/{type}')
                                ->withInput()
                                ->withErrors($validator);
            }else{
                // if ($request->hasFile('image')) {
                //     $image_name = '';
                //     $imageTempName = $request->file('image')->getPathname();
                //     $venue_img_extension = $request->image->extension();
                //     $imageName = uniqid() . '.' . $venue_img_extension;
                //     $path = base_path() . '/public/listing/';
                //     $request->file('image')->move($path, $imageName);
                //     $image = $imageName;
                // }else{
                //     session::flash('success_msg', 'Please select image first');
                //     return redirect('admin/listing/add/{type}');
                // }
                                $is_approved = isset($request->is_approved) ? '1' : '0';
                                $isFollow= isset($request->isFollow) ? '1' : '0';

                
                
                $listing= new Listing();
                $listing->listing_title = $request->listing_title;
                $listing->listing_country = $request->listing_country;
                // $listing->image = $image;
                $listing->tab_value_1 = $request->tab_value_1;
                $listing->tab_value_2 = $request->tab_value_2;
                $listing->tab_value_3 = $request->tab_value_3;
                $listing->listing_fullimage_slug = $request->listing_fullimage_slug;
                $listing->listing_detail = $request->listing_detail;
                $listing->parent_cat_id = $request->parent_cat_id;
                $listing->cat_id = $request->cat_id;
                $listing->fees = $request->fees;
                $listing->listing_review_totalreviews = $request->listing_review_totalreviews;
                $listing->listing_review_reviewspercent = $request->listing_review_reviewspercent;
                $listing->listing_thumbnail_slug = $request->listing_thumbnail_slug;
                $listing->listing_fullimage_slug = $request->listing_fullimage_slug;
                $listing->listing_city = $request->listing_city;
                $listing->listing_cordinate_latitude = $request->listing_cordinate_latitude;
                $listing->listing_cordinate_longitude = $request->listing_cordinate_longitude;
                $listing->listing_detail_title = $request->listing_detail_title;
                $listing->listing_slug = $request->listing_slug;
                $listing->headerhtml = $request->headerhtml;
                $listing->founded = $request->founded;
                $listing->websitelink = $request->websitelink;
                $listing->phonenumber = $request->phonenumber;
                $listing->email = $request->email;
                $listing->address = $request->address;
                $listing->bookinglink = $request->bookinglink;
                $listing->is_approved = $is_approved;
                $listing->isFollow = $isFollow;
                $listing->save();
                Session::flash('success_msg', 'Listing has been added successfully');
                return redirect('admin/listings/'.$type);
            }
        }
    }

    public function edit(Request $request , $postid = ''){
       
        $data['title'] = 'Update Listing';
        $data['class'] = 'listings';
        $data['table'] = 'Update Listing';
        
        if ($request->isMethod('get')) {
            $data['parent_categories'] = Category::where('parent_id' , 0)->get();
              $data['countries'] = Country::get();
            
            $post = Post::where('postid' , $postid)->first();
            $data['post'] = $post;
            $parent_id = $post->parent_cat_id;
            $data['sub_categories'] = Category::where('parent_id' , $parent_id)->get();
            return view('admin/listings/edit' , $data);
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
