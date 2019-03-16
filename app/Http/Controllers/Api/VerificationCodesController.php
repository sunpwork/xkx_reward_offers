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
        $captchaData = \Cache::get($request->captcha_key);
        if (!$captchaData) {
            return $this->response->error('图片验证码已失效', 422);
        }
        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            \Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('验证码错误');
        }

        $tel = $captchaData['tel'];

//        if (!app()->environment('production')) {
//            $code = '123456';
//        } else {
            $code = (string)random_int(100000, 999999);
            try {
                $easySms->send($tel, [
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
        \Cache::put($key, ['tel' => $tel, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
