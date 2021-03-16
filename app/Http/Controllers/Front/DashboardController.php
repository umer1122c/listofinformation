<?php

namespace App\Http\Controllers\Front;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\Area;
use App\Models\Wallet;
use App\Models\CartItem;
use App\Models\OrderDetails;
use App\Models\Setting;
use App\Classes\CommonLibrary;
use commonHelper;
use Illuminate\Support\Facades\Hash;
use Session;
use Helper;
use App\Models\Follower;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request){
        $data['title'] = 'My Account';
        $data['description'] = '';
        $data['class'] = 'account';
        $userId = session('user_id');
        if ($request-> isMethod('get')) {
            $data['userDetails'] = User::where('user_id','=',$userId)->first();
            return view('front/dashboard/index',$data);
        } 
    }
    
    public function changePassword(Request $request){
        $data['title'] = 'Change Password';
        $data['description'] = 'Change Password';
        $data['class'] = 'password';
        $userId = session('user_id');
        if ($request-> isMethod('get')) {
            $data['userDetails'] = User::where('user_id','=',$userId)->first();
            return view('front/dashboard/change_pasword',$data);
        }else{
            $user = User::where('user_id','=',$userId)->first();
            if($user){
                $user_password = $user->password;
                if(Hash::check($request->old_password, $user_password)){
                    User::where('user_id', $userId)->update(['password' => Hash::make($request->new_password)]);
                    return response()->json(['status' => "success","message" => 'Password has been updated successfully.'],200);
                }else{
                    return response()->json(['status' => "failed","message" => 'Oops! old password does not matched.'],200);
                }
            }else{
                return response()->json(['status' => "failed","message" => 'Oops! went something wrong.'],200);
            }
        }
    }
    
    public function updateProfile(Request $request){
        $user_id = session('user_id');
        
        if($request->user_name != $request->old_user_name){
            $exituser = User::where('user_name', $request->user_name)->first();
            if($exituser){ 
                return response()->json(['status'=>"alreadyexist"],200);
            }else{
                if($request->hasFile('user_image')) {
                    $image_name = '';
                    $user_img = User::where('user_id' , $user_id)->first();
                    if($user_img){
                        $avater = $user_img->user_image;
                        @unlink(base_path() . '/public/users/' . $avater);
                    }
                    $imageTempName = $request->file('user_image')->getPathname();
                    $venue_img_extension = $request->user_image->extension();
                    $imageName = uniqid() . '.' . $venue_img_extension;
                    $path = base_path() . '/public/users/';
                    $request->file('user_image')->move($path, $imageName);
                    $image = $imageName;
                    $social_image = '';
                }else{
                    $user_img = User::where('user_id' , $user_id)->first();
                    if($user_img){
                        if($user_img->social_image != ''){
                            $social_image = $user_img->social_image;
                            $image = '';
                        }else{
                            $image = $user_img->user_image;
                            $social_image = '';
                        }
                    }
                }
                User::where('user_id', $user_id)
                ->update([
                            'first_name' => $request->first_name,
                            'last_name' =>$request->last_name, 
                            'business_name' => $request->business_name,
                            'user_name' =>$request->user_name,
                            'phone' => $request->phone,
                            'user_image' => $image,
                            'social_image' => $social_image
                        ]);
                $user = User::where('user_id', $user_id)->first();
                if($user->user_image == ''){
                    $user_image = !empty($user->social_image) ? $user->social_image : url('/front/dummy_round.png');
                }else{
                    $user_image = url('/') . '/users/'.$user->user_image;
                }
                Session::put('first_name', $user->first_name);
                Session::put('last_name', $user->last_name);
                Session::put('user_image', $user_image);
                return response()->json(['status' => "success",'message' =>'Profile has been updated successfully!',"user_name" => $user_name,"user_image" => $user_image],200);
            }
        }else{
            if($request->hasFile('user_image')) {
                $image_name = '';
                $user_img = User::where('user_id' , $user_id)->first();
                if($user_img){
                    $avater = $user_img->user_image;
                    @unlink(base_path() . '/public/users/' . $avater);
                }
                $imageTempName = $request->file('user_image')->getPathname();
                $venue_img_extension = $request->user_image->extension();
                $imageName = uniqid() . '.' . $venue_img_extension;
                $path = base_path() . '/public/users/';
                $request->file('user_image')->move($path, $imageName);
                $image = $imageName;
                $social_image = '';
            }else{
                $user_img = User::where('user_id' , $user_id)->first();
                if($user_img){
                    if($user_img->social_image != ''){
                        $social_image = $user_img->social_image;
                        $image = '';
                    }else{
                        $image = $user_img->user_image;
                        $social_image = '';
                    }
                }
            }
            User::where('user_id', $user_id)
                ->update([
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name, 
                            'business_name' => $request->business_name,
                            'user_name' =>$request->user_name,
                            'phone' => $request->phone,
                            'user_image' => $image,
                            'social_image' => $social_image
                        ]);
            $user = User::where('user_id', $user_id)->first();
            if($user->user_image == ''){
                $user_image = !empty($user->social_image) ? $user->social_image : url('/front/dummy_round.png');
            }else{
                $user_image = url('/') . '/users/'.$user->user_image;
            }
            Session::put('first_name', $user->first_name);
            Session::put('last_name', $user->last_name);
            Session::put('user_image', $user_image);
            $user_name = $request->first_name.' '.$request->last_name;
            return response()->json(['status' => "success",'message' =>'Profile has been updated successfully!',"user_name" => $user_name,"user_image" => $user_image],200);
        }
    }
    
    public function followers(Request $request){
        $data['title'] = 'FOLLOWERS DETAILS';
        $data['description'] = '';
        $data['class'] = 'followers';
        $userId = session('user_id');
        $data['fallowers'] = Follower::Join('users', 'users.user_id', '=', 'followers.from_user_id')->select('from_user_id as user_id','is_follow','users.*')->where('to_user_id' , $userId)->where('is_follow' , 1)->paginate(12);
        $data['userDetails'] = User::where('user_id','=',$userId)->first();
        if ($request->ajax()) {
            return view('front/dashboard/followersLoads',$data)->render(); 
        }
        return view('front/dashboard/followers',$data);
    }
    
    public function following(Request $request){
        $data['title'] = 'FOLLWOING DETAILS';
        $data['description'] = '';
        $data['class'] = 'following';
        $userId = session('user_id');
        $data['following'] = Follower::Join('users', 'users.user_id', '=', 'followers.to_user_id')->select('to_user_id as user_id','is_follow','users.*')->where('from_user_id' , $userId)->where('is_follow' , 1)->paginate(12);
        $data['userDetails'] = User::where('user_id','=',$userId)->first();
        if ($request->ajax()) {
            return view('front/dashboard/followingLoads',$data)->render(); 
        }
        return view('front/dashboard/following',$data);
        
    }
    
    public function address(Request $request){
        $data['title'] = 'Manage Address';
        $data['description'] = '';
        $data['class'] = 'address';
        $userId = session('user_id');
        $data['address'] = UserAddress::join('areas', 'areas.id', '=', 'user_address.area_id' , 'left')->where('user_id','=',$userId)->orderby('address_id' , 'DESC')->paginate(10);
        $data['areas'] = Area::where('status' , 1)->orderby('name' , 'ASC')->get(); 
        if ($request->ajax()) {
            return view('front/dashboard/addressLoad',$data)->render(); 
        }
        return view('front/dashboard/userAddress',$data);
    }

    public function invitePeaple(Request $request){
        $data['amount'] = Setting::first()->referral_amount;
        $data['title'] = 'Invite your Friends and get Referral Amount in your Wallet';
        $data['description'] = 'When you refer a friend and invite them to sign up with Pricepally, you earn a referral amount in your wallet. Are you excited to get '.$data['amount'].'₦ right now!';
        $data['class'] = 'invite';
        $userId = session('user_id');
        
        $data['userDetails'] = User::where('user_id','=',$userId)->first();
        return view('front/dashboard/invitePeaple',$data);
    }
    
    public function wallet(Request $request){
        $data['title'] = 'Wallet';
        $data['description'] = '';
        $data['class'] = 'wallet';
        $user_id = session('user_id');
        $data['transactions'] = DB::table('transactions')
                                ->select('transactions.*','users.first_name','users.last_name','users.email')
                                ->join('users', 'users.user_id', '=', 'transactions.from_user_id' , 'left')
                                ->where('transactions.to_user_id' , $user_id)
                                ->groupby('transactions.created_at')
                                ->get();
        //dd($data['transactions']);
        $available_balance = DB::table('transactions')->where('to_user_id' , $user_id)->whereIn('trans_type' , array(0,2))->where('status' , 1)->sum('amount');
        $withdrawal_balance = DB::table('transactions')->where('to_user_id' , $user_id)->where('trans_type' , 1)->sum('amount');
        $data['available_wallet_amount'] = number_format($available_balance - $withdrawal_balance, 2, '.', '');
        $pd_balance = DB::table('transactions')->where('to_user_id' , $user_id)->where('trans_type' , 0)->where('status' , 0)->sum('amount');
        $data['pending_wallet_amount'] = number_format($pd_balance, 2, '.', '');
        $data['userDetails'] = User::where('user_id','=',$user_id)->first();
        return view('front/dashboard/wallet',$data);
    }

    public function saveAddress(Request $request)
    {
        $is_product = 1;
        $data['is_product'] = $is_product;
        $userId = session('user_id');
        if ($request->addressId != 0) {
            UserAddress::where('address_id', $request->addressId)
                ->update([
                    'lable' => $request->lable,
                    'house_name' =>$request->house_name,
                    'street'=>$request->street,
                    'town'=>$request->town,
                    'phone_number'=>$request->phone_number,
                    'country_code'=>$request->country_code,
                    'area_id'=>$request->area_id
                ]);
            $data['address'] = UserAddress::join('areas', 'areas.id', '=', 'user_address.area_id')->where('user_id','=',$userId)->orderby('address_id' , 'DESC')->paginate(10);
            if ($request->ajax()) {
                return view('front/dashboard/addressLoad',$data)->render(); 
            }
        }else{
            
            $address = new UserAddress();
            $address->lable = $request->lable;
            $address->user_id = $userId;
            $address->house_name = $request->house_name;
            $address->street = $request->street;
            $address->town = $request->town;
            $address->phone_number = $request->phone_number;
            $address->country_code = $request->country_code;
            $address->area_id = $request->area_id;
            $address->address_type = $request->address_type;
            $address->save();
            if($request->type == 'add'){
                $data['address'] = UserAddress::join('areas', 'areas.id', '=', 'user_address.area_id')->where('user_id','=',$userId)->orderby('address_id' , 'DESC')->paginate(10);
                if ($request->ajax()) {
                    return view('front/dashboard/addressLoad',$data)->render(); 
                }
            }else{
                $items = CartItem::Join('products', 'products.product_id', '=', 'cart_items.type_id','left')->Join('service_categories', 'service_categories.service_category_id', '=', 'cart_items.type_id','left')
                            ->select('cart_items.*' , 'products.product_title','products.product_images','service_categories.title','service_categories.image')
                            ->where('cart_items.user_id' , session('user_id'))
                            ->get();
                $subTotal = 0;
                $weight = 0;
                $total = 0;
                if(count($items) > 0){
                    foreach($items as $row){
                        $price = $row->qty * $row->price;
                        $weight = $weight + $row->weight;
                        $subTotal = $subTotal + $price;
                    }
                }
                $data['totalAmount'] =  $subTotal;
                $address = UserAddress::select('user_address.*','user_address.*','areas_inside.title as areasInside','areas_inside.id as areasInsideID','areas_zone_areas.title as areasOutside','areas_zone_areas.id as areasOutsideID','area_zone_countries.title as areasNigaria','area_zone_countries.title as areasNigariaID')->Join('areas_inside', 'user_address.area_id', '=', 'areas_inside.id','left')->Join('areas_zone_areas', 'user_address.area_id', '=', 'areas_zone_areas.id','left')->Join('area_zone_countries', 'user_address.area_id', '=', 'area_zone_countries.id','left')->where('user_id' , session('user_id'))->get(); 
                $data['address'] = $address;
                if(count($address) > 0){

                    $shipping_cost = commonHelper::GetShippingCost($address[0]->address_id , $address[0]->address_type , $address[0]->area_id, $weight);
                    $data['address_id'] =  $address[0]->address_id;
                }else{
                    $shipping_cost = 0;
                    $data['address_id'] = 0;
                }
                $total = $subTotal + $shipping_cost;
                $data['shipping_cost'] =  $shipping_cost;
                $data['total'] = $total;
                $data['weight'] =  $weight;
                if ($request->ajax()) {
                    return view('front.checkout.addressLoad',$data)->render(); 
                }
            }
        }
    }
    
    public function editaddress(Request $request){
        $userId = session('user_id');
        if ($request->addressId) {
            $edit = Address::where('id', $request->addressId)->update(['lable' => $request->lable,'user_id' =>$request->user_id,'house_name' =>$request->house_name,'street'=>$request->street,'town'=>$request->town,'country'=>$request->country,'area_id'=>$request->area_id,'postcode'=>$request->postcode]);
            if ($edit) {
                $data['userAddress'] = Address::where('user_id','=',$userId)->get();
                return view('front/dashboard/useraddress',$data);
            }
        }
    }
    
    public function addressdelete(Request $request, $id){
         $userId = session('user_id');
        $delete = Address::where('id' , $id)->delete();
        if ($delete) {
            $data['userAddress'] = Address::where('user_id','=',$userId)->get();
            return view('front/dashboard/useraddress',$data);
        }
    }
    
    public function getaddress(Request $request, $id){
    	return $address = Address::where('id',$id)->get();
    }
    
    public function logout(Request $request) {

        $request->session()->flush();
        return redirect('/');
    }
}
