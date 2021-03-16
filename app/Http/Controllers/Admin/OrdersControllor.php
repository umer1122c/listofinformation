<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\CustomerPoint;
use App\Models\Driver;
use App\Models\Transaction;
use App\Models\ServiceCategory;
use App\Models\UserLoginData;
use App\Models\Category;
use App\Models\Order;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Classes\CommonLibrary;
use commonHelper;
use Image;
use File;
use URL;
use Mail;
use DB;

class OrdersControllor extends Controller
{
	
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function view(){
        
        $data['title'] = 'Manage Orders';
        $data['class'] = 'orders';
        $data['table'] = 'Manage Orders';
        return view("admin.orders.list",$data);
    }



    public function listView(){
        $query = DB::table('orders')
                    ->select('orders.*','users.user_id as u_id','users.first_name','users.last_name','users.created_at as register_on')
                    ->join('users', 'users.user_id', '=', 'orders.user_id');
                    //->get();
                    //dd($query);
        return DataTables::of($query)
                ->addColumn('no_of_orders', function ($query) {
                    return Order::where('user_id' , $query->u_id)->whereIn('status' , ['In Progress' , 'Delivered'])->count();
                })
                ->addColumn('total', function ($query) {
                    
                    return number_format($query->order_total - $query->discount_amount,2);
                })
                ->addColumn('action', function($data){
                    if($data->status == 'Pending'){
                        return "<a href='javascript:void(0);'  class='all_button btn  btn-sm btn-info edit_button' dilivery_date='".$data->dilivery_date."' status='".$data->status."' id='".$data->order_id."'>Update Status</a><a href='".url('/')."/admin/order/detail/".$data->order_id."'  class='all_button btn  btn-sm btn-success' id='".$data->id."'>Order Detail</a><a href='javascript:void(0)' class='all_button btn btn-sm btn-danger delete_btn' id='".$data->order_id."'>Delete</a>";
                    }else{
                        return "<a href='javascript:void(0);'  class='all_button btn  btn-sm btn-info edit_button' dilivery_date='".$data->dilivery_date."' status='".$data->status."' id='".$data->order_id."'>Update Status</a><a href='".url('/')."/admin/order/detail/".$data->order_id."'  class='all_button btn  btn-sm btn-success' id='".$data->id."'>Order Detail</a>";
                    }
        })->make(true);
    }
	
    public function index($type = ''){
        if($type == 'normal'){
            $data['title'] = 'Manage Normal Orders';
            $data['class'] = 'orders';
            $data['table'] = 'Manage Normal Orders';
            $data['orders'] = DB::table('orders')
                                ->select('orders.*','users.first_name','users.last_name','user_address.*')
                                ->join('users', 'users.user_id', '=', 'orders.user_id')
                                ->join('user_address', 'user_address.address_id', '=', 'orders.address' , 'left')
                                //->where('orders.status' , '!=' , 'Pending')
                                ->where('orders.order_type' , 1)
                                ->get();
            //dd($data['orders']);
        }else{
            $data['title'] = 'Manage Pally Orders';
            $data['class'] = 'orders';
            $data['table'] = 'Manage Pally Orders';
            $data['orders'] = DB::table('orders')
                        ->select('orders.*','users.first_name','users.last_name','user_address.*')
                        ->join('users', 'users.user_id', '=', 'orders.user_id')
                        ->join('user_address', 'user_address.address_id', '=', 'orders.address' , 'left')
                        //->where('orders.status' , '!=' , 'Pending')
                        ->where('orders.order_type' , 2)
                        ->get();
        }
        return view('admin/orders/index' , $data);
    }
    
