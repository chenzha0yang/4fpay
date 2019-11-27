<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qihangtpay extends ApiModel
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

        //TODO: do something
        $data            = [];
        $data['apiName'] = 'WEB_PAY_B2C';
        if ($payConf['is_app'] == '1') {
            $data['apiName'] = 'WAP_PAY_B2C';
        }
        $data['apiVersion']    = '1.0.0.0';
        $data['platformID']    = $payConf['business_num'];
        $data['merchNo']       = $payConf['business_num'];
        $data['orderNo']       = $order;
        $data['tradeDate']     = date('Ymd');
        $data['amt']           = sprintf('%.2f', $amount);
        $data['merchUrl']      = $ServerUrl;
        $data['merchParam']    = $amount;
        $data['tradeSummary']  = 'goodsName';
        $str                   = self::getSignStr($data, true, false);
        $data['signMsg']       = md5($str . $payConf['md5_private_key']);
        $data['choosePayType'] = $bank;
        if ($payConf['pay_way'] == '1') {
            $data['choosePayType'] = '1';
            $data['bankCode']      = $bank;
        }
        unset($reqData);
        return $data;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['signMsg'];
        unset($data['signMsg'], $data['notifyType']);
        $str    = self::getSignStr($data, true, false);
        $mySign = md5($str . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($mySign) && $data['orderStatus'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}