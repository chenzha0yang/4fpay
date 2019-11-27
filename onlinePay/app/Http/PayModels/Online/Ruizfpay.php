<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Ruizfpay extends ApiModel
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
     * @param array       $reqData 接口传递的参数
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


        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];

        $data = array(
            'mno'     => $payConf['business_num'],
            'orderno' => $order,
            'amount'  => $amount * 100,
            'pt_id'   => $bank,
            'device'  => '2',  //2:PC  ,1: 移动端
        );
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP    = true;
            $data['device'] = '1';
        }
        if ($payConf['pay_way'] == 1) {
            //网银
            $data['pt_id'] = '3';
        }
        ksort($data);
        $signStr = http_build_query($data);

        $data['sign']             = self::privateEncrypt($signStr, $payConf['rsa_private_key']);
        $data['notify_url']       = $returnUrl;
        $data['async_notify_url'] = $ServerUrl;

        unset($reqData);
        return $data;
    }

    //私钥加密
    public static function privateEncrypt($data, $pi_key)
    {
        $crypto = '';
        foreach (str_split($data, 117) as $chunk) {
            openssl_private_encrypt($chunk, $encryptData, $pi_key);
            $crypto .= $encryptData;
        }
        $encrypted = base64_encode($crypto);
        return $encrypted;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $signStr = http_build_query($data);

        $mySign             = self::privateEncrypt($signStr, $payConf['rsa_private_key']);

        if ($sign == $mySign && $data['status'] == 1) {
            return true;
        } else {
            return false;
        }

    }
}