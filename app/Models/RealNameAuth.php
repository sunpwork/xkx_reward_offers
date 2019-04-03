<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealNameAuth extends Model
{
    const STATUSES = [
        'pending' => ['value' => 'pending', 'name' => '待审核', 'label_class' => 'label-warning'],
        'invalid' => ['value' => 'invalid', 'name' => '未通过', 'label_class' => 'label-danger'],
        'active' => ['value' => 'canceled', 'name' => '审核通过', 'label_class' => 'label-success']
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_INVALID = 'invalid';
    const STATUS_ACTIVE = 'active';

    protected $fillable = ['name', 'phone', 'gender'];

    public function realNameAuthImages()
    {
        return $this->hasMany(RealNameAuthImage::class, 'real_name_auth_id', $this->primaryKey);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
