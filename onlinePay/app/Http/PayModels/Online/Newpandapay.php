<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Newpandapay extends ApiModel
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

        //TODO: do something
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data['merId']  = $payConf['business_num'];
        $data['orderId']  = $order;
        $data['orderAmt']  = $amount;
        $data['channel']  = $bank;
        $data['desc']  = 'wahaha';
        $data['attch'] = 'qrq';
        $data['smstyle'] = '1';
        $data['userId'] = 'aaa';
        $data['ip']  = self::getIp();
        $data['notifyUrl']  = $ServerUrl;
        $data['returnUrl']  = $returnUrl;
        $data['nonceStr']  = md5(time() . mt_rand(10000,99999));
        $signStr = self::getSignStr($data, true, true);
        $keys = strtoupper(md5($signStr."&key=".$payConf['md5_private_key']));
        $pay_key = openssl_pkey_get_private($payConf['rsa_private_key']);
        openssl_sign($keys, $sign, $pay_key,OPENSSL_ALGO_SHA256);
        $data['sign']     = base64_encode($sign);
        unset($reqData);
        return $data;
    }

    public static function getQrCode($req)
    {
        $data = json_decode($req, true);
        if($data['code'] == '1'){
            $data['payurl'] = $data['data']['payurl'];
        }
        return $data;
    }



    public static function signOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $keys = strtoupper(md5($signStr."&key=".$payConf['md5_private_key']));
        $pub_key = openssl_get_publickey($payConf['public_key']);
        $res = openssl_verify($keys, base64_decode($sign), $pub_key, OPENSSL_ALGO_SHA256);
        if ($res  == 1) {
            return true;
        } else {
            return false;
        }
    }

}