<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yijifenpay extends ApiModel
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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地


        $data['price'] = number_format($amount, 2, '.', ''); //支付金额 元 两位小数
        $data['paytype'] = $bank; //类型请自行调整
        $data['notify_url'] = $ServerUrl; //这个是订单回调地址，成功付款后定时通知队列会调这个地址。
        $data['return_url'] = $returnUrl; //这个是订单回调地址，成功付款后实时跳回这个地址。
        $data['orderno'] = $order; //订单号

        ksort($data);
        $signStr = '';
        foreach ( $data as $key => $value ) {
            $signStr .= $value;
        }
        $data['uid'] = $payConf['business_num']; //商户ID，请自行调整
        $data['sign'] = strtolower(md5($signStr.$payConf['md5_private_key'].$payConf['business_num']));

        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        $data['token']=$payConf['md5_private_key'];
        unset($data['sign']);
        ksort($data);
        $signStr = '';
        foreach ( $data as $key => $value ) {
            $signStr .= $value;
        }
        $signTrue = md5($signStr);
        if (strtolower($sign) == strtolower($signTrue)) {
            return true;
        }
        return false;
    }
}