    public function orderDetail(Request $request , $TransactionID = ''){
        $data['title'] = 'Manage Orders Detail';
        $data['class'] = 'orders';
        $data['table'] = 'Manage Orders Detail';
        $data['invoice_no'] = $TransactionID;
        $prodImageUrl = URL::to('').'/products/';
        $is_product = 0;
        $is_service = 0;
        if ($request->isMethod('get')) {
            $orderArray = [];
            $transactions = DB::table('orders')->where('order_id' ,$TransactionID)->first();
            $data['orders'] = $transactions;
            if($transactions){
                $data['DateTime'] = date('d M Y' , strtotime($transactions->created_at));
                $user = User::where('user_id' , $transactions->user_id)->first();
                $data['first_name'] = $user->first_name;
                $data['last_name'] = $user->last_name;
                $data['phone'] = $user->phone;
                $data['email'] = $user->email;
                $data['address'] =$user->address;
                $data['address1'] =$user->address1;
            }else{
                $data['DateTime'] = date('d M Y');
                $data['first_name'] = '';
                $data['last_name'] = '';
                $data['address'] = '';
                $data['address1'] = '';
                $data['phone'] = '';
                $data['email'] = '';
            }
            
            $data['address'] = '';
            $data['phone_number'] = '';
            
            $order_details = DB::table('order_details')->select('id','order_id','type_id','name' , 'quantity' , 'price','delivery_status')->where('order_id' , $TransactionID)->get();
            //$orders->order_details = $order_details;
            foreach($order_details as $row){
                $products = Product::select('product_images','product_title')->where('product_id' , $row->type_id)->first();
                //dd($products);
                $row->product_title = $products->product_title;
                if($products){
                    $product_images = json_decode($products->product_images);
                    if(count($product_images) > 0){
                        $row->product_image = $prodImageUrl.$product_images[0]->imagePath;
                    }else{
                        $row->product_image = asset('front/placeholder.png');
                    }
                }else{
                    $row->product_image = asset('front/placeholder.png');
                }
                //return $row->product_image;
                $orderArray[] = $row;
            }
            $data['transaction_items'] = $orderArray;
            return view('admin/orders/detail' , $data);
        }else{
            DB::table('order_details')->where('id' , $request->item_id)->update(['delivery_status' => $request->delivery_status]);
            return redirect('admin/order/detail/'.$TransactionID);
        }
    }
    
    public function refundAmount(Request $request){
        $savTrans = DB::table('transactions')->insert(['from_user_id' => 0 ,'to_user_id' => $request->user_id ,'amount'=> $request->amount,'order_id'=> $request->id,'note'=> $request->note,'trans_type'=> 2,'status'=> 1, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]);
        return ["status" => true,"message" =>"Refund order amount successfully"];
    }
    
    public function indexLinks($user_id = ''){
        $data['title'] = 'Manage Pally Links';
        $data['class'] = 'orders';
        $data['table'] = 'Manage Pally Links';
        $data['pallys'] = DB::table('pallys')
                    ->select('pallys.*','users.first_name','users.last_name','products.product_title')
                    ->join('users', 'users.user_id', '=', 'pallys.user_id')
                    ->join('products', 'products.product_id', '=', 'pallys.product_id' , 'left')
                    ->where('pallys.user_id' , $user_id)
                    ->get();
        //dd($data['orders']);
        return view('admin/orders/pally' , $data);
    }
    
    public function updateStatus(Request $request){
        $id = $request->id;
        $order = Order::where('order_id' , $id)->first();
        $dilivery_date = date('Y-m-d h:i:s' , strtotime($request->dilivery_date));
        Order::where('order_id', $id)
                ->update([
                        'dilivery_date' => $dilivery_date,
                        'status' => $request->status
                    ]);
        return ["status" => true,"message" =>"Order status updated successfully"];
    }


