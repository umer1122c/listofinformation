<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = "reviews";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','user_id','product_id','review_text','rating','created_at','updated_at'];
    protected $casts = [
        'id'=>'integer','user_id'=>'integer','product_id'=>'integer','review_text'=>'string','rating'=>'integer'
    ];
}
