<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = "courses";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','course_id','cat_id','course_title','course_description','html_description','price','course_image','status','created_at','updated_at'];
    protected $casts = [
        'id'=>'integer','course_id'=>'integer','cat_id'=>'integer','course_title'=>'string','course_description'=>'string','price'=>'string','course_image'=>'string','product_discount'=>'integer','status'=>'integer'
    ];
}
