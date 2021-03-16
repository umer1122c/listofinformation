<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;

use App\Models\Area;
use App\Models\CartItem;
use App\Models\Copoun;
use App\Models\CouponUser;
use App\Models\Product;
use App\Models\CartServiceItem;
use App\Models\ServiceCategory;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class CartController extends Controller
{
    public function GetCartItems(Request $request)
    {
        try{
            $user = $request->user();
            $prodImageUrl = URL::to('').'/products/';
            $cartItemArray = [];
            $cartItems = CartItem::select('cart_items.cart_id','cart_items.type_id as item_id','cart_items.name as item_name','cart_items.qty','cart_items.price as item_cost','cart_items.type','cart_items.weight')
                        ->where('user_id' , $user->user_id)
                        ->orderBy('cart_items.id','DESC')
                        ->get();
            //dd($cartItems);
            if(count($cartItems) > 0){
                foreach($cartItems as $row){
                    if($row->type == 'Product'){
                        $product = Product::where('product_id' , $row->item_id)->first();
                        if($product){
                            $product_images = json_decode($product->product_images);
                            $row->item_images = $prodImageUrl.$product_images[0]->imagePath;
                        }
                        $row->service_list = [];
                    }else{
                        $serviceCate = ServiceCategory::where('service_category_id' , $row->item_id)->first();
                        if($serviceCate){
                            $row->item_images = URL::to('').'/service_categories/'.$serviceCate->image;
                        }
                        $servicesItem = CartServiceItem::where('cart_id' , $row->cart_id)->get();
                        if(count($servicesItem) > 0){
                            $row->service_list = $servicesItem;
                        }else{
                            $row->service_list = [];
                        }
                    }
                    $cartItemArray[] = $row;
                }
                return ["status" => true,"message"=>"Cart data found.","user_access"=>request()->user()->user_access,"product_list"=>$cartItemArray];
            }else{
                return ["status" => true,"message"=>"Cart data found.","user_access"=>request()->user()->user_access,"product_list"=>$cartItemArray];
            }
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function AddProductToCart(Request $request)
    {
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'product_id' => 'required',
                'product_name' => 'required',
                'price' => 'required',
                'qty' => 'required',
                'type' => 'required',
                'weight' => 'required'
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $cart_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);
            $data = [
                "type_id"  => request()->product_id,
                "cart_id"  => $cart_id,
                "user_id"  => $user->user_id,
                "name"  => request()->product_name,
                "price"  => request()->price,
                "type"  => request()->type,
                "qty"  => request()->qty,
                "weight"  => request()->weight,
                "created_at"  => date('Y-m-d h:i:s'),
                "updated_at"  => date('Y-m-d h:i:s'),
            ];
            $itemArr = (object) array();
            $itemArr->cart_id = $cart_id;
            $itemArr->product_id = request()->product_id;
            $itemArr->type = request()->type;
            $itemArr->weight = request()->weight;
            $cartItem = CartItem::where('user_id' , $user->user_id)->where('type_id' , request()->product_id)->where('type','Product')->first();
            if($cartItem){
                $qty = $cartItem->qty + request()->qty;
                CartItem::where('user_id' , $user->user_id)->where('type_id' , request()->product_id)->update(['qty'=>$qty]);
                return ["status" => true,"message"=>"Item added into cart successfully.","user_access"=>request()->user()->user_access,"Cart_details"=>$itemArr];
            }else{
                CartItem::insert($data);
                return ["status" => true,"message"=>"Item added into cart successfully.","user_access"=>request()->user()->user_access,"Cart_details"=>$itemArr];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function AddServiceToCart(Request $request)
    {
        try{
            $user = $request->user();
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            
            $cart_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);
            
            $serviceArrayNew = [];
            $serviceArray = $jsonrequest['service_list'];
            $cartItem = CartItem::where('user_id' , $user->user_id)->where('type_id' , $jsonrequest['ServiceCategoryId'])->where('type','Service')->first();
            if($cartItem){
                $cartItem->cart_id;
                CartServiceItem::where('cart_id',$cartItem->cart_id)->delete();
                CartItem::where('user_id' , $user->user_id)->where('type_id' , $jsonrequest['ServiceCategoryId'])->where('type','Service')->delete();
            }
            $price = 0;
            if(count($serviceArray) > 0){
                foreach($serviceArray as $row){
                    $serviceArrayNew[] = [
                        "cart_id"  => $cart_id,
                        "service_id"  => $row['serviceId'],
                        "service_name"  => $row['serviceName'],
                        "price"  => $row['price']
                    ];
                    $price = $price + $row['price'];
                }
            }
            //dd($serviceArrayNew);
            $data = [
                "type_id"  => $jsonrequest['ServiceCategoryId'],
                "cart_id"  => $cart_id,
                "user_id"  => $user->user_id,
                "name"  => $jsonrequest['ServiceCategoryName'],
                "price"  => $price,
                "type"  => $jsonrequest['type'],
                "qty"  => 1,
                "weight"  => 0,
                "created_at"  => date('Y-m-d h:i:s'),
                "updated_at"  => date('Y-m-d h:i:s'),
            ];
            $itemArr = (object) array();
            $itemArr->cart_id = $cart_id;
            $itemArr->ServiceCategoryId = $jsonrequest['ServiceCategoryId'];
            $itemArr->type = $jsonrequest['type'];
            $itemArr->service_list = $serviceArrayNew;
            $cartItem = CartItem::where('user_id' , $user->user_id)->where('type_id' , $jsonrequest['ServiceCategoryId'])->where('type','Service')->first();
            
            CartItem::insert($data);
            CartServiceItem::insert($serviceArrayNew);
            return ["status" => true,"message"=>"Item added into cart successfully.","user_access"=>request()->user()->user_access,"Cart_details"=>$itemArr];
            
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function UpdateCartItem(Request $request)
    {
        try{
            $user = $request->user();
            $prodImageUrl = URL::to('').'/products/';
            $validator = Validator::make(request()->all(), [
                'cart_id' => 'required',
                'qty' => 'required',
            ]);
            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all())];
            }//..... end if() .....//
            
            CartItem::where('cart_id' , request()->cart_id)->update(['qty'=>request()->qty]);
            
            $cartItemArray = [];
            $cartItems = CartItem::select('cart_items.cart_id','cart_items.type_id as item_id','cart_items.name as item_name','cart_items.qty','cart_items.price as item_cost','cart_items.type')
                        ->where('user_id' , $user->user_id)
                        ->orderBy('cart_items.id','DESC')
                        ->get();
            //dd($cartItems);
            if(count($cartItems) > 0){
                foreach($cartItems as $row){
                    if($row->type == 'Product'){
                        $product = Product::where('product_id' , $row->item_id)->first();
                        if($product){
                            $product_images = json_decode($product->product_images);
                            $row->item_images = $prodImageUrl.$product_images[0]->imagePath;
                        }
                        $row->service_list = [];
                    }else{
                        $serviceCate = ServiceCategory::where('service_category_id' , $row->item_id)->first();
                        if($serviceCate){
                            $row->item_images = URL::to('').'/service_categories/'.$serviceCate->image;
                        }
                        $servicesItem = CartServiceItem::where('cart_id' , $row->cart_id)->get();
                        if(count($servicesItem) > 0){
                            $row->service_list = $servicesItem;
                        }else{
                            $row->service_list = [];
                        }
                    }
                    $cartItemArray[] = $row;
                }
            }
            return ["status" => true,"message"=>"Shopping cart has been updated successfully.","user_access"=>request()->user()->user_access,"Cart_details"=>$cartItemArray];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function ValidateCart(Request $request)
    {
        try{
            $user = $request->user();
            $jsonrequest = json_decode(file_get_contents('php://input'), true);
            $prodImageUrl = URL::to('').'/products/';
            
            $is_update = 0;
            $normalProductsArray =  CartItem::where('user_id' , $user->user_id)->where('type' , 'normal')->get();
            if(count($normalProductsArray) > 0){
                foreach ($normalProductsArray as $normal) {
                    $product_id = $normal->product_id;
                    $product = Product::where('product_id' , $product_id)->where('status' , 0)->first();
                    //dd($product);
                    if($product){
                        CartItem::where('product_id' , $product_id)->where('user_id' , $user->user_id)->where('pally_id' , 0)->delete();
                        $is_update = 1;
                    }
                    
                }
            }
            $pallyProductsArray =  CartItem::where('user_id' , $user->user_id)->where('type' , 'Open')->get();
            if(count($pallyProductsArray) > 0){
                foreach ($pallyProductsArray as $pally) {
                    $pally_id = $pally->pally_id;
                    $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally_id)->first();
                    //dd($open_pallys);
                    if($open_pallys){
                        if($open_pallys->pally_type == 'Open'){
                            $cartItemCount = CartItem::where('pally_id' , $pally_id)->where('user_id' , $user->user_id)->where('type',$open_pallys->pally_type)->first();
                            if($cartItemCount){
                                $cartItemCount = $cartItemCount->qty;
                                $pally_count = $open_pallys->number_of_person - $open_pallys->pally_count;
                                if($cartItemCount > $pally_count){
                                    //echo $cartItemCount;exit;
                                    if($cartItemCount == 1){
                                        CartItem::where('pally_id' , $pally_id)->where('user_id' , $user->user_id)->delete();
                                    }else{
                                        if($cartItemCount  > $pally_count){
                                            $remaning = $pally_count;
                                        }else{
                                            $remaning = $cartItemCount;
                                        }
                                        if($remaning > 0){
                                            CartItem::where('pally_id' , $pally_id)->where('user_id' , $user->user_id)->update(['qty'=> $remaning]);
                                        }else{
                                            CartItem::where('pally_id' , $pally_id)->where('user_id' , $user->user_id)->delete();
                                        }
                                        
                                    }
                                    $is_update = 1;
                                }
                            }else{
                                
                            }
                        }
                    }
                }
            }
            //return $is_update;
            $cartItemArray = [];
            if($is_update == 1){
                $cartItems = CartItem::join('products' , 'products.product_id','=','cart_items.product_id')
                                ->select('cart_items.cart_id as cart_itemid','cart_items.product_id','cart_items.pally_id','cart_items.qty','cart_items.price as item_cost','products.cat_id as category_id','categories.title as category_name','products.product_title as product_name','products.product_images','products.pally_size as pally_people_count','cart_items.attribute_id','cart_items.attribute_cost','cart_items.attribute_name')
                                ->join('categories' , 'products.cat_id','=','categories.id')
                                ->where('user_id' , $user->user_id)
                                ->orderBy('cart_items.id','DESC')
                                ->get();
                if(count($cartItems) > 0){
                    foreach($cartItems as $row){
                        $pally_id = $row->pally_id;
                        if($pally_id == '0'){
                            $row->type = 'normal';
                            $row->pally_people_count = 0;
                            $row->pally_link = '';
                        }else{
                            $slug = Product::where('product_id' , $row->product_id)->first()->slug;
                            $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally_id)->first();
                            $row->type = $open_pallys->pally_type;
                            $row->pally_people_count = $open_pallys->number_of_person;
                            $row->pally_link = URL::to('shop/pally/detail/'.$slug.'/'.$row->product_id.'/'.$pally_id);
                        }
                        $product_images = json_decode($row->product_images);
                        $row->product_images = $prodImageUrl.$product_images[0]->imagePath;

                        $cartItemArray[] = $row;
                    }
                    return ["status" => true,"message"=>"Cart data found.","user_access"=>request()->user()->user_access,'is_update' => $is_update,"product_list"=>$cartItemArray];
                }else{
                    return ["status" => true,"message"=>"Cart data found.","user_access"=>request()->user()->user_access,'is_update' => $is_update,"product_list"=>$cartItemArray];
                }
            }else{
                $cartItems = CartItem::join('products' , 'products.product_id','=','cart_items.product_id')
                                ->select('cart_items.cart_id as cart_itemid','cart_items.product_id','cart_items.pally_id','cart_items.qty','cart_items.price as item_cost','products.cat_id as category_id','categories.title as category_name','products.product_title as product_name','products.product_images','products.pally_size as pally_people_count','cart_items.attribute_id','cart_items.attribute_cost','cart_items.attribute_name')
                                ->join('categories' , 'products.cat_id','=','categories.id')
                                ->where('user_id' , $user->user_id)
                                ->orderBy('cart_items.id','DESC')
                                ->get();
                if(count($cartItems) > 0){
                    foreach($cartItems as $row){
                        $pally_id = $row->pally_id;
                        if($pally_id == '0'){
                            $row->type = 'normal';
                            $row->pally_people_count = 0;
                            $row->pally_link = '';
                        }else{
                            $slug = Product::where('product_id' , $row->product_id)->first()->slug;
                            $open_pallys = DB::table('open_pallys')->where('pally_id' , $pally_id)->first();
                            $row->type = $open_pallys->pally_type;
                            $row->pally_people_count = $open_pallys->number_of_person;
                            $row->pally_link = URL::to('shop/pally/detail/'.$slug.'/'.$row->product_id.'/'.$pally_id);
                        }
                        $product_images = json_decode($row->product_images);
                        $row->product_images = $prodImageUrl.$product_images[0]->imagePath;

                        $cartItemArray[] = $row;
                    }
                    return ["status" => true,"message"=>"Cart data found.","user_access"=>request()->user()->user_access,'is_update' => $is_update,"product_list"=>$cartItemArray];
                }else{
                    return ["status" => true,"message"=>"Cart data found.","user_access"=>request()->user()->user_access,'is_update' => $is_update,"product_list"=>$cartItemArray];
                }
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>0];
        }
    }
    
    public function DeleteCartItem(Request $request){
        try{
            $user = $request->user();
            $prodImageUrl = URL::to('').'/products/';
            $validator = Validator::make(request()->all(), [
                'cart_itemid' => 'required'
            ]);
            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            CartItem::where('cart_id' , request()->cart_itemid)->delete();
            CartServiceItem::where('cart_id' , request()->cart_itemid)->delete();
            $cartItemArray = [];
            $cartItems = CartItem::select('cart_items.cart_id','cart_items.type_id as item_id','cart_items.name as item_name','cart_items.qty','cart_items.price as item_cost','cart_items.type')
                        ->where('user_id' , $user->user_id)
                        ->orderBy('cart_items.id','DESC')
                        ->get();
            //dd($cartItems);
            if(count($cartItems) > 0){
                foreach($cartItems as $row){
                    if($row->type == 'Product'){
                        $product = Product::where('product_id' , $row->item_id)->first();
                        if($product){
                            $product_images = json_decode($product->product_images);
                            $row->item_images = $prodImageUrl.$product_images[0]->imagePath;
                        }
                        $row->service_list = [];
                    }else{
                        $serviceCate = ServiceCategory::where('service_category_id' , $row->item_id)->first();
                        if($serviceCate){
                            $row->item_images = URL::to('').'/service_categories/'.$serviceCate->image;
                        }
                        $servicesItem = CartServiceItem::where('cart_id' , $row->cart_id)->get();
                        if(count($servicesItem) > 0){
                            $row->service_list = $servicesItem;
                        }else{
                            $row->service_list = [];
                        }
                    }
                    $cartItemArray[] = $row;
                }
            }
            return ["status" => true,"message"=>"Deleted cart item successfully.","user_access"=>request()->user()->user_access,"product_list"=>$cartItemArray];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function ValidationCoupon(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'coupon_code' => 'required'
            ]);
            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all())];
            }//..... end if() .....//
            $Coupon = Copoun::select('id as coupon_id', 'code as coupon_code','discount as coupon_discount','min_price as min_cart_total','no_of_time','max_price_applay as max_amount')->where('code',request()->coupon_code)->where('status',0)->first();
            
            if($Coupon){
                $userCount = CouponUser::where('user_id' , $user->user_id)->where('coupon_code',request()->coupon_code)->count();
                //dd($userCount);
                if($userCount < $Coupon->no_of_time){
                    return ["status" => true,"message"=>"Coupon Applied successfully.","user_access"=>request()->user()->user_access,"Cart_details"=>$Coupon];
                }else{
                    return ['status' => false, 'message' => 'Coupon code hass been expaired for this user.',"user_access"=>request()->user()->user_access];
                }
            }else{
                return ['status' => false, 'message' => 'No coupon code found.',"user_access"=>request()->user()->user_access];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function ValidateWalletMoney(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'amount' => 'required'
            ]);
            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all())];
            }//..... end if() .....//
             $available_balance = DB::table('transactions')->where('to_user_id' , $user->user_id)->whereIn('trans_type' , array(0,2))->where('status' , 1)->sum('amount');
            $withdrawal_balance = DB::table('transactions')->where('to_user_id' , $user->user_id)->where('trans_type' , 1)->sum('amount');
            $wallet = $available_balance - $withdrawal_balance;
            if(request()->amount < $wallet){
                return ["status" => true,"message"=>"Applied amount."];
            }else{
                return ['status' => false, 'message' => 'You entered insufficient amount.',"user_access"=>request()->user()->user_access];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetWalletAmount(Request $request){
        try{
            $user = $request->user();
            //return $user->user_id;
            $transactionsArray = [];
            $data = (object) array();
            $available_balance = DB::table('transactions')->where('to_user_id' , $user->user_id)->whereIn('trans_type' , array(0,2))->where('status' , 1)->sum('amount');
            $withdrawal_balance = DB::table('transactions')->where('to_user_id' , $user->user_id)->where('trans_type' , 1)->sum('amount');
            $data->available_wallet_amount = number_format($available_balance - $withdrawal_balance, 2, '.', '');
            $pd_balance = DB::table('transactions')->where('to_user_id' , $user->user_id)->where('trans_type' , 0)->where('status' , 0)->sum('amount');
            $data->pending_wallet_amount = number_format($pd_balance, 2, '.', '');
            $wallet = Wallet::where('user_id',$user->user_id)->first();
            if($wallet){
                $transactions = DB::table('transactions')
                                    ->select('transactions.transaction_id','transactions.trans_type as transaction_type','transactions.cash_flow_type as refferal_type','transactions.created_at','transactions.amount','users.user_id','users.first_name','users.last_name')
                                    ->join('users', 'users.user_id', '=', 'transactions.from_user_id' , 'left')
                                    ->where('transactions.to_user_id' , $user->user_id)
                                    ->get();
                if(count($transactions) > 0){
                    foreach($transactions as $row){
                        $transactionsArray[] = $row;
                    }
                }
                return ['status' => true, 'message' => 'Data found.',"user_access"=>request()->user()->user_access, 'wallet_detail' => $data,'transactions'=>$transactionsArray ];
            }else{
                return ['status' => true, 'message' => 'Data found.' ,"user_access"=>request()->user()->user_access, 'wallet_detail' => $data,'transactions'=>$transactionsArray];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function getUserTransactions($date = '' , $user_id = ''){
        $transactions = DB::table('transactions')
                                    ->select('transactions.*','users.first_name','users.last_name','users.email')
                                    ->join('users', 'users.user_id', '=', 'transactions.from_user_id' , 'left')
                                    ->where('transactions.to_user_id' , $user_id)
                                    ->where('transactions.created_at' , $date)
                                    ->get();
        return $transactions;
    }
}