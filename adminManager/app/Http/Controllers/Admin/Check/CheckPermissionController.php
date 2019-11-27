<?php

namespace App\Http\Controllers\Admin\Check;

use App\Http\Controllers\AdminController;
use App\Extensions\Code;
use Illuminate\Http\Request;

class CheckPermissionController extends AdminController
{
    /**
     * 权限验证
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function checkPermission(Request $request)
    {
        //是系统账号
        if (self::$adminUser->name == 'administrator') {
            return true;
        }
        //权限为空
        if (empty(self::$adminUser->ownPermissions)) {
            return self::getInstance()->responseJson(Code::NO_PERMISSION);
        }
        //不为空走验证
        foreach (self::$adminUser->ownPermissions as $permission) {
            if ($request->is(trim($permission->http_path, '/')) && in_array($request->getMethod(), $permission->http_method)) {
                return true;
            }
        }
        return self::getInstance()->responseJson(Code::NO_PERMISSION);
    }
}