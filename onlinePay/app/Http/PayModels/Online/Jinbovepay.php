<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jinbovepay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$method = 'get';

        $data['pid']      = $payConf['business_num']; // 商户ID
        $data['type']     = $bank; //支付方式
        if((int)$payConf['pay_way'] === 1){
            $data['card_type'] = 1;
            $data['type'] = 'gateway';
            $data['bank_code'] = $bank;
        }else{
            $data['gateway'] = 'pay030';
        }
        $data['out_trade_no']     = $order; //商户平台唯一订单号
        $data['notify_url'] = $ServerUrl; //商户异步回调通知地址
        $data['return_url'] = $returnUrl;
        $data['name']     = 'Jinbo'; //版本号
        $data['money']    = sprintf('%.2f',$amount); //支付金额

        $string       = self::getSignStr($data,true,true);
        $data['sign']         = md5($string . $payConf['md5_private_key']);
        $data['sign_type']    = 'MD5'; //签名加密方式

        unset($reqData);
        return $data;
    }

    public static function signOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['sign_type']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue = md5($signStr . $payConf['md5_private_key']);
        if (strtoupper($signTrue) == strtoupper($sign) && $data['trade_status'] == 'TRADE_SUCCESS') {
            return true;
        }
        return false;
    }

}