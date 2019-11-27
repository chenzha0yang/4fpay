<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xingshidaipay extends ApiModel
{

    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $changeUrl = ''; //自定义请求地址

    public static $amount = 0; // callback  amount

    /**
     * @param $reqData
     * @param $payConf
     * @return array
     */
    public static function getData($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $data                = [];
        $data['shid']      = $payConf['business_num'];//商户ID
        $data['ordernumber'] = $order;
        $data['amount'] = sprintf('%.2f', $amount);
        $data['pay'] = $bank;
        $data['return_url'] = $ServerUrl;

        $signStr             = "shid={$data['shid']}&amount={$data['amount']}&ordernumber={$data['ordernumber']}&pay={$data['pay']}&return_url={$data['return_url']}";
        $data['sign']        = self::getMd5Sign("{$signStr}&", $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }


    public static function callback($request)
    {
        echo '付款成功'; // 接收callback 即同步返回 success msg

        $data = $request->all();
        if (!$payConf = self::getPayConf($data['ordernum'])) return false;
        $sign = $data['sign'];
        $md5 = strtoupper(md5('status='.$data['status'].'&ordernum='.$data['ordernum'].'&price='.$data['price'].'&' . $payConf['md5_private_key']));
        if ($md5 == strtoupper($sign) && $data['status'] == 3) {
            static::$amount = $data['price']; // 下发金额设置 （注意单位）
            return true;
        } else {
            self::addCallbackMsg($request);
            return false;
        }
    }

}