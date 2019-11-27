<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wandapay extends ApiModel
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
        $data['service']        = $bank;
        $data['out_trade_no']        = $order;
        $data['trade_time']        = date('Ymdhis');
        $data['subject']        = 'qaq';
        $data['body']        = 'nick';
        $data['total_fee']        = $amount;
        $data['spbill_create_ip']        = self::getIp();
        $data['notify_url']        = $ServerUrl;
        $data['return_url']        = $returnUrl;
        $data['sign_type']        = 'MD5';
        $data['trade_type']        = 'NATIVE';
        if($payConf['pay_way'] == 3){
            $data['trade_type']        = 'H5';
        };

        $signStr      = self::getSignStr($data, true,true);
        $data['sign'] = strtoupper(md5($signStr."&key=".$payConf['md5_private_key'])); //MD5签名
        unset($reqData);
        return $data;
    }


    public static function getQrCode($result)
    {
        $res = json_decode($result, true);
        if ($res['return_code'] == 'success') {
            static::$result['appPath'] = $res['mweb_url'];
            self::$pc = true;
        } else {
            static::$result['msg'] = $res['err_msg'];
            static::$result['code'] = $res['err_code'];
        }

    }


    public static function callback($request)
    {

        echo 'success';

        $data = $request->all();

        $payConf = static::getPayConf($data['out_trade_no']);
        if (!$payConf) return false;

        $sign = $data['sign'];
        unset($data['sign']);
        $signStr      = self::getSignStr($data, true,true);
        $signTrue = strtoupper(md5($signStr."&key=".$payConf['md5_private_key'])); //MD5签名

        if ($signTrue == strtoupper($sign) && $data['result_code'] == 'success') {
            static::$amount = $data['total_fee'];  //注意转换成元
            return true;
        } else {
            static::addCallbackMsg($request);
            return false;
        }
    }


}