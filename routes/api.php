<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api'
], function (\Dingo\Api\Routing\Router $api) {
    // 获取图片验证码
    $api->post('captchas','CaptchasController@store')
        ->name('api.captchas.store');
    // 短信验证码
    $api->post('verificationCodes', 'VerificationCodesController@store')
        ->name('api.verificationCodes.store');
    // 用户注册
    $api->post('users','UserController@store')
        ->name('api.users.store');
    // 小程序登录
    $api->post('weapp/authorizations','AuthorizationsController@weappStore')
        ->name('api.weapp.authorizations.store');

});