<?php

namespace App\Transformers;

use App\Models\RealNameAuth;
use League\Fractal\TransformerAbstract;

class RealNameAuthTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['realNameAuthImages'];

    public function transform(RealNameAuth $realNameAuth)
    {
        return [
            'id' => $realNameAuth->id,
            'name' => $realNameAuth->name,
            'gender' => $realNameAuth->gender,
            'phone' => $realNameAuth->phone,
            'status' => $realNameAuth->status
        ];
    }

    public function includeRealNameAuthImages(RealNameAuth $realNameAuth)
    {
        return $this->collection($realNameAuth->realNameAuthImages,new RealNameAuthImageTransformer());
    }
}