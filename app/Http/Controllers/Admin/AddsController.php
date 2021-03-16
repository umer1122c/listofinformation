<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Adds;
use App\Models\Post;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File; 

class AddsController extends Controller
{
    public function index(){
        $data['title'] = 'Manage Advertisement';
        $data['class'] = 'adds';
        $data['table'] = 'Manage Advertisement';
        $data['adds'] = Adds::get();
        return view('admin/advertisement/index' , $data);
    }

    public function create(Request $request){
        $data['title'] = 'Add Advertisement';
        $data['class'] = 'adds';
        $data['table'] = 'Add Advertisement';
        
        if ($request->isMethod('get')) {
            $data['posts'] = Post::where('is_approved',1)->get();
            return view('admin/advertisement/create' , $data);
        }else{

            $title = $request->title;
            $validator = Validator::make($request->all(), [
                                
                            ]);
            if ($validator->fails()) {
                return redirect('admin/advertisement/add')
                                ->withInput()
                                ->withErrors($validator);
            }else{
                
                
                $adds = new Adds();
                $adds->postid = $request->postid;
                $adds->position = $request->position;
                $adds->add_content = $request->add_content;
                $adds->save();
                Session::flash('success_msg', 'Advertisement has been added successfully');
                return redirect('admin/advertisement');
            }
        }
    }

    public function edit(Request $request , $id = ''){
       
        $data['title'] = 'Update Advertisement';
        $data['class'] = 'adds';
        $data['table'] = 'Update Advertisement';
        
        if ($request->isMethod('get')) {
            $data['posts'] = Post::where('is_approved',1)->get();
            $data['adds'] = Adds::where('id' , $id)->first();
            return view('admin/advertisement/edit' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                //'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/awards/edit/'.$id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                
                
                Adds::where('id', $id)
                        ->update([
                                'postid' => $request->postid,
                                'position' => $request->position,
                                'add_content' => $request->add_content
                            ]);
                Session::flash('success_msg', 'Advertisement has been updated successfully.'); 
                return redirect('admin/advertisement');
            }
        }
    }

    public function delete($id = '') {
        $delete = Adds::where('id' , $id)->delete();
        Session::flash('success_msg', 'Advertisement has been deleted successfully.'); 
        return redirect('admin/advertisement');
        
        
    }

}
