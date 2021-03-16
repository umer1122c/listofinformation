<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUser extends Model
{
    protected $table = "coupon_users";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','coupon_code','user_id'];
    protected $casts = [
        'id'=>'integer','coupon_code'=>'string','user_id'=>'integer'
    ];
}
