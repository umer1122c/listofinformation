<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = "teams";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','memberName','memberDesignation','memberImage','memberInformation','status'];
    protected $casts = [
        'id'=>'integer','memberName'=>'string','memberDesignation'=>'string','memberImage'=>'string'
    ];
}
