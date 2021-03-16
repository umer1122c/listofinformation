<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;

class TestimonialControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function index($parent_id = ''){
        
        $data['title'] = 'Manage Testimonials';
        $data['class'] = 'team';
        $data['table'] = 'Manage Testimonials';
        
        $data['testimonials'] = Testimonial::get();
        return view('admin/testimonials/index' , $data);
    }

    public function create(Request $request , $parent_id = ''){
        $data['title'] = 'Add Testimonials';
        $data['class'] = 'service';
        $data['table'] = 'Add Testimonials';
        
        if ($request->isMethod('get')) {
            return view('admin/testimonials/create' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/testimonial/add')
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/testimonials/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                }else{
                    ession::flash('success_msg', 'Please select image first');
                    return redirect('admin/testimonial/add');
                }
                
                $testimonials = new Testimonial();
                $testimonials->image = $image;
                $testimonials->name = $request->name;
                $testimonials->review = $request->review;
                $testimonials->rating = $request->rating;
                $testimonials->save();
                Session::flash('success_msg', 'Testimonial has been added successfully');
                return redirect('admin/testimonials');
            }
        }
    }

    public function edit(Request $request , $id = ''){
       
        $data['title'] = 'Update Testimonials';
        $data['class'] = 'team';
        $data['table'] = 'Update Testimonials';
        
        if ($request->isMethod('get')) {
            $data['testimonial'] = Testimonial::where('id' , $id)->first();
            return view('admin/testimonials/edit' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                //'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/testimonial/edit/'.$id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $cat_img = Testimonial::where('id' , $id)->first();
                    if($cat_img){
                        @unlink(base_path() . '/public/testimonials/' .$cat_img->image);
                    }

                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/testimonials/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                } else {
                    $image = '';
                    $cat_img = Testimonial::where('id' , $id)->first();
                    if($cat_img){
                        $image = $cat_img->image;
                    }
                }
                
                Testimonial::where('id', $id)
                        ->update([
                                'name' => $request->name,
                                'image' => $image,
                                'review' => $request->review,
                                'rating' => $request->rating
                            ]);
                Session::flash('success_msg', 'Testimonial has been updated successfully.'); 
                return redirect('admin/testimonials');
            }
        }
    }

    public function delete($id = '') {
        $cat_img = Testimonial::where('id' , $id)->first();
        if($cat_img){
            @unlink(base_path() . '/public/testimonials/' .$cat_img->image);
        }
        $delete = Testimonial::where('id' , $id)->delete();
        Session::flash('success_msg', 'Testimonial has been deleted successfully.'); 
        return redirect('admin/testimonials');
        
        
    }
	
}
