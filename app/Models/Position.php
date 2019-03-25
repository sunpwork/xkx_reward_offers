<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Position extends Model
{
    protected $fillable = ['category_id', 'title', 'covers', 'detail_info', 'contact_man', 'contact_phone',
        'quantity', 'salary', 'work_address'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function applyRecords()
    {
        return $this->hasMany(ApplyRecord::class, 'position_id', $this->primaryKey);
    }

    public function setCoversAttribute($value)
    {
        $this->attributes['covers'] = Storage::disk('qiniu')->getUrl($value);
    }
}
