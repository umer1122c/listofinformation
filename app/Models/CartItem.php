<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = "cart_items";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','user_id','cart_id','product_id','name','qty','price','type','pally_id','status','created_at','updated_at'];
    protected $casts = [
        'id'=>'integer','user_id'=>'integer','cart_id'=>'integer','product_id'=>'integer','name'=>'string','price'=>'float','qty'=>'integer','type' => 'string','pally_id' => 'string','status'=>'integer'
    ];
}
