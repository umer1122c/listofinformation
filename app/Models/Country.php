<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = "countries";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','name','image'];
    protected $casts = ['id'=>'integer','name'=>'string','image'=>'string'];
}
