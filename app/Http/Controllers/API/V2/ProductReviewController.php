<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class ProductReviewController extends Controller
{
    public function AddProductReview(Request $request)
    {
        try{
            $user = $request->user();
            $validator = Validator::make(request()->all(), [
                'product_id' => 'required',
                'review' => 'required',
                'rating' => 'required',
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//

            $reviews = new Review();
            $reviews->user_id = $user->user_id;
            $reviews->product_id = request()->product_id;
            $reviews->review = request()->review;
            $reviews->rating = request()->rating;
            $reviews->created_at = date('Y-m-d h:i:s');
            $reviews->updated_at = date('Y-m-d h:i:s');
            $reviews->save();
            $review_id = $reviews->id;
            $review = Review::select('id as review_id','review','rating','created_at as review_date')->where('id' , $review_id)->first();
            if($review){
                $review->review_date = date('d-m-Y' , strtotime($review->review_date));
                $users = User::select('user_id','first_name','last_name','user_name','user_image')->where('user_id' , $user->user_id)->first();
                if($users){
                    if($users->user_image == ''){
                        if($users->social_image != ''){
                            $users->user_image = $users->social_image;
                        }else{
                            $users->user_image = URL::to('/front/dummy_round.png');
                        }
                    }else{
                        $users->user_image = URL::to('/') . '/users/'.$users->user_image;
                    }   
                }
                $review->user_info = $users;
            }
            return ["status" => true,"message"=>"User review has been added successfully!","user_access"=>request()->user()->user_access,"reviewDetail"=>$review];

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }

    public function GetProductReviews()
    {
        try{
            $validator = Validator::make(request()->all(), [
                'product_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ['status' => false, 'message' => implode(' ', $validator->errors()->all()),"user_access"=>1];
            }//..... end if() .....//
            $reviewObje = (object) array();
            $reviewArray = [];
            $reviews = Review::select('id as review_id','user_id','review','rating','created_at as review_date')->where('product_id' , request()->product_id)->get();
            if(count($reviews) > 0){
                foreach($reviews as $row){
                    $row->review_date = date('d-m-Y' , strtotime($row->review_date));
                    $users = User::select('user_id','first_name','last_name','user_name','user_image')->where('user_id' , $row->user_id)->first();
                    if($users){
                        if($users->user_image == ''){
                            if($users->social_image != ''){
                                $users->user_image = $users->social_image;
                            }else{
                                $users->user_image = URL::to('/front/dummy_round.png');
                            }
                        }else{
                            $users->user_image = URL::to('/') . '/users/'.$users->user_image;
                        }   
                    }
                    $row->user_info = $users;
                    $reviewArray[] = $row;
                }
            }
            $total_reviews = Review::where('product_id' , request()->product_id)->count();
            $ratings = Review::where('product_id' , request()->product_id)->avg('rating');
            if($ratings == null){
                $product_rating = 0;
            }else{
                $product_rating = $ratings;
            }
            $reviewObje->total_reviews = $total_reviews;
            $reviewObje->total_rating = $product_rating;
            $reviewObje->reviews = $reviewArray;
            return ["status" => true,"message"=>"data found","user_access"=>request()->user()->user_access,"productreview"=>$reviewObje];

        }catch(\Exception $e){
            return ['status'=>false,"message"=>$e->getMessage(),"user_access"=>1];
        }
    }
}
