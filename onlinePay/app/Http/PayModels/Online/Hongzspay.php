<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;


class Hongzspay extends ApiModel
{


    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false


    public static function getAllInfo($reqData, $payConf)
    {

        /**
         * 参数赋值，方法间传递数组
         */
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$isAPP = true;

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';

        $data['appKey'] = $payConf['business_num'];
        $data['outOrderId'] = $order;
        $data['orderFund'] = $amount;
        $data['callbackUrl'] = $ServerUrl;
        $data['payType'] = $bank;

        $signStr = "appKey=".$data['appKey']."outOrderId=".$data['outOrderId']."orderFund=".$data['orderFund']."callbackUrl=".$data['callbackUrl']."key=".$payConf['md5_private_key'];
        $data['sign']        = strtolower(md5($signStr));
        unset($reqData);
        return $data;
    }

    public static function getQrCode($result)
    {
        $res = json_decode($result, true);
        if ($res['code'] == '0') {
            $res['pcUrl'] = $res['data']['pcUrl'];
        }
        return $res;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        $signStr = "appKey=".$data['appKey']. "outOrderId=".$data['outOrderId']. "orderFund=".$data['orderFund']. "orderId=".$data['orderId']. "realOrderFund=".$data['realOrderFund']."key=".$payConf['md5_private_key'];
        $signTrue = strtolower(md5($signStr));
        if ($sign == $signTrue ) {
            return true;
        } else {
            return false;
        }
    }

}