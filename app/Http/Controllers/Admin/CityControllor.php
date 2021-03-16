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

class CityControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function index(){
        $data['title'] = 'Manage cities';
        $data['class'] = 'city';
        $data['table'] = 'Manage Countries';
        $data['cities'] = City::select('cities.*','countries.name as country')->join('countries','countries.id' , '=' , 'cities.country_id')->get();
        return view('admin/cities/index' , $data);
    }

    public function create(Request $request){
        $data['title'] = 'Add City';
        $data['class'] = 'city';
        $data['table'] = 'Add City';
        if ($request->isMethod('get')) {
            $data['countries'] = Country::get();
            return view('admin/cities/create' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                'name' => 'required',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/city/add')
                                ->withInput()
                                ->withErrors($validator);
            }else{
                
                $cities = new City();
                $cities->region_id  = 0;
                $cities->country_id = $request->country_id;
                $cities->name = $request->name;
                $cities->save();
                Session::flash('success_msg', 'City has been added successfully');
                return redirect('admin/cities');
            }
        }
    }

    public function edit(Request $request , $id = ''){
       
        $data['title'] = 'Edit City';
        $data['class'] = 'city';
        $data['table'] = 'Edit City';
        if ($request->isMethod('get')) {
            $data['countries'] = Country::get();
            $data['city'] = City::where('id' , $id)->first();
            return view('admin/cities/edit' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                //'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/city/edit/'.$id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                
                
                City::where('id', $id)
                        ->update([
                                'country_id' => $request->country_id,
                                'name' => $request->name,
                            ]);
                Session::flash('success_msg', 'City has been updated successfully.'); 
                return redirect('admin/cities');
            }
        }
    }

    public function delete($id = '') {
        
        $delete = City::where('id' , $id)->delete();
        Session::flash('success_msg', 'City has been deleted successfully.'); 
        return redirect('admin/cities');
        
        
    }
	
}
