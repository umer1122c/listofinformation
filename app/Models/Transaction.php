<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = "transactions";
    public $timestamps = false;
    protected $primaryKey = "transaction_id";
    protected $fillable = ['transaction_id','from_user_id','to_user_id','amount','trans_type','status'];
    protected $casts = [
        'transaction_id'=>'integer','from_user_id'=>'integer','to_user_id'=>'integer','amount'=>'float','trans_type'=>'integer','status'=>'integer'
    ];
}
