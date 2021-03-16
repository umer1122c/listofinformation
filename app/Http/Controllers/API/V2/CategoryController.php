<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use URL;

class CategoryController extends Controller
{
    public function ProductCategory(Request $request){
        try{
            $validator = Validator::make(request()->all(), [
                'offset' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $offset = request()->offset + 20;
            $categories = Category::select('id as category_id','category_type','title as category_name','slug as category_slug','description as category_description','image as category_image','status as category_status')
                            ->where('parent_id' , 0)
                            ->where('status' , 0)
                            ->orderBy('updated_at' , 'DESC')
                            ->skip(request()->offset)->take(20)
                            ->get();
            //return count($trims);
            $categoriesArray = [];
            if(count($categories) > 0){
                foreach($categories as $row){
                    $row->category_image = !empty($row->category_image) ? url("/")."/categories/".$row->category_image : "";
                    $row->banner_image = !empty($row->banner_image) ? url("/")."/categories/".$row->banner_image : "";
                    $sub_categories = Category::select('id as sub_category_id','parent_id as category_id','title as sub_category_name','slug as sub_category_slug','description as sub_category_description','image as sub_category_image','status as category_status')
                                        ->where('parent_id' , $row->category_id)
                                        ->where('status' , 0)
                                        ->orderBy('updated_at' , 'DESC')
                                        ->get();
                    if(count($sub_categories) > 0){
                        $subCatArray = [];
                        foreach($sub_categories as $sub_row){
                            $sub_row->category_name = Category::where('id' , $sub_row->category_id)->first()->title;
                            $sub_row->image = !empty($sub_row->image) ? url("/")."/".$sub_row->image : "";
                            $sub_row->banner_image = !empty($sub_row->banner_image) ? url("/categories/")."/categories/".$sub_row->banner_image : "";
                            $subCatArray[] = $sub_row;
                        }
                    }else{
                        $subCatArray = [];
                    }
                    //$row->sub_categories = $subCatArray;
                    $categoriesArray[] = $row;
                }
                return ['status'=>true,"message"=>'categories data found.',"user_access"=>1,"offset"=>$offset,'categories' => $categoriesArray];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>1,"offset"=>$offset,'categories' => $categoriesArray];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
    
    public function GetSubCategories(Request $request){
        try{
            $validator = Validator::make(request()->all(), [
                'category_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $subCategories = Category::select('id as sub_category_id','parent_id as category_id','title as sub_category_name','slug as sub_category_slug','description as sub_category_description','image as sub_category_image','status as category_status')
                            ->where('parent_id' , request()->category_id)
                            ->where('status' , 0)
                            ->orderBy('updated_at' , 'DESC')
                            ->get();
            //return count($trims);
            $subCategoriesArray = [];
            if(count($subCategories) > 0){
                foreach($subCategories as $row){
                    $row->category_name = Category::where('id' , $row->category_id)->first()->title;
                    $row->sub_category_image = !empty($row->sub_category_image) ? url("/")."/categories/".$row->sub_category_image : "";
                    //$row->banner_image = !empty($row->banner_image) ? url("/")."/".$row->banner_image : "";
//                    $sub_categories = Category::where('parent_id' , $row->id)->where('status' , 0)->orderBy('updated_at' , 'DESC')->get();
//                    if(count($sub_categories) > 0){
//                        $subCatArray = [];
//                        foreach($sub_categories as $sub_row){
//                            $sub_row->image = !empty($sub_row->image) ? url("/")."/".$sub_row->image : "";
//                            $sub_row->banner_image = !empty($sub_row->banner_image) ? url("/")."/".$sub_row->banner_image : "";
//                            $subCatArray[] = $sub_row;
//                        }
//                    }else{
//                        $subCatArray = [];
//                    }
//                    $row->sub_categories = $subCatArray;
                    $subCategoriesArray[] = $row;
                }
                return ['status'=>true,"message"=>'categories data found.',"user_access"=>1,'categories' => $subCategoriesArray];
            }else{
                return ['status'=>true,"message"=>'No data found.',"user_access"=>1,'categories' => $subCategoriesArray];
            }
        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
}
