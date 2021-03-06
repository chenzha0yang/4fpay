<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wanlianpay extends ApiModel
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
     * @param array $reqData 接口传递的参数
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

        //TODO: do something
        self::$reqType        = 'curl';
        self::$httpBuildQuery = true;
        self::$payWay         = $payConf['pay_way'];
        if ($payConf['is_app'] = 1) {
            self::$isAPP = true;
        }
        $data                 = [];
        $data['merchant_no']  = $payConf['business_num'];
        $data['amount']       = number_format(floatval($amount), 2, '.', '');
        $data['currency']     = '156';
        $data['order_no']     = $order;
        $data['pay_code']     = $bank;
        $data['pay_ip']       = selfg::getIp();
        $data['request_time'] = date("Y-m-d H:i:s");
        $data['product_name'] = 'wl';
        $data['notify_url']   = $ServerUrl;
        $data['return_url']   = $returnUrl;
        $data['remark']       = 'wl';
        $signStr              = self::getSignStr($data, false, true);
        $data['sign']         = self::getMd5Sign($signStr . '&key=', $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    /***
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $mySign  = self::getMd5Sign($signStr . '&key=', $payConf['md5_private_key']);
        if ($sign == $mySign && $data['ord_status'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
}