<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zbpay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        if ($payConf['pay_way'] != '9') {
            $data['merchantid']   = $payConf['business_num']; //商户编号
            $data['paytype']      = $bank; //支付方式
            $data['amount']       = number_format($amount, 2); //订单金额
            $data['orderid']      = $order; //订单编号
            $data['notifyurl']    = $ServerUrl; //异步通知地址
            $data['request_time'] = date("YmdHis"); //请求时间

            $signStr            = self::getSignStr($data, true, false);
            $data['sign']       = self::getMd5Sign("{$signStr}", '&key='.$payConf['md5_private_key']);
            $data['returnurl'] = $returnUrl; //同步地址
            $data['israndom']  = 'N'; //启用订单风控
            $data['isqrcode']  = '';
            $data['desc']      = $order; //描述
        } else {

            $data['merchantid']   = $payConf['business_num'];
            $data['amount']       = number_format($amount, 2);
            $data['orderid']      = $order;
            $data['notifyurl']    = $ServerUrl;
            $data['request_time'] = date("YmdHis");

            $signStr            = self::getSignStr($data, true, false);
            $data['sign']       = self::getMd5Sign("{$signStr}", '&key='.$payConf['md5_private_key']);
            $data['returnurl'] = $returnUrl;
            $data['desc']      = $order;
        }
        unset($reqData);
        return $data;
    }
}