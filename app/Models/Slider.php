<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = "sliders";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','order_number','type','image'];
	
}
