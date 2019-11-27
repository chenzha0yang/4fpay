<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jiandanfupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$unit = 2;
        $data       = [];
        if ($payConf['pay_way'] == '1') {
            $data['service'] = 'pay.b2c';
        } else {
            $data['service'] = $bank;
        }
        $data['version']    = '1.0';//接口版本
        $data['merchantId'] = $payConf['business_num']; //商家号
        $data['orderNo']    = $order; //商户网站唯一订单号
        $data['tradeDate']  = date("Ymd", time());; //商户交易日期
        $data['tradeTime'] = date("His", time());//商户交易时间
        $data['amount']    = $amount * 100; //商户订单总金额
        $data['cellPhone'] = '';
        $data['clientIp']  = '35.194.220.251'; //客户端IP
        $data['notifyUrl'] = $ServerUrl; //服务器异步通知地址
        $data['attach']    = '';
        $data['key']       = $payConf['md5_private_key'];
        $strToSign         = self::getSignStr($data, false, true);
        $data['sign']      = strtolower(md5($strToSign));//签名

        unset($reqData);
        return $data;
    }
}