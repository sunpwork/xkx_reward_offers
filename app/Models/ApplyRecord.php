<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplyRecord extends Model
{
    protected $fillable = ['position_id', 'user_id', 'name', 'phone', 'gender'];

    protected $keyType = 'varchar';

    public $incrementing = false;

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
