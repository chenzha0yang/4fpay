<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayMerchant;

class Jinzuanpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signData = [];

    /**
     * @param array       $reqData 接口传递的参数
     * @param PayMerchant $payConf object PayMerchant类型的对象
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

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;

        $data['merchantId']   = $payConf['business_num'];
        $data['timestamp'] = self::msectime();
        $data['signatureMethod'] = 'HmacSHA256';
        $data['signatureVersion'] = '1';

        $data['jUserId'] =isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        $data['jUserIp'] = self::getIp();
        $data['jOrderId'] = $order;
        $data['payWay'] = $bank;
        $data['orderType'] = '1';
        $data['amount'] = $amount;
        $data['currency'] = 'CNY';
        $data['notifyUrl'] = $ServerUrl;
        $data['jExtra'] = '1';

        $signStr =  self::getSignStr($data,true,true);
        $data['signature'] =  strtoupper(hash_hmac('sha256', $signStr, $payConf['md5_private_key'], false));
        unset($reqData);

        return $data;

    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['signature'];
        unset($data['signature']);
        $signStr =  self::getSignStr($data,true,true);
        $mySign =  strtoupper(hash_hmac('sha256', $signStr, $payConf['md5_private_key'], false));
        if ($mySign == strtoupper($sign) && $data['status'] == "3") {
            return true;
        }
        return false;
    }

    //返回当前的毫秒时间戳
    public static function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }


    /**
     * 二维码链接处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){

        $responseData = json_decode($response,true);
        if($responseData['code'] == '0'){
            $responseData['url'] = $responseData['data']['paymentUrl'];
        }

        return $responseData;
    }

}