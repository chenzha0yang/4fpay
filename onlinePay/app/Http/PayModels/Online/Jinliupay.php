<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jinliupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

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
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址
        $ip        = '';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
            if ($payConf['pay_way'] == 2) {
                $ip = self::getClientIP();
            }
        }

        //TODO: do something
        self::$unit     = 2; // 单位 ： 分
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$resType  = 'other';
        self::$postType = true;
//        self::$method  = 'header';

        $data            = array(
            'action'   => $bank,
            'txnamt'   => $amount * 100,
            'merid'    => $payConf['business_num'],
            'orderid'  => $order,
            'code'     => '',
            'openid'   => '',
            'ip'       => $ip,
            'backurl'  => $ServerUrl,
            'fronturl' => $returnUrl,
        );
        $json            = json_encode($data);
        $sign            = md5(base64_encode($json) . $payConf['md5_private_key']);
        $post['resp']    = urlencode(base64_encode($json));
        $post['sign']    = $sign;
        $post['txnamt']  = $data['txnamt'];
        $post['orderid'] = $data['orderid'];


        unset($reqData);
        return $post;
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

    public static function getQrCode($req)
    {

        $arr = json_decode($req, true);

        $reData = base64_decode($arr['resp']);
        $reData = json_decode($reData, true);
        return $reData;
    }

    public static function getRequestByType($data)
    {
        $str = 'req=' . $data['resp'] . '&sign=' . $data['sign'];
        return $str;
    }
}