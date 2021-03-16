<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $contentId
 * @property integer $contentProfileId
 * @property integer $commentProfileId
 * @property int $status
 * @property string $commentLine
 * @property string $updated_at
 * @property string $created_at
 * @property LupinContent $lupinContent
 */
class UserLoginData extends Model
{
    /**
     * @var array
     */
    public $timestamps = false;
    protected $table = 'userlogindata';
    protected $fillable = ['userId', 'deviceToken', 'deviceType', 'appversion', 'loginToken', 'tokenStatus', 'timeZone','createdDate'];
    protected $casts = [
        'userId'=>'integer','deviceToken'=>'string','deviceType'=>'string','appversion'=>'float','loginToken'=>'string','tokenStatus'=>'integer','timeZone'=>'string'
    ];
  //   protected $hidden = array('created_at', 'updated_at');


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    
}
