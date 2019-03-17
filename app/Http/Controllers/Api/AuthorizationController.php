<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizationController extends Controller
{
    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 错误');
        }
        $user = User::where('weapp_openid', $data['openid'])->first();

        if (!$user) {
            return $this->response->errorForbidden('用户不存在');
        }

        $user->weixin_session_key = $data['session_key'];
        $user->save();

        $token = Auth::guard('api')->fromUser($user);
        return $this->responseWithToken($token)->setStatusCode(201);
    }

    protected function responseWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }

    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->responseWithToken($token);
    }

    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }
}
