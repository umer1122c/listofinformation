<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = "user_id";
    protected $fillable = ['id','user_id','deviceType','social_type','social_id','first_name','last_name','business_name','email','user_name','password','phone','user_image','social_image','phone','status','referral_code','user_access'];
    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'=>'integer','user_id'=>'integer','social_type'=>'integer','social_id'=>'string','deviceType'=>'string','first_name'=>'string','last_name'=>'string','business_name'=>'string','email'=>'string','user_name'=>'string','password'=>'string','phone'=>'string','user_image'=>'string','social_image'=>'string','referral_code'=>'string','user_access'=>'integer'
    ];
}
