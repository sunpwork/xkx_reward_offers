<?php

namespace App\Transformers;

use App\Models\ApplyRecord;
use League\Fractal\TransformerAbstract;

class ApplyRecordTransformer extends TransformerAbstract
{
    public function transform(ApplyRecord $applyRecord)
    {
        return [
            'id' => $applyRecord->id,
            'name' => $applyRecord->name,
            'gender' => $applyRecord->gender,
            'phone' => $applyRecord->phone
        ];
    }
}