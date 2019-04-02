<?php

namespace App\Transformers;

use App\Models\RealNameAuthImage;
use League\Fractal\TransformerAbstract;

class RealNameAuthImageTransformer extends TransformerAbstract
{

    public function transform(RealNameAuthImage $realNameAuthImage)
    {
        return [
            'id' => $realNameAuthImage->id,
            'url' => $realNameAuthImage->url,
        ];
    }
}