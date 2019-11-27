<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Extensions\Curl;

class ErrorLogsController extends AdminController
{
    /**
     * 日志文件前缀
     * @var string
     */
    private static $logPrefix = 'logs/laravel-';

    /**
     * 日志文件后缀
     * @var string
     */
    private static $logSuffix = '.log';

    /**
     * 按日期查询错误日志(查询后台)
     * @return \Illuminate\Http\JsonResponse
     */
    public function ErrorLogsAdminSelect()
    {
        //获取日期查询条件
        $get = self::requestParam(__FUNCTION__);

        if (isset($get['date'])) {
            //有查询日期的日志文件路径
            $path = storage_path(self::$logPrefix . $get['date'] . self::$logSuffix);
        } else {
            //没有日期查询全部
            $path = storage_path('logs/laravel.log');
        }
        if (!file_exists($path)) {
            //当前日期无数据
            return self::responseJson(Code::NULL_DATE_LOGS);
        }
        $logs = file_get_contents($path);
        self::$response['data'] = $logs;
        return $this->responseJson(Code::SUCCESS);

    }

    /**
     * 前台错误日志查询
     * @return \Illuminate\Http\JsonResponse
     */
    public function ErrorLogsFrontSelect()
    {
        //获取日期查询条件
        $get = self::requestParam(__FUNCTION__);

        if (isset($get['date'])) {
            //有查询日期的日志文件路径
            $path = storage_path(self::$logPrefix . $get['date'] . self::$logSuffix);
            $path = str_replace('adminManager', 'onlinePay', $path); // 替换为前台路径
        } else {
            //没有日期查询全部
            $path = str_replace('adminManager', 'onlinePay', storage_path('logs/laravel.log')); // 替换为前台路径
        }
        if (!file_exists($path)) {
            //当前日期无数据
            return self::responseJson(Code::NULL_DATE_LOGS);
        }
        $logs = file_get_contents($path);
        self::$response['data'] = $logs;
        return $this->responseJson(Code::SUCCESS);
    }
}
