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
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['bindings'],
], function (\Dingo\Api\Routing\Router $api) {
    // 获取图片验证码
    $api->post('captchas', 'CaptchasController@store')
        ->name('api.captchas.store');
    // 短信验证码
    $api->post('verificationCodes', 'VerificationCodesController@store')
        ->name('api.verificationCodes.store');
    // 校验短信验证码
    $api->put('verificationCodes/check', 'VerificationCodesController@check')
        ->name('api.verificationCodes.check');

    // 用户注册
    $api->post('users', 'UserController@store')
        ->name('api.users.store');
    // 小程序登录
    $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
        ->name('api.weapp.authorizations.store');
    // 刷新token
    $api->put('authorizations/current', 'AuthorizationsController@update')
        ->name('api.authorizations.update');

    // 兼职列表
    $api->get('positions', 'PositionsController@index')
        ->name('api.positions.index');
    // 兼职详情
    $api->get('positions/{position}', 'PositionsController@show')
        ->name('api.positions.show');
    // 所有分类
    $api->get('categories', 'CategoriesController@index')
        ->name('api.categories.index');

    // 需要token验证的接口
    $api->group([
        'middleware' => 'api.auth'
    ], function (\Dingo\Api\Routing\Router $api) {
        // 获取当前登录用户的信息
        $api->get('user', 'UsersController@me')
            ->name('api.user.show');
        // 小程序绑定手机号
        $api->put('weapp/user/bindPhoneNumber', 'UsersController@weappBindPhoneNumber')
            ->name('api.weapp.users.bindPhoneNumber');
        // 修改用户信息
        $api->put('user', 'UsersController@update')
            ->name('api.user.update');
        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        // 图片资源
        $api->post('images', 'ImagesController@store')
            ->name('api.images.store');

        // 提交报名申请
        $api->post('positions/{position}/apply_records', 'ApplyRecordsController@store')
            ->name('api.positions.reply_records.store');
        // 获取报名申请信息
        $api->get('user/positions/{position}/apply_record', 'ApplyRecordsController@myApply')
            ->name('api.user.positions.reply_record');
        // 获取报名申请信息
        $api->get('user/positions', 'PositionsController@myApplyIndex')
            ->name('api.user.positions');

        // 提交实名认证
        $api->post('user/realNameAuths', 'RealNameAuthsController@store')
            ->name('api.user.real_name_auths.store');
        // 获取实名认证信息
        $api->get('user/realNameAuth', 'RealNameAuthsController@show')
            ->name('api.user.real_name_auth');

        // 发布跑腿信息
        $api->post('errands','ErrandsController@store')
            ->name('api.errands.store');
    });
});