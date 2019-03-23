<?php

namespace App\Http\Requests\Api;

class UserRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string',
            'gender' => 'string|in:male,female,secret',
            'verification_key' => 'string',
            'verification_code' => 'string',
        ];
    }
}
