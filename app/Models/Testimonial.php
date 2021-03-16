<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = "testimonials";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','name','image','review','status'];
    protected $casts = [
        'id'=>'integer','name'=>'string','image'=>'string','review'=>'string'
    ];
}
