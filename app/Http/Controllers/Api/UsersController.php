<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\UserRequest;
use App\Models\Image;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    public function weappBindPhoneNumber(Request $request)
    {
        $this->validate($request, [
            'encryptedData' => 'required|string',
            'iv' => 'required|string'
        ]);
        $miniProgram = \EasyWeChat::miniProgram();
        $user = $this->user();
        $decryptedData = $miniProgram->encryptor->decryptData($user->weixin_session_key, $request->iv, $request->encryptedData);
        $user->phone = $decryptedData['phoneNumber'];
        $user->save();
        return $this->response->item($user, new UserTransformer());
    }

    public function update(UserRequest $request,ImageUploadHandler $uploadHandler)
    {
        $user = $this->user();

        $attributes = $request->only(['name', 'gender', 'avatar']);
        if ($request->verification_key && $request->verification_code) {
            $verifyData = \Cache::get($request->verification_key);

            if (!$verifyData) {
                return $this->response->error('验证码已失效', 422);
            }

            if (!hash_equals($verifyData['code'], $request->verification_code)) {
                return $this->response->errorUnauthorized('验证码错误');
            }
            $attributes['phone'] = $verifyData['phone'];
            \Cache::forget($request->verification_key);
        }
        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);
            $attributes['avatar'] = $image->path;
        }
        $user->update($attributes);
        return $this->response->item($user, new UserTransformer())->setStatusCode(201);
    }
}
