<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;

//        if (!app()->environment('production')) {
//            $code = '123456';
//        } else {
        $code = (string)random_int(100000, 999999);
        try {
            $easySms->send($phone, [
                'template' => env('QCLOUD_SMS_TEMPLATE_ID'),
                'data' => [$code]
            ]);
        } catch (NoGatewayAvailableException $exception) {
            $message = $exception->getException('qcloud')->getMessage();
            return $this->response->errorInternal($message ?: '短信发送异常');
        }
//        }

        $key = 'verificationCode_' . str_random(15);
        $expiredAt = now()->addMinutes(10);
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }

    public function check(Request $request)
    {
        $this->validate($request, [
            'verification_key' => 'required|string',
            'verification_code' => 'required|string'
        ]);

        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            return $this->response->errorUnauthorized('验证码错误');
        }
        \Cache::forget($request->verification_key);
        return $this->response->array(['phone' => $verifyData['phone']]);
    }
}
