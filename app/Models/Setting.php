<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = "settings";
    public $timestamps = false;
    protected $primaryKey = "SettingId";
    protected $fillable = ['SettingId','IsEvent','IsSyncEposNow','IsUpdateEposNow','updated_at'];
	
}
