<?php

namespace App\Http\Requests\Api;

class ErrandRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|string',
            'appointment_time' => 'required|string',
            'gender_limit' => 'required|string|in:male,female,noLimit',
            'expense' => 'required|numeric',
            'location_name' => 'required|string',
            'location_address' => 'required|string',
            'location_latitude' => 'required|numeric',
            'location_longitude' => 'required|numeric',
        ];
    }
}
