<?php

namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

use App\Models\RealNameAuthImage;
use Faker\Provider\Uuid;

class RealNameAuthImageObserver
{
    public function creating(RealNameAuthImage $realNameAuthImage){
        if (!$realNameAuthImage->id){
            $realNameAuthImage->id = Uuid::uuid();
        }
    }
}