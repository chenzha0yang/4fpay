<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jfbvpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $payInfo                   = explode("@", $payConf['pay_id']);
        if(!isset($payInfo['1'])){
            echo '绑定格式错误！请参考：商户号@appid';exit();
        }

        $data['mch_id']       = $payInfo['0']; #商户号
        $data['appid']        = $payInfo['1']; #APPID
        $data['nonce_str']    = self::randStr();
        $data['sign_type']    = 'MD5';
        $data['timestamp']    = date('YmdHis'); #下单时间
        $data['out_trade_no'] = $order; #商户订单号
        $data['title']        = "honor"; #商品名称
        $data['pay_type']     = $bank;
        $data['total_fee']    = sprintf('%.2f', $amount);
        $data['remarkj']      = sprintf('%.2f', $amount);
        $data['notify_url']   = $ServerUrl; #通知地址
        $signStr              = self::getSignStr($data, false, true);
        $data['sign']         = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '0') {
            $data['qrCode'] = $data['data']['pay_url'];
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);

        ksort($data);
        $buff = "";
        foreach ($data as $x => $x_value) {
            if ($x != "sign" && $x_value != "" && !is_array($x_value)) {
                $buff .= $x . "=" . $x_value . "&";
            }
        }
        $string = $buff . "key=" . $payConf['md5_private_key'];
        $signTrue   = strtoupper(md5($string));

        if (strtoupper($sign) == strtoupper($signTrue)  && $data['trade_status'] == '1') {
            return true;
        }
        return false;
    }

     //随机数
    public static function randStr($length = 30)
    {
        $chars    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

}