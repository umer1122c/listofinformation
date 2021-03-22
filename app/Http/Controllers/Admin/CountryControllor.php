<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\City;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;

class CountryControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function index(){
        $data['title'] = 'Manage Countries';
        $data['class'] = 'country';
        $data['table'] = 'Manage Countries';
        $data['countries'] = Country::get();
        return view('admin/countries/index' , $data);
    }

    public function create(Request $request){
        $data['title'] = 'Add Country';
        $data['class'] = 'country';
        $data['table'] = 'Add Country';
        if ($request->isMethod('get')) {
            return view('admin/countries/create' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/country/add')
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/countries/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                }else{
                    ession::flash('success_msg', 'Please select image first');
                    return redirect('admin/country/add');
                }
                
                $countries = new Country();
                $countries->name = $request->name;
                $countries->image = $image;
                $countries->save();
                Session::flash('success_msg', 'Country has been added successfully');
                return redirect('admin/countries');
            }
        }
    }

    public function edit(Request $request , $id = ''){
       
        $data['title'] = 'Edit Country';
        $data['class'] = 'country';
        $data['table'] = 'Edit Country';
        if ($request->isMethod('get')) {
                $data['country'] = Country::where('id' , $id)->first();
                return view('admin/countries/edit' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                //'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/country/edit/'.$id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $cat_img = Country::where('id' , $id)->first();
                    if($cat_img){
                        @unlink(base_path() . '/public/countries/' .$cat_img->image);
                    }

                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/countries/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                } else {
                    $image = '';
                    $cat_img = Country::where('id' , $id)->first();
                    if($cat_img){
                            $image = $cat_img->image;
                    }
                }
                
                Country::where('id', $id)
                        ->update([
                                'name' => $request->name,
                                'image' => $image
                            ]);
                Session::flash('success_msg', 'Country has been updated successfully.'); 
                return redirect('admin/countries');
            }
        }
    }

    public function delete($id = '') {
        $data['title'] = 'Edit Country';
        $data['class'] = 'country';
        $data['table'] = 'Edit Country';
        $cat_img = Country::where('id' , $id)->first();
        if($cat_img){
            @unlink(base_path() . '/public/countries/' .$cat_img->image);
        }
        $delete = Country::where('id' , $id)->delete();
        Session::flash('success_msg', 'Country has been deleted successfully.'); 
        return redirect('admin/countries');
        
        
    }
     public function getCities($country_id = ''){
        $data['cities'] = City::where('country_id' , $country_id)->get();
        return view('admin/listings/ajaxcities' , $data);
    }
	
	
}
