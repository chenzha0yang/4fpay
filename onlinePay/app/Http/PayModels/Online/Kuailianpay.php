<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Kuailianpay extends ApiModel
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

        //TODO: do something

        self::$reqType  = 'curl';
        self::$method   = 'header';
        self::$payWay   = $payConf['pay_way'];
        self::$postType = true;
        self::$unit     = 2;

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if($payConf['pay_way'] == '1'||$payConf['pay_way'] == '9' || $payConf['is_app'] == '1'){
            self::$isAPP = true;
        }

        $data['merchantNo'] = $payConf['business_num'];
        if ($payConf['pay_way'] == 1) {
            $data['bankName'] = $bank;
            $data['payType']  = '11';
        } else {
            $data['payType']  = $bank;
        }
        $data['currencyType'] = 'CNY';
        $data['cardType']     = '01';
        $data['productName']  = 'PAY';
        $data['orderAmount']  = $amount * 100;
        $data['orderNo']      = $order;
        $data['notifyUrl']    = $ServerUrl;
        $data['callbackUrl']  = $returnUrl;
        $signStr              = self::getSignStr($data, true, true);
        $data['sign']         = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);

        $json                = json_encode($data);
        $post['data']        = $json;
        $post['orderNo']     = $order;
        $post['orderAmount'] = $amount * 100;
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($json)
        );

        unset($reqData);
        return $post;
    }

    public static function getVerifyResult($request,$mod)
    {
        $arr['orderAmount'] = $request['orderAmount'] / 100;
        return $arr;
    }

    public static function getRequestByType($data)
    {
        $json = $data['data'];
        return $json;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $arr = json_decode($data,true);
        $sign = $arr['sign'];
        $mySign = MD5($payConf['business_num'] . $arr['user_id'] . $arr['order_no'] . $payConf['md5_private_key'] . $arr['money'] . $arr['type']);
        if ($sign == $mySign && $arr['status'] == 0) {
            return true;
        } else {
            return false;
        }
    }

}