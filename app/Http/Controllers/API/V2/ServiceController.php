<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use URL;

class ServiceController extends Controller
{
    public function ServiceCategory(Request $request){
        try{
            $validator = Validator::make(request()->all(), [
                'offset' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $offset = request()->offset + 20;
            $services = ServiceCategory::select('service_category_id as ServiceCategoryId','title as service_name','slug as service_slug','description as service_description','image as service_image','status as service_status')
                            ->where('status' , 0)
                            ->orderBy('updated_at' , 'DESC')
                            ->skip(request()->offset)->take(20)
                            ->get();
            //return count($trims);
            $serviceCatArray = [];
            if(count($services) > 0){
                foreach($services as $row){
                    $row->service_image = !empty($row->service_image) ? url("/")."/service_categories/".$row->service_image : "";
                    $serviceCatArray[] = $row;
                }
                return ['status'=>true,"message"=>'services categories data found.',"user_access"=>1,"offset"=>$offset,'service_catgory' => $serviceCatArray];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>1,"offset"=>$offset,'service_catgory' => $serviceCatArray];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function ServiceList(Request $request){
        try{
            $validator = Validator::make(request()->all(), [
                'ServiceCategoryId' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $services = Service::select('id','service_category_id as ServiceCategoryId','service_name','description','price')
                            ->where('service_category_id' , request()->ServiceCategoryId)
                            ->where('status' , 1)
                            ->orderBy('updated_at' , 'DESC')
                            ->get();
            //return count($trims);
            $serviceArray = [];
            if(count($services) > 0){
                foreach($services as $row){
                    $serviceArray[] = $row;
                }
                return ['status'=>true,"message"=>'services data found.',"user_access"=>1,'services' => $serviceArray];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>1,'services' => $serviceArray];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetTopServices(Request $request){
        try{
            
            $services = ServiceCategory::select('service_category_id as ServiceCategoryId','title as service_name','slug as service_slug','description as service_description','image as service_image','status as service_status')
                            ->where('status' , 0)
                            ->orderBy('updated_at' , 'DESC')
                            ->skip(request()->offset)->take(10)
                            ->get();
            //return count($trims);
            $serviceCatArray = [];
            if(count($services) > 0){
                foreach($services as $row){
                    $row->service_image = !empty($row->service_image) ? url("/")."/service_categories/".$row->service_image : "";
                    $serviceCatArray[] = $row;
                }
                return ['status'=>true,"message"=>'services categories data found.',"user_access"=>1,'top_services' => $serviceCatArray];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>1,'top_services' => $serviceCatArray];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
}
