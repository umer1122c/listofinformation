<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\CartServiceItem;
use App\Models\Service;
use App\Models\Product;
use App\Models\Copoun;
use App\Models\ServiceCategory;
use App\Models\UsedCoupon;
use App\Models\Attribute;
use App\Models\OrderAddress;
use App\Classes\CommonLibrary;
use commonHelper;
use App\Models\User;
use Helper;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use File;
use DB;
use URL;
use Cart;

class CartController extends Controller
{
    public function index(){
        $data['title'] = 'Cart';
        $data['class'] = 'cart';
        $data['prodImageUrl'] = URL::to('').'/products/';
        $data['serviceImageUrl'] = URL::to('').'/service_categories/';
        
        return view('front/cart/index',$data);
    }
    
    public function addToCartSession(Request $request) { 
        
        $cart_id = substr(str_shuffle('123456789123456789123456789321654987'),0,8);
        $modifiers = array('cart_id' => $cart_id);
        $parmArray['id'] = $request->id;
        $parmArray['name'] = $request->name;
        $parmArray['qty'] = $request->qty;
        $parmArray['price'] = $request->price;
        $parmArray['options'] = $modifiers;
        Cart::add($parmArray);
        
        return response()->json(['status'=>"success","message"=>'Item added into cart successfully.','cartCount' => count(Cart::content()->groupBy('id')),'cartTotal' => Cart::subtotal()],200);
        
    }
    
    public function updateItem($id = '' , $qty){
        $cart =  CartItem::where('cart_id', $id)->update(['qty' => $qty]);
        if($cart){
            $data = CartItem::select('price','qty')->where('user_id',session('user_id'))->get();
            $total = 0;
            foreach ($data as $key => $value) {
                $total += $value->price*$value->qty;
            }
            $cartCount = CartItem::where('user_id',session('user_id'))->count();
            return response()->json(['status'=>"success","message"=>'Item quantity updated in cart successfully.','cartCount' => $cartCount,'total'=> number_format($total,2)],200);
        }else{
            return response()->json(['status'=>"fail","message"=>'Error accer during cart item deleting.'],200);
        }
    }
    
    public function updateCartQty(Request $request){
        $cartItem = Cart::where('user_id',session('user_id'))->where('product_id',commonHelper::getProductId($request->slug))->first();
        if($cartItem){
            if ($request->qty =='plus') {
                $qty = $cartItem->product_quentity +$request->product_quentity;
                Cart::where('user_id' ,session('user_id'))->where('product_id' , commonHelper::getProductId($request->slug))->update(['product_quentity'=>$qty]);
                $data = Cart::where('user_id',session('user_id'))->count();
                if($data == null){
                        return 0;
                    }else{
                        return $data;
                    }
            }else{
                $qty = $cartItem->product_quentity - $request->product_quentity;
                if ($qty == 0) {
                    Cart::where('user_id' , session('user_id'))->where('product_id' , commonHelper::getProductId($request->slug))->delete();
                    $data = Cart::where('user_id',session('user_id'))->count();
                    if($data == null){
                        return 0;
                    }else{
                        return $data;
                    }
                }else{
                    Cart::where('user_id' , session('user_id'))->where('product_id' , commonHelper::getProductId($request->slug))->update(['product_quentity'=>$qty]);
                    $data = Cart::where('user_id',session('user_id'))->count();
                    if($data == null){
                        return 0;
                    }else{
                        return $data;
                    }
                }
            }

        }

    }
    
    public function deleteCartItemSession($id = ''){
        $cartIds = explode(',' , $id);
        if(count($cartIds) > 0){
            foreach($cartIds as $key => $val){
                Cart::remove($val);
            }
            return response()->json(['status'=>"success","message"=>'Item deleted form cart successfully.','cartCount' => count(Cart::content()->groupBy('id')),'total'=> Cart::subtotal()],200);
        }else{
            return response()->json(['status'=>"fail","message"=>'Error accer during cart item deleting.'],200);
        }
    }
}