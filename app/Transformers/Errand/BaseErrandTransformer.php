<?php


namespace App\Transformers\Errand;


use App\Models\Errand;
use App\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class BaseErrandTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    public function transform(Errand $errand)
    {
        return [
            'id' => $errand->id,
            'content' => $errand->content,
            'appointment_time' => $errand->appointment_time,
            'gender_limit' => $errand->gender_limit,
            'expense' => $errand->expense,
            'location_name' => $errand->location_name,
            'location_address' => $errand->location_address,
            'location_latitude' => $errand->location_latitude,
            'location_longitude' => $errand->location_longitude,
            'status' => $errand->status,
        ];
    }

    public function includeUser(Errand $errand)
    {
        return $this->item($errand->user, new UserTransformer());
    }
}