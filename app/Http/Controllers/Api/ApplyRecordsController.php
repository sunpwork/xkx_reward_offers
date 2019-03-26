<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApplyRecordRequest;
use App\Models\ApplyRecord;
use App\Models\Position;
use App\Models\User;
use App\Transformers\ApplyRecordTransformer;
use Illuminate\Http\Request;

class ApplyRecordsController extends Controller
{
    public function store(ApplyRecordRequest $request, Position $position, ApplyRecord $applyRecord)
    {
        if ($this->getApplyRecord($this->user(), $position)) {
            return $this->response->errorForbidden('重复提交');
        } else if (!$position->display){
            return $this->response->errorForbidden('报名已关闭');
        }else{
            $applyRecord->fill($request->all());
            $applyRecord->user_id = $this->user()->id;
            $applyRecord->position_id = $position->id;
            $applyRecord->save();

            return $this->response->item($applyRecord, new ApplyRecordTransformer())->setStatusCode(201);
        }
    }

    public function myApply(Position $position)
    {
        $applyRecord = $this->getApplyRecord($this->user(), $position);
        if ($applyRecord) {
            return $this->response->item($applyRecord, new ApplyRecordTransformer());
        } else {
            return $this->response->errorNotFound();
        }
    }

    protected function getApplyRecord(User $user, Position $position)
    {
        return ApplyRecord::where([
            'position_id' => $position->id,
            'user_id' => $user->id,
        ])->first();
    }
}
