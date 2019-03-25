<?php

namespace App\Transformers;

use App\Models\Position;
use League\Fractal\TransformerAbstract;

class PositionTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['category'];

    public function transform(Position $position)
    {
        return [
            'id' => $position->id,
            'category_id' => (int)$position->category_id,
            'title' => $position->title,
            'covers' => $position->covers,
            'detail_info' => $position->detail_info,
            'contact_man' => $position->contact_man,
            'contact_phone' => $position->contact_phone,
            'quantity' => (int)$position->quantity,
            'apply_quantity' => (int)$position->apply_quantity,
            'salary' => $position->salary,
            'work_address' => $position->work_address,
            'created_at' => $position->created_at->toDateTimeString(),
            'updated_at' => $position->updated_at->toDateTimeString(),
        ];
    }

    public function includeCategory(Position $position)
    {
        return $this->item($position->category, new CategoryTransformer());
    }
}