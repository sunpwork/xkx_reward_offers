<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function positions()
    {
        return $this->hasMany(Position::class, 'category_id', $this->primaryKey);
    }
}
