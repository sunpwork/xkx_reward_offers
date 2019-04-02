<?php

namespace App\Http\Requests\Api;

use Illuminate\Support\Facades\Auth;

class RealNameAuthRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = Auth::guard('api')->id();
        return [
            'name' => 'required|string',
            'phone' => 'regex:/^1[34578]\d{9}$/',
            'gender' => 'string|in:male,female',
        ];
    }
}
