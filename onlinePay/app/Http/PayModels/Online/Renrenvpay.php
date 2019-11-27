<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Renrenvpay extends ApiModel
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

        $data['merchantId'] = $payConf['business_num'];//商户号
        $data['notifyUrl']  = $ServerUrl; // 外网能访问
        $data['outOrderId'] = $order;//交易号
        $data['subject']    = 'zhif';//订单名称
        $data['body']       = 'zhif'; // 订单描述
        $data['transAmt']   = $amount;//交易金额
        if($payConf['pay_way'] != '9'){
            $data['scanType']   = $bank;//扫码类型
        }
        if((int)$payConf['pay_way'] === 1){
            unset($data['scanType']);
            $data['returnUrl'] = $returnUrl;
            $data['defaultBank'] = $bank;
            $data['channel'] = 'B2C';
            $data['cardAttr'] = '1';
        }
        $data['sign']       = self::getSendSignature($data, $payConf['rsa_private_key']);

        //TODO: do something
        if($payConf['pay_way'] != 9 || $payConf['pay_way'] != 1){
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$resType = 'other';
            self::$httpBuildQuery = true;
        }

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['respCode'] == '99') {
            $data['qrCode'] = $data['payCode'];
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        // $sign = $data['sign'];
        // unset($data['sign']);
        // $signStr  = self::getSignStr($data, true, true);
        // $signTrue = strtoupper(sha1($signStr . "&key=" . $payConf['md5_private_key']));
        if ($data['respCode'] == '00') {
            return true;
        }
        return false;
    }

    public static function getSendSignature($parm, $merchNo)
    {
        $data = [];
        foreach ($parm as $k => $v) {
            if ($k == 'sign' || $k == 'signType' || $v == '') {
                continue;
            }
            $data[$k] = $v;
        }
        $sign = self::array_to_querystr($data);
        return self::rsaSendSign($sign, $merchNo);
    }

    public static function rsaSendSign($data, $merid)
    {
        $key = openssl_get_privatekey($merid);
        openssl_sign($data, $sign, $key);
        openssl_free_key($key);
        $sign = base64_encode($sign);
        return $sign;
    }

    public static function array_to_querystr($parm, $enc = false)
    {
        $ary = [];
        foreach ($parm as $k => $v) {
            $val     = ($enc) ? urlencode($v) : $v;
            $ary[$k] = $k . '=' . $v;
        }
        ksort($ary);
        return implode('&', $ary);
    }

    public static function getRecvSignature($parm, $merchNo)
    {
        $data = [];
        foreach ($parm as $k => $v) {
            if ($k == 'sign' || $k == 'signType' || $v == '') {
                continue;
            }
            $data[$k] = $v;
        }
        $sign = self::array_to_querystr($data);
        return self::rsaRecvSign($sign, $parm['sign'], $merchNo);
    }

    public static function rsaRecvSign($data, $sign, $merid)
    {
        $key    = openssl_get_publickey($merid);
        $verify = openssl_verify($data, base64_decode($sign), $key, OPENSSL_ALGO_SHA1);
        return $verify;
    }
}