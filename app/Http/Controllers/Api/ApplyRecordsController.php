<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApplyRecordRequest;
use App\Models\ApplyRecord;
use App\Models\Position;
use App\Transformers\ApplyRecordTransformer;
use Illuminate\Http\Request;

class ApplyRecordsController extends Controller
{
    public function store(ApplyRecordRequest $request, Position $position, ApplyRecord $applyRecord)
    {
        $applyRecord->fill($request->all());
        $applyRecord->user_id = $this->user()->id;
        $applyRecord->position_id = $position->id;
        $applyRecord->save();

        return $this->response->item($applyRecord, new ApplyRecordTransformer())->setStatusCode(201);
    }

    public function myApply(Position $position)
    {
        $applyRecord = ApplyRecord::where([
            'position_id' => $position->id,
            'user_id' => $this->user->id,
        ])->first();
        if ($applyRecord) {
            return $this->response->item($applyRecord, new ApplyRecordTransformer());
        }else{
            return $this->response->errorNotFound();
        }
    }
}
