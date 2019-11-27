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
        '200' => [],
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
        '1014' => '参数提交错误',
        '1015' => '错误的代理线路',
        '1016' => '非法参数',
        '1017' => '商户信息不存在',
        '1018' => 'merchantId不能为空',
        '1019' => 'privateKey不能为空',
        '1020' => 'notifyUrl不能为空',
        '1021' => 'notifyUrl格式错误',
        '1022' => 'payId不能为空',
        '1023' => 'payType不能为空',
        '1024' => 'payId三方支付配置不存在',
        '1025' => 'payType支付类型不存在',
        '1026' => 'merchantId必传',
        '1027' => 'payType必传',
        '1028' => 'merId格式错误',
        '1029' => 'orderId格式错误',
        '1030' => '[longUrl]错误',
        '1031' => '[status]错误',
        '1032' => '长链接不存在',
    ];

    /**
     * @param string $code
     * @param bool $_
     * @return string|array
     */
    public static function getError($code = '400', $_ = false)
    {
        if (! isset(self::$errCodes[$code])) {
            $code = '400';
        }

        return $_ ? "[{$code}]" : self::$errCodes[$code];
    }
}
