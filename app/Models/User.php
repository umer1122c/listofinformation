<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "users";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = ['id','user_id','first_name','last_name','email','password','phone','user_image','status'];
	
}
