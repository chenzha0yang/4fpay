<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;


class Juhepay extends ApiModel
{

    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $amount = 0; // callback  amount

    public static $changeUrl = ''; //自定义请求地址


    public static function getData($reqData, $payConf)
    {
        static::$action = 'formPost';

        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址


        $data['pay_memberid'] = $payConf['business_num'];
        $data['pay_orderid'] = $order;
        $data['pay_applydate'] = date("Y-m-d H:i:s");
        $data['pay_bankcode'] = $bank;
        $data['pay_notifyurl'] = $ServerUrl;
        $data['pay_callbackurl'] = $returnUrl;
        $data['pay_amount'] = $amount;
        $signStr = self::getSignStr($data,true,true);
        $data['pay_md5sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key'] ));
        $data['pay_productname'] = 'goods_name';

        unset($reqData);
        return $data;
    }

    public static function callback($request)
    {
        echo 'OK';
        $data = $request->all();

        $payConf = static::getPayConf($data['orderNo']);
        if (!$payConf) return false;

        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key'] ));
        if ($sign == $signTrue && $data['returncode'] == '00') {
            static::$amount = $data['amount'];
            return true;
        } else {
            static::addCallbackMsg($request, $data['amount']);
            return false;
        }
    }

}