<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Session;
use Validator;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Order;
use App\Models\BankInfo;
use App\Models\Contact;
use App\Models\Newsletter;
use App\Models\Transaction;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Former;
use Image;
use File;
use DB;

class DashboardControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
    public function dashboard(Request $request) { 
        $data['title'] = 'Dashboard';
        $data['class'] = 'dashboard';
        $data['users'] = User::count();
        return view('admin/dashboard/dashboard' , $data);
    }
    
    
    
    public function view(){
        $data['title'] = 'Get In Touch';
        $data['class'] = 'contact';
        $data['table'] = 'Get In Touch';
        return view("admin.contacts.list",$data);
    }



    public function listView(){
        return DataTables::of(Contact::query())->make(true);
    }
    
    public function newsLetter(Request $request) { 
        $data['title'] = 'Manage Newsletter';
        $data['class'] = 'newsletter';
        $data['table'] = 'Manage Newsletter';
        $data['newsletters'] = Newsletter::get();
        return view('admin/dashboard/index' , $data);
    }
    
    public function contactUs(Request $request) { 
        $data['title'] = 'Get In Touch';
        $data['class'] = 'contact';
        $data['table'] = 'Get In Touch';
        $data['newsletters'] = Newsletter::get();
        return view('admin/dashboard/index' , $data);
    }
	
    public function profile(Request $request) { 
        $data['title'] = 'Manages Profile';
        $data['class'] = 'dashboard';
        $data['table'] = 'Update Profile';
        $admin_id = Session::get('admin_id');
        if ($request->isMethod('get')) {
                $data['admins'] = Admin::where('admin_id' , $admin_id)->first();
                //dd($data['admins']);
                return view('admin/dashboard/create' , $data);
        }else{
            if ($request->hasFile('avater')) {
                $image_name = '';
                $admin_img = Admin::where('admin_id' , $admin_id)->first();
                if($admin_img){
                    $avater = $admin_img->avater;
                    @unlink(base_path() . '/users/' . $avater);
                }

                $imageTempName = $request->file('avater')->getPathname();
                $venue_img_extension = $request->avater->extension();
                $imageName = uniqid() . '.' . $venue_img_extension;
                $path = base_path() . '/users/';
                $request->file('avater')->move($path, $imageName);
                $image = $imageName;
            } else {
                $image = '';
                $admin_img = Admin::where('admin_id' , $admin_id)->first();
                if($admin_img){
                    $image = $admin_img->avater;
                }
            }

            $password = $request->admin_password;
            if($password == ''){
                Admin::where('admin_id', $admin_id)
                        ->update([
                                  'admin_name' => $request->admin_name,
                                  'avater' => $image
                              ]);
                Session::put('admin_name', $request->admin_name);
                Session::put('avater', $image);
                Session::flash('success_msg', 'Profile has been updated successfully.'); 
                return redirect('profile');
            }else{
                Admin::where('admin_id', $admin_id)
                        ->update([
                                  'admin_name' => $request->admin_name,
                                  'avater' => $image,
                                  'admin_password' => md5($password)
                              ]);
                Session::put('admin_name', $request->admin_name);
                Session::put('avater', $image);
                Session::flash('success_msg', 'Profile has been updated successfully.'); 
                return redirect('profile');
            }
        }	
    }
	
    public function settings(Request $request) { 
        $data['title'] = 'Manages Settings';
        $data['class'] = 'settings';
        $data['table'] = 'Manages Settings';
        $user_id = Session::get('user_id');
        if ($request->isMethod('get')) {
                $data['setting'] = Setting::first();
                return view('admin/settings/edit' , $data);
        }else{
            if ($request->hasFile('site_logo')) {
                $image_name = '';
                $sitelogo = Setting::first();
                if($sitelogo){
                    $site_logo = $sitelogo->site_logo;
                    @unlink(base_path() . '/public/settings/' . $site_logo);
                }

                $imageTempName = $request->file('site_logo')->getPathname();
                $venue_img_extension = $request->site_logo->extension();
                $imageName = uniqid() . '.' . $venue_img_extension;
                $path = base_path() . '/public/settings/';
                $request->file('site_logo')->move($path, $imageName);
                $image = $imageName;
            } else {
                $sitelogo = Setting::first();
                if($sitelogo){
                    $image = $sitelogo->site_logo;
                }
            }
            
            Setting::where('id', 1)
                ->update([
                    'site_name' => $request->site_name,
                    'site_title' => $request->site_title,
                    'site_email' => $request->site_email,
                    'from_email' => $request->from_email,
                    'phone_number' => $request->phone_number,
                    'site_footer_text' => $request->site_footer_text,
                    'site_logo' => $image
                ]);
            Session::flash('success_msg', 'Setting has been updated successfully.'); 
            return redirect('admin/settings');
        }
    }
}
