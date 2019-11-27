<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;


class Hengjiupay extends ApiModel
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


        $data = [];
        $data['channel'] = $payConf['business_num'];//商户ID
        $data['callback'] = $ServerUrl;
        $data['orderid'] = $order;
        $data['txnAmt'] = $amount;
        $data['paytype'] = $bank;
        $data['ip'] = '112.232.23.456';//如果获取到的是内网IP，三方不让内网IP提交，所以写死
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtolower(md5($signStr . "&key=" . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign'], $data['resultMsg']);
        $signStr = self::getSignStr($data, true, true);
        $signTrue = strtolower(md5($signStr . "&key=" . $payConf['md5_private_key']));
        if ($signTrue == $sign && $data['respCode'] == '0000') {
            return true;
        } else {
            return false;
        }
    }
}