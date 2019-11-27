<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zuanspay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

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
        //TODO: do something
        self::$method  = 'header';
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$unit    = 2;
        self::$isAPP   = true;

        $pubkey = openssl_pkey_get_public($payConf['public_key']);
        $paykey = openssl_get_privatekey($payConf['rsa_private_key']);
        if($paykey == false || $pubkey ==false){
            echo '公钥或私钥格式不对';exit;
        }

        $data                   = [];
        $data['service']        = 'quickPayApply';
        $data['merchantNo']     = $payConf['business_num'];
        $data['bgUrl']          = $ServerUrl;
        $data['version']        = 'V1.0';
        $data['orderNo']        = $order;
        $data['orderAmount']    = (int)$amount * 100;
        $data['curCode']        = 'CNY';
        $data['orderTime']      = date("YmdHis");
        $data['productName']    = 'zs';
        $data['payChannelType'] = $bank;
        $signStr                = self::getSignStr($data, true, true);
        $data['sign']           = self::getRsaSign($signStr, $payConf['rsa_private_key']);
        $post                   = [];
        $post['data']           = json_encode($data);
        $post['httpHeaders']    = [
            'Content-Type: application/json',
            'token: ' . $payConf['md5_private_key'],
            'Content-Length: ' . strlen($post['data'])];
        $post['orderNo']        = $data['orderNo'];
        $post['orderAmount']    = $data['orderAmount'];
        unset($reqData);
        return $post;
    }

    /***
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $res                 = $request->getContent();
        $data                = json_decode($res, true);
        $data['orderAmount'] = $data['orderAmount'] / 100;
        return $data;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        $dinPaySign = base64_decode($data["sign"]);
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $key = openssl_pkey_get_public($payConf['public_key']);
        $result = openssl_verify($signStr, $dinPaySign, $key, OPENSSL_ALGO_SHA1);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}