<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = "newsletters";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','email','status'];
    protected $casts = [
        'id'=>'integer','email'=>'string','status'=>'integer'
    ];
}
