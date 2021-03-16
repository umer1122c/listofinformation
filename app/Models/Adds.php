<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adds extends Model
{
    protected $table = "adds";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['postid','position','add_content'];
    protected $casts = [
        'id'=>'integer','title'=>'string','image'=>'string'
    ];
}
