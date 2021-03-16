<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = "admin";
    public $timestamps = false;
    protected $primaryKey = "admin_id";
    protected $fillable = ['admin_id','admin_name','admin_email','admin_avatar','super_admin'];
}
