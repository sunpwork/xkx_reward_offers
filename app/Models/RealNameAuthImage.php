<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealNameAuthImage extends Model
{
    protected $keyType = 'varchar';

    public $incrementing = false;

    protected $fillable = ['url'];

    public function realNameAuth()
    {
        return $this->belongsTo(RealNameAuth::class, 'real_name_auth_id', 'id');
    }
}
