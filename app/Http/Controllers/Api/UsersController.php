<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
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
}
