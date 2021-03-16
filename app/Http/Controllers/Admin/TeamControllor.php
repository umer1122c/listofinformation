<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;

class TeamControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function index($parent_id = ''){
        
        $data['title'] = 'Manage Team';
        $data['class'] = 'team';
        $data['table'] = 'Manage Team';
        
        $data['teams'] = Team::get();
        return view('admin/teams/index' , $data);
    }

    public function create(Request $request , $parent_id = ''){
        $data['title'] = 'Add Team';
        $data['class'] = 'service';
        $data['table'] = 'Add Team';
        
        if ($request->isMethod('get')) {
            return view('admin/teams/create' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/team/add')
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/uploads/teams/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                }else{
                    ession::flash('success_msg', 'Please select image first');
                    return redirect('admin/team/add');
                }
                
                $teams = new Team();
                $teams->memberImage = $image;
                $teams->memberName = $request->memberName;
                $teams->memberDesignation = $request->memberDesignation;
                $teams->memberInformation = $request->memberInformation;
                $teams->save();
                Session::flash('success_msg', 'Team has been added successfully');
                return redirect('admin/teams');
            }
        }
    }

    public function edit(Request $request , $id = ''){
       
        $data['title'] = 'Update Team';
        $data['class'] = 'team';
        $data['table'] = 'Update Team';
        
        if ($request->isMethod('get')) {
            $data['team'] = Team::where('id' , $id)->first();
            return view('admin/teams/edit' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                //'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/team/edit/'.$id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                if ($request->hasFile('image')) {
                    $image_name = '';
                    $cat_img = Team::where('id' , $id)->first();
                    if($cat_img){
                        @unlink(base_path() . '/public/uploads/teams/' .$cat_img->memberImage);
                    }

                    $imageTempName = $request->file('image')->getPathname();
                    $venue_img_extension = $request->image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/uploads/teams/';
                    $request->file('image')->move($path, $imageName);
                    $image = $imageName;
                } else {
                    $image = '';
                    $cat_img = Team::where('id' , $id)->first();
                    if($cat_img){
                        $image = $cat_img->memberImage;
                    }
                }
                
                Team::where('id', $id)
                        ->update([
                                'memberName' => $request->memberName,
                                'memberImage' => $image,
                                'memberDesignation' => $request->memberDesignation,
                                'memberInformation' => $request->memberInformation
                            ]);
                Session::flash('success_msg', 'Team has been updated successfully.'); 
                return redirect('admin/teams');
            }
        }
    }

    public function delete($id = '') {
        $cat_img = Team::where('id' , $id)->first();
        if($cat_img){
            @unlink(base_path() . '/public/uploads/teams/' .$cat_img->image);
        }
        $delete = Team::where('id' , $id)->delete();
        Session::flash('success_msg', 'Team has been deleted successfully.'); 
        return redirect('admin/teams');
        
        
    }
	
}
