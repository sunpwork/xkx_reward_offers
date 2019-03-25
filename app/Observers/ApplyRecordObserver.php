<?php

namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

use App\Models\ApplyRecord;
use Faker\Provider\Uuid;

class ApplyRecordObserver
{
    public function creating(ApplyRecord $applyRecord){
        if (!$applyRecord->id){
            $applyRecord->id = Uuid::uuid();
        }
    }

    public function created(ApplyRecord $applyRecord)
    {
        $position = $applyRecord->position;

        $position->increment('apply_quantity', 1);
    }

    public function deleted(ApplyRecord $applyRecord)
    {
        $applyRecord->position->decrement('apply_quantity', 1);
    }
}