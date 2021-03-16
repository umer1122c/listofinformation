<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;

use App\Models\Area;
use App\Models\AreaInside;
use App\Models\AreasZoneArea;
use App\Models\AreasZonePrice;
use App\Models\UserAddress;
use App\Models\AreaZoneCountry;
use App\Models\AreasNgZonePrice;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AddresController extends Controller
{
    public function GetAreas(Request $request){
        try{
            $areas = Area::select('id as area_id','name as area_name','value1','value2','value3')->where('status' , 1)->orderBy('name' , 'ASC')->get();
            if(count($areas) > 0){
                return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"areas"=>$areas];
            }
            return ["status" => true,"message"=>"No data found","user_access"=>request()->user()->user_access,"areas"=>$areas];

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function InsideLagosAreas(Request $request){
        try{
            $areas = AreaInside::select('id as area_id','title as area_name','area_val as price')->where('status' , 1)->orderBy('title' , 'ASC')->get();
            if(count($areas) > 0){
                return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"areas"=>$areas];
            }
            return ["status" => true,"message"=>"No data found","user_access"=>request()->user()->user_access,"areas"=>$areas];

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function OutsideLagosAreas(Request $request){
        try{
            $areas = AreasZoneArea::select('id as area_id','title as area_name')->where('status' , 1)->orderBy('title' , 'ASC')->get();
            if(count($areas) > 0){
                return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"areas"=>$areas];
            }
            return ["status" => true,"message"=>"No data found","user_access"=>request()->user()->user_access,"areas"=>$areas];

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function OutsideNigeriaAreas(Request $request){
        try{
            $areas = AreaZoneCountry::select('id as area_id','title as area_name')->where('status' , 1)->orderBy('title' , 'ASC')->get();
            if(count($areas) > 0){
                return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"areas"=>$areas];
            }
            return ["status" => true,"message"=>"No data found","user_access"=>request()->user()->user_access,"areas"=>$areas];

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }

    public function GetUserAddress(Request $request){
        try{
            $user = $request->user();
            $address = UserAddress::Join('areas_inside', 'user_address.area_id', '=', 'areas_inside.id','left')->Join('areas_zone_areas', 'user_address.area_id', '=', 'areas_zone_areas.id','left')->Join('area_zone_countries', 'user_address.area_id', '=', 'area_zone_countries.id','left')->Join('users', 'users.user_id', '=', 'user_address.user_id')
                            ->select('user_address.address_id','user_address.lable as label','user_address.house_name','user_address.street','user_address.town','user_address.area_id','user_address.phone_number','user_address.country_code','user_address.address_type','users.email')
                            ->where('user_address.user_id' , $user->user_id)
                            ->get();
            if(count($address) > 0){
                return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"address"=>$address];
            }
            return ["status" => true,"message"=>"No data found","user_access"=>request()->user()->user_access,"address"=>$address];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function AddUserAddress(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'label' => 'required',
                'house_name' => 'required',
                'street' => 'required',
                'town' => 'required',
                'area_id' => 'required',
                'country_code' => 'required',
                'phone_number' => 'required',
                'address_type' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $userAddress = new UserAddress();
            $userAddress->user_id = $user->user_id;
            $userAddress->house_name = request()->house_name;
            $userAddress->lable = request()->label;
            $userAddress->street = request()->street;
            $userAddress->town = request()->town;
            $userAddress->area_id = request()->area_id;
            $userAddress->country_code = request()->country_code;
            $userAddress->phone_number = request()->phone_number;
            $userAddress->address_type = request()->address_type;
            $userAddress->save();
            $address_id = $userAddress->address_id;
            $UserAddress = UserAddress::Join('areas_inside', 'user_address.area_id', '=', 'areas_inside.id','left')->Join('areas_zone_areas', 'user_address.area_id', '=', 'areas_zone_areas.id','left')->Join('area_zone_countries', 'user_address.area_id', '=', 'area_zone_countries.id','left')->Join('users', 'users.user_id', '=', 'user_address.user_id')
                                ->select('user_address.address_id','user_address.lable as label','user_address.house_name','user_address.street','user_address.town','user_address.area_id','user_address.phone_number','user_address.country_code','user_address.address_type','users.email')
                                ->where('address_id' , $address_id)
                                ->first();
            return ["status" => true,"message"=>"Address added successfully.","user_access"=>request()->user()->user_access,"address"=>$UserAddress];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }

    public function EditUserAddress(){
        try{
            $validator = Validator::make(request()->all(), [
                'address_id' => 'required',
                'label' => 'required',
                'house_name' => 'required',
                'street' => 'required',
                'town' => 'required',
                'area_id' => 'required',
                'phone_number' => 'required',
                'country_code' => 'required',
                'address_type' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//


            $data = [
                "lable"  => request()->label,
                "house_name"  => request()->house_name,
                "street"  => request()->street,
                "town"  => request()->town,
                "area_id"  => request()->area_id,
                "country_code"  => request()->country_code,
                "phone_number"  => request()->phone_number,
                "address_type" => request()->address_type
            ];
            DB::table("user_address")->where('address_id' , request()->address_id)->update($data);
            $UserAddress = UserAddress::Join('areas_inside', 'user_address.area_id', '=', 'areas_inside.id','left')->Join('areas_zone_areas', 'user_address.area_id', '=', 'areas_zone_areas.id','left')->Join('area_zone_countries', 'user_address.area_id', '=', 'area_zone_countries.id','left')->Join('users', 'users.user_id', '=', 'user_address.user_id')
                                ->select('user_address.address_id','user_address.lable as label','user_address.house_name','user_address.street','user_address.town','user_address.area_id','user_address.phone_number','user_address.country_code','user_address.address_type','users.email')
                                ->where('address_id' , request()->address_id)
                                ->first();
            return ["status" => true,"message"=>"Address updated successfully.","user_access"=>request()->user()->user_access,"address"=>$UserAddress];
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetShippingCost(Request $request){
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'address_id' => 'required',
                'product_weight' => 'required'
            ]);
            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $address = UserAddress::Join('areas_inside', 'user_address.area_id', '=', 'areas_inside.id','left')->Join('areas_zone_areas', 'user_address.area_id', '=', 'areas_zone_areas.id','left')->Join('area_zone_countries', 'user_address.area_id', '=', 'area_zone_countries.id','left')
                                ->Join('users', 'users.user_id', '=', 'user_address.user_id')
                                ->select('user_address.area_id','user_address.address_type')
                                ->where('address_id' , request()->address_id)
                                ->first();
            //dd($address);
            $weight= request()->product_weight;
            $x = explode('.' , $weight);
            $y = $x[1];
            if($y < 5){
                $weight = $x[0] + 0.5;
            }else if($y == 5){
                $weight = $weight;
            }else{
                $weight = $x[0] + 1;
            }
            if($address){
                if($address->address_type == 'Inside'){
                    $areaInside = AreaInside::select('id as area_id','area_val')->where('id' , $address->area_id)->where('status' , 1)->first();
                    if($areaInside){
                        if($weight <= 5){
                            $shipping_cost = $areaInside->area_val;
                        }else{
                            $remaningweight = $weight - 5;
                            $extraCost = $remaningweight * 500;
                            $shipping_cost = $areaInside->area_val + $extraCost;
                        }
                    }else{
                        $shipping_cost = 0;
                    }
                }else if($address->address_typ == 'Outside'){
                    $areaOutside = AreasZoneArea::select('id as area_id','zone_id')->where('id' , $address->area_id)->where('status' , 1)->first();
                    //dd($areaOutside);
                    if($areaOutside){
                        $zone_id = $areaOutside->zone_id;
                        $AreasZonePrice = AreasZonePrice::where('zone_id' , $zone_id)->where('weight' , $weight)->first();
                        if($AreasZonePrice){
                            $shipping_cost = $AreasZonePrice->price;
                        }else{
                            $shipping_cost = 0;
                        }
                    }else{
                        $shipping_cost = 0;
                    }
                }else{
                    
                    $areaCountry = AreaZoneCountry::select('id as area_id','zone_id')->where('id' , $address->area_id)->where('status' , 1)->first();
                    //dd($areaCountry);
                    if($areaCountry){
                        $zone_id = $areaCountry->zone_id;
                        $AreasZonePrice = AreasNgZonePrice::where('zone_id' , $zone_id)->where('weight' , $weight)->first();
                        //dd($AreasZonePrice);
                        if($AreasZonePrice){
                            $shipping_cost = $AreasZonePrice->price;
                        }else{
                            $shipping_cost = 0;
                        }
                    }else{
                        $shipping_cost = 0;
                    }
                }
                return ["status" => true,"message"=>"shipping cost calculated.","user_access"=>request()->user()->user_access,"shipping_amount" => number_format($shipping_cost, 2, '.', '')];
            }else{
                return ['status' => false,"user_access"=>request()->user()->user_access, 'message' => 'Address not found.'];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
}
