<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = "notifications";
    public $timestamps = false;
    protected $primaryKey = "notification_id";
    protected $fillable = ['notification_id','sender_user_id','reciever_user_id','pally_id','title','body','type','status'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'notification_id'=>'integer','sender_user_id'=>'integer','reciever_user_id'=>'integer','pally_id'=>'string','title'=>'string','body'=>'string','type'=>'string','status'=>'integer'
    ];
}
