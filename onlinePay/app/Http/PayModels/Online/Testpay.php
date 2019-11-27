<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Testpay extends ApiModel
{

    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $changeUrl = ''; //自定义请求地址

    public static $amount = 0; // callback  amount


    public static function getData($reqData, $payConf)
    {
        static::$action = 'formGet';

        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $data                = [];
        $data['parter']      = $payConf['business_num'];//商户ID
        $data['type']        = $bank;//银行类型
        $data['value']       = $amount;//金额
        $data['orderid']     = $order;//商户订单号
        $data['callbackurl'] = $ServerUrl;//下行异步通知地址

        $signStr             = "parter={$data['parter']}&type={$data['type']}&value={$data['value']}&orderid={$data['orderid']}&callbackurl={$data['callbackurl']}";
        $data['sign']        = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }


    public static function getQrCode($result)
    {
        $res = json_decode($result, true);
        if ($res['respCode'] == '00') {
            static::$result['appPath'] = $res['payUrl'];
            self::$pc = true;
        } else {
            static::$result['msg'] = $res['respMsg'];
            static::$result['code'] = $res['respCode'];
        }

    }


    public static function callback($request)
    {

        echo 'success';

        $data = $request->all();

        $payConf = static::getPayConf($data['orderNo']);
        if (!$payConf) return false;

        $sign = $data['signature'];
        unset($data['signature']);
        ksort($data);
        $md5 = strtoupper(md5(json_encode($data, 320) . $payConf['md5_private_key']));

        if ($md5 == strtoupper($sign) && $data['status'] == 'ok') {
            static::$amount = $data['totalAmount'];  //注意转换成元
            return true;
        } else {
            static::addCallbackMsg($request);
            return false;
        }
    }

    //自定义的请求数据
    public static function other()
    {

    }

}