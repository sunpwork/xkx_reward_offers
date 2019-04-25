<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Errand extends Model
{
    const STATUSES = [
        'waitingPay' => ['value' => 'waitingPay', 'name' => '待付款', 'label_class' => 'label-danger'],
        'pending' => ['value' => 'pending', 'name' => '待接单', 'label_class' => 'label-warning'],
        'done' => ['value' => 'done', 'name' => '已完成', 'label_class' => 'label-success']
    ];

    const STATUS_WAITINGPAY = 'waitingPay';
    const STATUS_PENDING = 'pending';
    const STATUS_DONE = 'done';

    const GENDER_LIMITS = [
        'male' => ['value'=>'male','name' => '限男生'],
        'female' => ['value'=>'female','name' => '限女生'],
        'noLimit' => ['value'=>'noLimit','name' => '不限性别']
    ];

    const GENDER_LIMITS_MALE = 'male';
    const GENDER_LIMITS_FEMALE = 'female';
    const GENDER_LIMITS_NOLIMIT = 'noLimit';

    protected $keyType = 'varchar';

    public $incrementing = false;

    protected $fillable = [
        'content', 'hidden_content', 'appointment_time', 'gender_limit', 'expense',
        'location_name', 'location_address', 'location_latitude', 'location_longitude'
    ];

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
