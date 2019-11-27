<?php

namespace App\Http\Controllers\Admin\Index;

use App\Http\Controllers\AdminController;
use App\Extensions\Captcha;
use App\Extensions\RedisConPool;

/**
 * 验证码
 *
 * Class CaptchaController
 * @package App\Http\Controllers\V1\Index
 */
class CaptchaController extends AdminController
{

    /**
     * 生成验证码
     */
    public function createCode()
    {
        $token = self::requestParam(__FUNCTION__)[trans('auth.code')];
        RedisConPool::setEx(
            getRedis('captcha', ['token' => md5($token)]),
            60,
            Captcha::getInstance()->getCode()
        );
        Captcha::getInstance()->doImg();
    }
}