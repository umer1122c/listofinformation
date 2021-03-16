<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Models\MediaCategory;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use URL;

class MediaController extends Controller
{
    public function GetMediaServices(Request $request){
        try{
            $medias = MediaCategory::select('id as serviceId','title as serviceName','image as serviceImage')
                            ->where('status' , 0)
                            ->orderBy('updated_at' , 'DESC')
                            ->get();
            //return count($trims);
            $mediasArray = [];
            if(count($medias) > 0){
                foreach($medias as $row){
                    $row->serviceImage = !empty($row->serviceImage) ? url("/")."/medias/media_categories/".$row->serviceImage : "";
                    $mediasArray[] = $row;
                }
                return ['status'=>true,"message"=>'medias data found.',"user_access"=>1,'medias' => $mediasArray];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>1,'medias' => $mediasArray];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetMediaImages(Request $request){
        try{
            $validator = Validator::make(request()->all(), [
                'offset' => 'required',
                'serviceId' => 'required'
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $offset = request()->offset + 20;
            $medias = Media::select('id','image','created_at')
                            ->where('cat_id' , request()->serviceId)
                            ->where('status' , 1)
                            ->orderBy('updated_at' , 'DESC')
                            ->skip(request()->offset)->take(20)
                            ->get();
            //return count($trims);
            $mediasArray = [];
            if(count($medias) > 0){
                foreach($medias as $row){
                    $row->image = !empty($row->image) ? url("/")."/medias/".$row->image : "";
                    $mediasArray[] = $row;
                }
                return ['status'=>true,"message"=>'medias data found.',"user_access"=>1,"offset"=>$offset,'medias' => $mediasArray];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>1,"offset"=>$offset,'medias' => $mediasArray];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
}
