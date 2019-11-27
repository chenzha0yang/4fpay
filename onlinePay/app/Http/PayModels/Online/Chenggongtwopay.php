<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;

class Chenggongtwopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $array = [];


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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something

        $params = [
            'out_trade_no' => $order,
            'pay_type'     => $bank,    // 1微信2支付宝
            'amount'       => $amount, //金额
            'time'         => time(),
            'notify_url'   => $ServerUrl,//回调通知地址
            'type'         => 'html',
        ];
        ksort($params);
        $encode = json_encode($params);
        $pub_id = openssl_get_publickey($payConf['public_key']);
        $key_len = openssl_pkey_get_details($pub_id)['bits'];
        $data = [
            'mch_id' => $payConf['business_num'],//商户号
            'sign'   => self::sign($encode,$payConf['rsa_private_key']),
            'params' => self::privateEncrypt($encode,$key_len,$payConf['rsa_private_key'])
        ];


        unset($reqData);
        return $data;
    }

    //回调处理数据 解密
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        $bankOrder = PayOrder::getOrderData($arr['out_trade_no']);//根据订单号 获取入款注单数据
        $bankOrder = json_decode($bankOrder,true);
        $payConf   = PayMerchant::findOrFail($bankOrder['merchant_id']);//根据订单中的商户表ID获取配置信息
        //RSA公钥解密key
        $pub_id = openssl_get_publickey($payConf['public_key']);
        $key_len = openssl_pkey_get_details($pub_id)['bits'];
        $json = self::publicDecrypt($arr['params'],$key_len,$payConf['public_key']);
        if(!$json){
            return false;
        }
        $data = json_decode($json, true);
        self::$array = $data;
        return $data;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $arr = self::$array;
        if (self::verify(json_encode($arr), $data["sign"],$payConf['public_key'])&& $arr['msg'] == 'SUCCESS'){
            return true;
        }else{
            return false;
        }

    }

    /*
    * 数据加签
    */
    public static function sign($data,$private_key) {
        openssl_sign($data, $sign, $private_key, OPENSSL_ALGO_SHA256);
        return base64_encode($sign);
    }

    /*
     * 私钥加密
     */
    public static function privateEncrypt($data,$key_len,$private_key) {
        $encrypted = '';
        $part_len = $key_len / 8 - 11;
        $parts = str_split($data, $part_len);
        foreach ($parts as $part) {
            $encrypted_temp = '';
            openssl_private_encrypt($part, $encrypted_temp, $private_key);
            $encrypted .= $encrypted_temp;
        }

        return base64_encode($encrypted);
    }

    public static function publicDecrypt($encrypted,$key_len,$public_key) {
        $decrypted = "";
        $part_len = $key_len / 8;
        $base64_decoded = self::url_safe_base64_decode($encrypted);
        $parts = str_split($base64_decoded, $part_len);
        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_public_decrypt($part, $decrypted_temp, $public_key);
            $decrypted .= $decrypted_temp;
        }
        return $decrypted;
    }

    /*
     * 数据签名验证
     */
    public static function verify($data, $sign,$public_key) {
        $pub_id = openssl_get_publickey($public_key);
        $res = openssl_verify($data, self::url_safe_base64_decode($sign), $pub_id, OPENSSL_ALGO_SHA256);
        return $res;
    }

    public static function url_safe_base64_decode($data) {
        $base_64 = str_replace(array(
            '-',
            '_'
        ), array(
            '+',
            '/'
        ), $data);
        return base64_decode($base_64);
    }

    public static function url_safe_base64_encode($data) {
        return str_replace(array(
            '+',
            '/',
            '='
        ), array(
            '-',
            '_',
            ''
        ), base64_encode($data));
    }
}