<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "data_categories";
    public $timestamps = false;
    protected $primaryKey = "cat_id";
    protected $fillable = ['cat_id','cat_title','cat_image','parent_id'];
    protected $casts = [
        'cat_id'=>'integer','cat_title'=>'string','cat_image'=>'string','parent_id'=>'integer',
    ];
}
