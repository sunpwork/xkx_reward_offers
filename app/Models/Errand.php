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
}
