<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','order_id','user_id','deviceType','type','address','order_total','shipping_cost','contact_type','discount_amount','coupon_code','wallet_amount','reference','paystck_id','paystck_responce','dilivery_date','status','created_at','updated_at'];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'=>'integer','user_id'=>'integer','order_id'=>'integer','deviceType'=>'string','type'=>'string','order_total'=>'string','shipping_cost'=>'string','contact_type'=>'string','discount_amount'=>'string','coupon_code'=>'string','wallet_amount'=>'string','reference'=>'string','paystck_id'=>'string','paystck_responce'=>'string','status'=>'string','dilivery_date'=>'string','created_at'=>'string','updated_at'=>'string'
    ];	
}