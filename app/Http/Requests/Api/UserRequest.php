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
            'name' => 'required|string',
            'sex' => 'required|string|in:male,female',
            'verification_key' => 'required|string',
            'verification_code' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }
}
