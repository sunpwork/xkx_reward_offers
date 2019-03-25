<?php

namespace App\Http\Requests\Api;

class ApplyRecordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'phone' => 'regex:/^1[34578]\d{9}$/',
            'gender' => 'string|in:male,female',
        ];
    }
}
