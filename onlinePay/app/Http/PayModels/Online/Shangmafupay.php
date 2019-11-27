<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shangmafupay extends ApiModel
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

        $data               = [];
        $data['mchNo']      = $payConf['business_num'];//修改为自己的商户号
        $data['outTradeNo'] = $order;//订单号
        $data['amount']     = $amount * 100;//交易金额 （单位分）
        $data['body']       = 'test';//商品名称
        $data['payDate']    = date('YmdHis');//交易金额 （单位分）
        $data['notifyUrl']  = $ServerUrl;//异步回调地址 （公网可访问的地址）
        if ($payConf['pay_way'] == '1') {
            $data['returnUrl'] = $returnUrl;//同步回调地址 （公网可访问的地址）
            $data['channel']   = '1';//渠道类型
            $data['bankType']  = '11';//银行卡编码
            $data['bankCode']  = $bank;//银行卡类型
        } else {
            $data['title']   = 'test';//商品标题
            $data['channel'] = $bank;//支付渠道（1.微信，2.支付宝，3.qq钱包，4京东扫码，5银联扫码）
        }
        $data['sign']   = self::sign($data, $payConf['rsa_private_key'], $payConf['vircarddoin']);//签名
        $data['remark'] = 'ceshi';//备注

        unset($reqData);
        return $data;
    }

    //签名
    private static function sign($args, $merchantPrivateKey, $merchantSignKey)
    {

        $privateKey = '-----BEGIN RSA PRIVATE KEY-----' . "\n" . $merchantPrivateKey . "\n" . '-----END RSA PRIVATE KEY-----';

        ksort($args);
        $mab = '';
        foreach ($args as $k => $v) {
            $mab = $mab . $k . '=' . $v . '&';
        }
        $mab = $mab . 'signKey=' . $merchantSignKey;

        $data = md5($mab);

        $strSignature = self::signs($data, $privateKey);

        return $strSignature;


    }

    /**
     * 获取签名
     * @param string $strData 加密数据
     * @param string $privateKey 私钥
     * @return string $signature 签名
     */
    function signs($strData, $privateKey)
    {
        if (!openssl_get_privatekey($privateKey)) {
            echo 'encryptTaiping openssl_get_privatekey failed.';
            return false;
        }
        $signature = '';
        if (!openssl_sign($strData, $signature, $privateKey, OPENSSL_ALGO_SHA1)) {
            echo 'openssl_sign failed.';
            return false;
        }
        $signature = base64_encode($signature);
        return $signature;
    }
}