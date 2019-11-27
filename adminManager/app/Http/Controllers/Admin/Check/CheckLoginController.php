<?php

namespace App\Http\Controllers\Admin\Check;

use App\Http\Controllers\Admin\Index\LoginController;
use App\Http\Controllers\AdminController;
use App\Extensions\Code;
use Illuminate\Http\Request;
use App\Extensions\RedisConPool;

class CheckLoginController extends AdminController
{

    /**
     * 登录验证
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function checkLogin(Request $request)
    {
        if (empty($request->header("C-Token"))) {

            return self::getInstance()->responseJson(Code::PLEASE_LOGIN_AGAIN);
        }
        if (empty($request->header("R-Token"))) {
            return self::getInstance()->responseJson(Code::PLEASE_LOGIN_AGAIN_2);
        }

        if (!$token = $request->header("C-Token")) {
            return self::getInstance()->responseJson(Code::PLEASE_LOGIN_AGAIN_3);
        }

        try {
            $key = decrypt($token);
        } catch (\Exception $exception) {
            return self::getInstance()->responseJson(Code::PLEASE_LOGIN_AGAIN_4);
        }

        $admin = RedisConPool::get(getRedis('adminSession', ['key' => $key]));

        if (empty($admin)) {
            return self::getInstance()->responseJson(Code::PLEASE_LOGIN_AGAIN_5);
        }

        self::$adminUser = json_decode($admin);

        if (empty(self::$adminUser)) {
            return self::getInstance()->responseJson(Code::PLEASE_LOGIN_AGAIN_6);
        }

//        //验证IP是否在允许登陆范围内
//        if (empty(self::$adminUser->login_ip)) {
//            return self::getInstance()->responseJson(Code::NOT_ALLOWED_LOGIN_IP);
//        }
//
//        $ipList = explode(',', self::$adminUser->login_ip);
//
//        if (!in_array(self::getClientIp(0, true), $ipList, true)) {
//            (new LoginController)->logout();
//            return self::getInstance()->responseJson(Code::NOT_ALLOWED_LOGIN_IP);
//
//        }

        if ($request->header('R-Token') !== self::$adminUser->remember_token) {
            self::$adminUser = null;
            return self::getInstance()->responseJson(Code::REMOTE_LOGIN);
        }

        // 刷新登陆
        RedisConPool::setEx(
            getRedis('adminSession', ['key' => $key]),
            config("app.loginTimer"),
            $admin
        );

        return true;
    }
}
