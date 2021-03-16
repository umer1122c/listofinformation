<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = "order_details";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','order_id','product_id','prod_title','quantity','price','pally_id','type','delivery_status','attribute_id','attribute_cost','attribute_name'];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'=>'integer','order_id'=>'integer','product_id'=>'integer','prod_title'=>'string','quantity'=>'integer','price'=>'string','pally_id'=>'string','type'=>'string','delivery_status'=>'string','attribute_id'=>'integer','attribute_cost'=>'string','attribute_name'=>'string'
    ];	
}