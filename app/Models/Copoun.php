<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Copoun extends Model
{
    protected $table = "copouns";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','min_price','code','discount','max_price_applay'];
    protected $casts = [
        'id'=>'integer','min_price'=>'float','code'=>'string','discount'=>'string','max_price_applay'=>'string',
    ];
}
