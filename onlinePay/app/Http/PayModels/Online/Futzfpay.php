<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Futzfpay extends ApiModel
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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';

        $data                  = [];
        $payInfo               = explode('@', $payConf['business_num']);
        $data['merchant_code'] = $payInfo[0];//商户号
        $data['appno_no']      = $payInfo[1];
        if ($payConf['pay_way'] == 1) {
            $data['bank_code'] = $bank;
            $data['pay_type']  = 'wangguan';
        } else {
            $data['pay_type'] = $bank;//银行编码
        }
        $data['order_no']     = $order;//订单号
        $data['order_amount'] = sprintf('%.2f', $amount);//订单金额
        $data['order_time']   = date('YmdHis', time());
        $data['product_name'] = 'test';
        $data['product_code'] = '111';
        $data['user_no']      = time();
        $data['return_url']   = $returnUrl;
        $data['notify_url']   = $ServerUrl;
        if ($payConf['pay_way'] == 2 && $payConf['is_app'] == 1) {
            $data['merchant_ip'] = self::getIp();
        }
        $signStr                  = self::getSignStr($data, true, true);
        $sign                     = strtoupper(self::getMd5Sign($signStr . "&key=", $payConf['md5_private_key']));
        $post['transdata']        = urlencode(json_encode($data));
        $post['signtype']         = 'MD5';
        $post['sign']             = urlencode($sign);
        $postData['data']         = json_encode($post);
        $postData['order_no']     = $data['order_no'];
        $postData['order_amount'] = $data['order_amount'];
        $postData['httpHeaders']  = [
            'Content-Type: application/json; charset=utf-8'
        ];
        unset($reqData);
        return $postData;
    }

    //回调金额化分为元
    public static function getVerifyResult($request)
    {
        $json      = $request->getContent();
        $data      = json_decode($json, true);
        $tranSdaTa = json_decode(urldecode($data['transdata']), true);
        return $tranSdaTa;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $post      = file_get_contents('php://input');
        $data      = json_decode($post, true);
        $tranSdaTa = json_decode(urldecode($data['transdata']), true);
        $sign      = urldecode($data['sign']);
        $signStr   = self::getSignStr($tranSdaTa, true, true);
        $signTrue  = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));
        if ($sign == $signTrue) {
            return true;
        }
        return false;
    }


}