<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xindongpay extends ApiModel
{

    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $changeUrl = ''; //自定义请求地址

    public static $amount = 0; // callback  amount


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

        $data                = [];
        $data['m_id']      = $payConf['business_num'];//商户ID
        $data['accounts']        = $payConf['business_num'];//银行类型
        $data['order_id']     = $order;
        $data['amount']     = $amount*100;
        $data['m']     = 1;
        $data['order_ip']     = self::getIp();
        $data['r']     = self::randStr(32);
        $data['return_url']     = urlencode($returnUrl);
        $data['notify_url']     = urlencode($ServerUrl);
        $signStr          = self::getSignStr($data, true, true);
        $data['sign']     = md5($signStr.'&'.$payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }


    public static function callback($request)
    {

        echo 'ok';

        $data = $request->all();

        $payConf = static::getPayConf($data['order_id']);
        if (!$payConf) return false;

        $sign = $data['sign'];
        $code = $data['code'];
        unset($data['sign'],$data['code']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue  = md5($signStr.'&'.$payConf['md5_private_key']);

        if ($signTrue == $sign && $code == '000000') {
            static::$amount = $data['amount']/100;  //注意转换成元
            return true;
        } else {
            static::addCallbackMsg($request);
            return false;
        }
    }

}