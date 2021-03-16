<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = "posts";
    public $timestamps = false;
    protected $primaryKey = "bookmark_id";
    protected $fillable = ['bookmark_id','user_id','product_id','created_at','updated_at'];
    protected $casts = [
        'bookmark_id'=>'integer','user_id'=>'integer','product_id'=>'integer'
    ];
}
