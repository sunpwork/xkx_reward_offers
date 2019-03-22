<?php

namespace App\Http\Requests\Api;


class VerificationCodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => 'regex:/^1[34578]\d{9}$/'
        ];
    }

    public function attributes()
    {
        return [

        ];
    }
}
