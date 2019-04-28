<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gender', 'phone', 'avatar',
        'weapp_openid', 'weixin_session_key'
    ];

    const GENDERS = [
        'male' => ['value' => 'male', 'name' => '男'],
        'female' => ['value' => 'female', 'name' => '女'],
        'secret' => ['value' => 'secret', 'name' => '保密']
    ];

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_SECRET = 'secret';

    public function applyRecords()
    {
        return $this->hasMany(ApplyRecord::class, 'user_id', $this->primaryKey);
    }

    public function realNameAuth()
    {
        return $this->hasOne(RealNameAuth::class, 'user_id', $this->primaryKey);
    }
}
