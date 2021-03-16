<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Session;

class AdminControllor extends Controller
{
	
	public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
	
	public function index(){
		$data['title'] = 'Manage Admins';
		$data['class'] = 'admins';
		$data['table'] = 'Manage Admins';
		$data['admins'] = Admin::get();
		return view('admin/admins/index' , $data);
	}
	
	public function updateStatus($status = '' , $admin_id = '') {
		
		Admin::where('admin_id', $admin_id)
          ->update([
					'status' => $status
					]
				   );
		if($status == 0){
			Session::flash('success_msg', 'Admin has been inactiv successfully.'); 
		}else{
			Session::flash('success_msg', 'Admin has been activ successfully.'); 
		}
		return redirect('admins');
        
    }
	
	public function delete($admin_id = '') {
		
		$delete = Admin::where('admin_id' , $admin_id)->delete();
		Session::flash('success_msg', 'Admin has been deleted successfully.'); 
		return redirect('admins');
        
    }
}
