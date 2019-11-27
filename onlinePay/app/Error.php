<?php

namespace App;

class Error
{
    /**
     * 错误码
     * @var [type]
     */
    public static $errCodes = [
        // 系统码
        '200' => '成功',
        '400' => '未知错误',
        '401' => '无此权限',
        '500' => '服务器异常',

        // 公共错误码
        '1001' => '[userId]缺失',
        '1002' => '[userId]不存在或无权限',
        '1003' => '[method]缺失',
        '1004' => '[format]错误',
        '1005' => '[name]错误',
        '1009' => 'runApi方法不存在，请联系管理员',
        '1010' => '[secret]缺失',
        '1011' => '[secret]必须为字符串',
        '1012' => '[startTime]不能为空',
        '1013' => '查询区间不得大于一天',
        '1014' => '提交参数不完整',
    ];

    /**
     * @param string $code
     * @param bool $_
     * @return string
     */
    public static function getError($code = '400', $_ = false)
    {
        if (! isset(self::$errCodes[$code])) {
            $code = '400';
        }

        return ($_ ? "[{$code}]" : '')
            . self::$errCodes[$code];
    }
}
