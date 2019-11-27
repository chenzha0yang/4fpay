<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yutongpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit           = 2; // 单位 ： 分
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;

        $data = array(
            'version'         => '1.0.0',
            'transType'       => 'SALES',
            'productId'       => $bank,
            'merNo'           => $payConf['business_num'],
            'orderDate'       => date("Ymd"), //交易日期
            'orderNo'         => $order, //订单号
            'notifyUrl'       => $ServerUrl,
            'returnUrl'       => $returnUrl,
            'transAmt'        => $amount * 100, //分为
            'commodityName'   => 'honor',
            'commodityDetail' => 'i9', //产品描述
        );
        if ($payConf['pay_way'] == '1') {
            $data['productId'] = '0001';
            $data['bankCode']  = $bank;
        }
        $temp    = self::getSignStr($data, false, true);
        $pay_key = openssl_pkey_get_private($payConf['rsa_private_key']);
        openssl_sign($temp, $sign, $pay_key);
        $signature         = base64_encode($sign);
        $data['signature'] = $signature; //签名

        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['transAmt'])) {
            $arr['transAmt'] = $arr['transAmt'] / 100;
        }
        return $arr;
    }

    public static function signOther($mod, $data, $payConf)
    {
        $sign = $data['signature'];
        unset($data['signature']);
        //对返回数据按 ascii 方式排序   注意：如果值为空  不参与签名
        $temp    = self::getSignStr($data, false, true);
        $pub_key = openssl_get_publickey($payConf['public_key']);
        $true    = (bool)openssl_verify($temp, base64_decode($sign), $pub_key, OPENSSL_ALGO_SHA1);
        return $true;
    }

}