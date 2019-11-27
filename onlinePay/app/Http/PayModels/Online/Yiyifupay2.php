<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yiyifupay2 extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /**
     * @param array       $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something

        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType        = 'other';

        $data = array(
            'merchantId'  => $payConf['business_num'],
            'timestamp'   => self::getMsectime(),
            'tradeNo'     => $order,
            'notifyUrl'   => $ServerUrl,
            'totalAmount' => $amount,
            'subject'     => 'title',
            'body'        => 'orderInfo',
        );
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $val) {
            $signStr .= $key . '=' . urlencode($val) . '&';
        }
        $signStr           = rtrim($signStr, '&');
        $data['signature'] = hash_hmac('sha1', $signStr, $payConf['md5_private_key']);


        unset($reqData);
        return $data;
    }

    /**
     * @return array|false|string
     * 获取客户端真实IP
     */
    public static function getClientIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }

    /**
     * @return string
     * [返回十三位时间戳、精确到毫秒]
     */
    public static function getMsectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (string)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    public static function getQrcode($req)
    {
        $data                       = json_decode($req, true);
        $returnData                 = [];
        $returnData['errCode']      = $data['code'];
        $returnData['errMsg']       = $data['msg'];
        $returnData['msg']          = $data['data']['msg'];
        $returnData['code']         = $data['data']['code'];
        $returnData['out_trade_no'] = $data['data']['out_trade_no'];
        $returnData['qr_code']      = $data['data']['qr_code'];

        return $returnData;
    }
    public static function SignOther($model, $data, $payConf)
    {

       if ($payConf['pay_way'] == 2) {
           $sign = $data['sign'];
           unset($data['sign']);
           $signStr  = self::getSignStr($data, true, true);
           $mySign = strtoupper(md5($signStr . '&key=' .  $payConf['md5_private_key']));

           if ($sign == $mySign && $data['result_code'] == 'SUCCESS') {
               return true;
           } else {
               return false;
           }
       }

    }
}