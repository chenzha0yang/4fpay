<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zhongbipay extends ApiModel
{

    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $changeUrl = ''; //自定义请求地址

    public static $amount = 0; // callback  amount

    public static $httpBuildQuery = false;


    public static function getData($reqData, $payConf)
    {
        static::$action = 'curlPost';
        static::$httpBuildQuery = true;

        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $data                = [];
        $data['merchantId'] = $payConf['business_num'];//商户ID
        $data['timestamp'] = time()*1000;
        $data['signatureMethod'] = 'HmacSHA256';
        $data['signatureVersion'] = 1;
        $data['jUserId'] = $payConf['business_num'];
        $data['jUserIp'] = self::getIp();
        $data['jOrderId'] = $order;
        $data['orderType'] = 1;
        $data['payWay'] = $bank;
        $data['amount'] = $amount;
        $data['currency'] = 'CNY';
        $data['notifyUrl'] = $ServerUrl;

        $signStr = self::getSignStr($data,true,true);
        $data['signature']  = strtoupper(hash_hmac('sha256', $signStr, $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }


    public static function getQrCode($result)
    {
        $res = json_decode($result, true);
        if ($res['code'] == '0') {
            static::$result['appPath'] = $res['data']['paymentUrl'];
            self::$pc = true;
        } else {
            static::$result['msg'] = $res['message'];
            static::$result['code'] = $res['code'];
        }

    }


    public static function callback($request)
    {

        echo '{ “code”:0,“message”:”ok”,“data”:{}}';

        $data = $request->all();

        $payConf = static::getPayConf($data['jOrderId']);
        if (!$payConf) return false;

        $sign = $data['signature'];
        unset($data['signature']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue  = strtoupper(hash_hmac('sha256', $signStr, $payConf['md5_private_key']));

        if ($signTrue == strtoupper($sign) && $data['status'] == '3') {
            static::$amount = $data['amount'];
            return true;
        } else {
            static::addCallbackMsg($request);
            return false;
        }
    }


}