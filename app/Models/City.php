<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = "cities";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','name','image'];
    protected $casts = ['id'=>'integer','name'=>'string','image'=>'string'];
}