    public function edit(Request $request , $id = ''){
       
        
        $data['title'] = 'Update Order Status';
        $data['class'] = 'orders';
        $data['table'] = 'Update Order Status';
        
        
        if ($request->isMethod('get')) {
            $data['order'] = Order::where('order_id' , $id)->first();
            return view('admin/orders/edit' , $data);
        }else{
            $validator = Validator::make($request->all(), [
                                //'image' => 'mimes:jpeg,jpg,png,gif|required|max:1600',
                            ]);
            if ($validator->fails()) {
                return redirect('admin/order/update/status/'.$id)
                                ->withInput()
                                ->withErrors($validator);
            }else{
                $order = Order::where('order_id' , $id)->first();
                $dilivery_date = date('Y-m-d h:i:s' , strtotime($request->dilivery_date));
                Order::where('order_id', $id)
                        ->update([
                                'dilivery_date' => $dilivery_date,
                                'status' => $request->status
                            ]);
                if($request->status == 'In Progress'){
                    $order_id = $id;
                    $user_id = $order->user_id;
                    $order_details = DB::table('order_details')->where('order_id' , $order_id)->get();
                    if(count($order_details) > 0){
                        foreach($order_details as $row){
                            $pally_id = $row->pally_id;
                            $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally_id)->first();
                            if($open_pallys){
                                $open_pallys->pally_type;
                                $number_of_person = $open_pallys->number_of_person;
                                $pally_count = $open_pallys->pally_count;
                                $users_name = User::where('user_id' , $user_id)->first();
                                if($users_name){
                                    $users_name = $users_name->first_name;
                                }else{
                                    $users_name = '';
                                }
                                if($open_pallys->pally_type == 'Open'){
                                    $pally_users = DB::table('close_pally_users')->where('pally_id' , $pally_id)->get();
                                    if(count($pally_users) > 0){
                                        foreach($pally_users as $row1){
                                            if($user_id != $row1->user_id){
                                                $usersname = User::where('user_id' , $row1->user_id)->first();
                                                $title = 'Open Pally Paid';
                                                $body = $users_name.' has paid a share of the open pally';
                                                $username = $usersname;
                                                $pally_id = $pally_id;
                                                $type = 'pally_friend';
                                                //Send Notification
                                                $this->sendNotificationToUser($user_id,$row1->user_id,$usersname,$title,$body,$pally_id,$type);
                                                //End Notification
                                            }
                                        }
                                    }
                                    if($pally_count <= $number_of_person){
                                        $pallyCurrentUserArray = [];
                                        $pallyCurrentUserArray['type'] = 'Open';
                                        $pallyCurrentUserArray['pally_id'] = $pally_id;
                                        $pallyCurrentUserArray['user_id'] = $user_id;
                                        $pallyCurrentUserArray['status'] = 1;
                                        $pallyCurrentUserArray['created_at'] = time();
                                        $pallyCurrentUserArray['updated_at'] = time();
                                        DB::table('close_pally_users')->insert($pallyCurrentUserArray);
                                        $close_pally_users = DB::table('close_pally_users')->where('user_id' , $user_id)->where('pally_id' , $pally_id)->first();
                                        if($close_pally_users){
                                            DB::table('close_pally_users')->where('id' , $close_pally_users->id)->update(['status' => 1]);
                                        }
                                        $order_details_res = DB::table('order_details')->where('order_id' , $row->order_id)->where('pally_id' , $pally_id)->first();
                                        DB::table('close_pally_users')->where('user_id' , $user_id)->where('pally_id' , $pally_id)->update(['status' => 1]);
                                        $pally_count_increment = $pally_count + $order_details_res->quantity;
                                        if($number_of_person == $pally_count_increment){
                                            DB::table('open_pallys')->where('pally_id' , $pally_id)->update(['pally_count' => $pally_count_increment,'status' => 1]);
                                        }else{
                                            DB::table('open_pallys')->where('pally_id' , $pally_id)->update(['pally_count' => $pally_count_increment,'status' => 0]);
                                        }
                                    }
                                }else{
                                    //return '2222';
                                    if($pally_count == 0){
                                        $pally_users = DB::table('close_pally_users')->where('pally_id' , $pally_id)->get();
                                        //dd($pally_users);
                                        if(count($pally_users) > 0){
                                            foreach($pally_users as $row){
                                                if($user_id != $row->user_id){
                                                    $usersname = User::where('user_id' , $row->user_id)->first();
                                                    $title = 'New Pally Request';
                                                    $body = $users_name.' is inviting you to share a wholesale product via a closed pally';
                                                    $username = $usersname;
                                                    $pally_id = $pally_id;
                                                    $type = 'close';
                                                    //Send Notification
                                                    $this->sendNotificationToUser($user_id,$row->user_id,$usersname,$title,$body,$pally_id,$type);
                                                    //End Notification
                                                }
                                            }
                                        } 


                                    }
                                    $pally_users = DB::table('close_pally_users')->where('pally_id' , $pally_id)->get();
                                    //dd($pally_users);
                                    if(count($pally_users) > 0){
                                        foreach($pally_users as $row){
                                            if($user_id != $row->user_id){
                                                $usersname = User::where('user_id' , $row->user_id)->first();
                                                $title = 'Close Pally Paid';
                                                $body = $users_name.' has paid a share of the close pally';
                                                $username = $usersname;
                                                $pally_id = $pally_id;
                                                $type = 'pally_friend';
                                                //Send Notification
                                                $this->sendNotificationToUser($user_id,$row->user_id,$usersname,$title,$body,$pally_id,$type);
                                                //End Notification
                                            }
                                        }
                                    }
                                    if($pally_count <= $number_of_person){
                                        DB::table('close_pally_users')->where('user_id' , $user_id)->where('pally_id' , $pally_id)->update(['status' => 1]);
                                        $pally_count_increment = $pally_count + 1;
                                        if($number_of_person == $pally_count_increment){
                                            DB::table('open_pallys')->where('pally_id' , $pally_id)->update(['pally_count' => $pally_count_increment,'status' => 1]);
                                        }else{
                                            $pally_count_increment = $pally_count + 1;
                                            DB::table('open_pallys')->where('pally_id' , $pally_id)->update(['pally_count' => $pally_count_increment,'status' => 0]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $userOrders = DB::table('orders')->where('status' , 'In Progress')->where('user_id' , $user_id)->count();
                    //dd($userOrders);
                    if($userOrders == 1){
                        $to_user_id = $user_id;
                        $currentUserTransaction = DB::table('transactions')->where('to_user_id' , $to_user_id)->first();
                        if($currentUserTransaction){
                            $from_user_id = $currentUserTransaction->from_user_id;
                            DB::table('transactions')->where('from_user_id' , $from_user_id)->where('to_user_id' , $to_user_id)->update(['status' => 1]);
                            DB::table('transactions')->where('from_user_id' , $to_user_id)->where('to_user_id' , $from_user_id)->update(['status' => 1]);
                        }
                    }
                }
                Session::flash('success_msg', 'Order status has been updated successfully.'); 
                $order_type = $order->order_type;
                if($order_type == 1){
                    return redirect('admin/orders/normal');
                }elseif($order_type == 2){
                    return redirect('admin/orders/pally');
                }
            }
        }
    }
    
    public function sendNotificationToUser($user_id , $follower_id,$username,$title,$body,$pally_id,$type){
        $users = UserLoginData::select('deviceToken','userId')->where('userId' , $follower_id)->where('tokenStatus',0)->get();
        if(count($users) > 0){
            $deviceToken = [];
            foreach($users as $key => $val ){
                $deviceToken[] = $val->deviceToken;
            }
            $message =  array(
                            'type' => 'notification',
                            'title' => $title,
                            'body' => $body,
                            'username' => $username,
                            'pally_id' => $pally_id,
                            'notification_userid' => $follower_id,
                            'type1' => $type,
                            'sound'=> 'default',
                            'content-available'=> true,
                            'icon' => 'chat1'
                        );
            commonHelper::firebase($deviceToken,$message);
            commonHelper::saveNotification($user_id,$follower_id,$username,$title,$body,$type,$pally_id);
        }
    }
    
    public function palliedFriends($pally_id , $order_id = '')
    {
        $data['title'] = 'Pallied Friends';
        $data['class'] = 'orders';
        $data['table'] = 'Pallied Friends';
        $pally = DB::table('open_pallys')->select('pally_id','pally_type','number_of_person as pally_size')->where('pally_id' , $pally_id)->first();
        $usersArray = [];
        if($pally){
            if($pally->pally_type == 'Open'){
                $pally_detail =DB::table('open_pallys')->select('number_of_person','pally_count','product_id')->where('pally_id' , $pally_id)->first();
                if($pally_detail){
                    $product_id =  $pally_detail->product_id;
                    $product = Product::where('product_id',$product_id)->first();
                    //dd($product);
                    if($product){
                        $data['product_title'] = $product->product_title;
                        $data['product_unit'] = $product->product_unit;
                    }else{
                        $data['product_title'] = '';
                        $data['product_unit'] = '';
                    }
                    $data['number_of_person'] = $pally_detail->number_of_person;
                    $data['pally_count'] = $pally_detail->pally_count;
                }else{
                    $data['number_of_person'] = 0;
                    $data['pally_count'] = 0;
                    $data['product_title'] = '';
                    $data['product_unit'] = '';
                }
                $pally_users =DB::table('open_pallys')
                        ->select('open_pallys.*','close_pally_users.user_id','close_pally_users.status','users.first_name','users.last_name','users.user_image','users.social_image')
                        ->join('close_pally_users', 'open_pallys.pally_id', '=', 'close_pally_users.pally_id')
                        ->join('users', 'users.user_id', '=', 'close_pally_users.user_id')
                        ->where('close_pally_users.pally_id' , $pally_id)
                        ->get();
                //dd($pally_users);
                if(count($pally_users) > 0){
                    foreach($pally_users as $ord){
                        
                        if($ord->status == 1){
                            $ord->order_status = 'Paid';
                        }else{
                            $ord->order_status = 'UnPaid';
                        }
                        if($ord->last_name == null){
                            $ord->last_name =  '';
                        }

                        if($ord->user_image == ''){
                            if($ord->social_image != ''){
                                $ord->user_image = $ord->social_image;
                            }else{
                                $ord->user_image = URL::to('/') .'/front/images/dummy_round.png';
                            }
                        }else{
                            $ord->user_image = URL::to('/') . '/users/'.$ord->user_image;
                        }
                        $usersArray[] = $ord;
                    }
                }
                $pally->pallyusers = $usersArray;
            }else{
                $pally_detail =DB::table('open_pallys')->select('number_of_person','pally_count','product_id')->where('pally_id' , $pally_id)->first();
                if($pally_detail){
                    $product_id =  $pally_detail->product_id;
                    $product = Product::where('product_id',$product_id)->first();
                    //dd($product);
                    if($product){
                        $data['product_title'] = $product->product_title;
                        $data['product_unit'] = $product->product_unit;
                    }else{
                        $data['product_title'] = '';
                        $data['product_unit'] = '';
                    }
                    $data['number_of_person'] = $pally_detail->number_of_person;
                    $data['pally_count'] = $pally_detail->pally_count;
                }else{
                    $data['number_of_person'] = 0;
                    $data['pally_count'] = 0;
                    $data['product_title'] = '';
                    $data['product_unit'] = '';
                }
                $pally_users =DB::table('open_pallys')
                        ->select('open_pallys.number_of_person','open_pallys.pally_count','close_pally_users.user_id','close_pally_users.status','users.first_name','users.last_name','users.user_image','users.social_image')
                        ->join('close_pally_users', 'open_pallys.pally_id', '=', 'close_pally_users.pally_id')
                        ->join('users', 'users.user_id', '=', 'close_pally_users.user_id')
                        ->where('close_pally_users.pally_id' , $pally_id)
                        ->get();
                //dd($pally_users);
                if(count($pally_users) > 0){
                    foreach($pally_users as $ord){
                        if($ord->status == 1){
                            $ord->order_status = 'Paid';
                        }else{
                            $ord->order_status = 'UnPaid';
                        }
                        if($ord->last_name == null){
                            $ord->last_name =  '';
                        }

                        if($ord->user_image == ''){
                            if($ord->social_image != ''){
                                $ord->user_image = $ord->social_image;
                            }else{
                                $ord->user_image = URL::to('/') .'/front/images/dummy_round.png';
                            }
                        }else{
                            $ord->user_image = URL::to('/') . '/users/'.$ord->user_image;
                        }
                        $usersArray[] = $ord;
                    }
                }
                $pally->pallyusers = $usersArray;
            }
            $data['pally_friends'] = $pally->pallyusers;
            //dd($data['pally_friends']);
            return view('admin/orders/pallied_friends' , $data);
        }else{
            $data['pally_friends'] = [];
            return view('admin/orders/pallied_friends' , $data);
        }
    }
    
    
    public function delete(){
        DB::table('order_details')->where('order_id' , request()->id)->delete();
        Order::where(["order_id"=>request()->id])->delete();
        return ["status" => true,"message" =>"Record deleted successfully"];
    }
}