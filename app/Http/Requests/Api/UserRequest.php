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
            'avatar' => 'mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200',
            'verification_key' => 'string',
            'verification_code' => 'string',
        ];
    }

    public function messages()
    {
        return [
            'avatar.mimes' => '头像必须是jpeg,bmp,png,gif 格式的图片。',
            'avatar.dimensions' => '图片的大小要求宽和高在200px以上。',
        ];
    }
}
