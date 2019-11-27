<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tenglongpay extends ApiModel
{

    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $changeUrl = ''; //自定义请求地址

    public static $amount = 0; // callback  amount


    public static function getData($reqData, $payConf)
    {
        static::$action = 'curlPost';

        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $data                = [];
        $data['merchantId']      = $payConf['business_num'];//商户ID
        $data['totalAmount']    = sprintf('%.2f',$amount);
        $data['tradeNo']     = $order;
        $data['model']     = $bank;
        $data['notifyUrl']     = $ServerUrl;

        $signStr          = self::getSignStr($data, true, true,'');
        $data['sign']     = md5($signStr.$payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }


    public static function getQrCode($result)
    {
        $res = json_decode($result, true);
        if ($res['Result'] == true && $res['Code'] == '2000') {
            static::$result['appPath'] = $res['Msg'];
            self::$pc = true;
        } else {
            static::$result['msg'] = $res['Msg'];
            static::$result['code'] = $res['Code'];
        }

    }

    public static function callback($request)
    {

        echo 'suc';

        $data = $request->all();

        $payConf = static::getPayConf($data['downNo']);
        if (!$payConf) return false;

        $sign = $data['sign'];
        unset($data['sign']);
        $signStr          = self::getSignStr($data, true, true,'');
        $signTrue     = md5($signStr.$payConf['md5_private_key']);

        if ($signTrue == $sign && $data['Code'] == '2000') {
            static::$amount = $data['realAmount'];  //注意转换成元
            return true;
        } else {
            static::addCallbackMsg($request);
            return false;
        }
    }

}