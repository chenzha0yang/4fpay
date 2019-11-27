<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Pingguopay extends ApiModel
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
        $data['mch_id']      = $payConf['business_num'];//商户ID
        $data['out_trade_no']     = $order;
        $data['total_fee']     = $amount*100;
        $data['trade_type']     = $bank;
        $data['ip']     = self::getIp();
        $data['callback_url']     = $returnUrl;
        $data['notify_url'] = $ServerUrl;//下行异步通知地址
        $data['sign'] = md5($data['mch_id'].$data['out_trade_no'].$data['total_fee'].$data['notify_url'].$payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }


    public static function getQrCode($result)
    {
        $res = json_decode($result, true);
        if ($res['status'] == '1') {
            static::$result['appPath'] = $res['data']['payurl'];
            self::$pc = true;
        } else {
            static::$result['msg'] = $res['error'];
            static::$result['code'] = $res['status'];
        }

    }


    public static function callback($request)
    {
        echo 'success';

        $json = $request->getContent();
        $data = json_decode($json,true);
        $payConf = static::getPayConf($data['out_trade_no']);
        if (!$payConf) return false;

        $sign = $data['sign'];
        $signTrue = md5($data['result_code'].$data['mch_id'].$data['out_trade_no'].$data['transaction_id'].$data['total_fee'].$payConf['md5_private_key']);
        if ($signTrue == $sign && $data['result_code'] == 'SUCCESS') {
            static::$amount = $data['total_fee']/100;  //注意转换成元
            return true;
        } else {
            static::addCallbackMsg($request);
            return false;
        }
    }


}