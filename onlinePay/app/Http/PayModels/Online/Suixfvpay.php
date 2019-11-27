<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Suixfvpay extends ApiModel
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
        $data                = [];
        $data['version'] = '1.0';
        $data['customerid']   = $payConf['business_num'];  // 商户号
        $data['sdorderno']  = $order;
        $data['total_fee']      = sprintf("%.2f", $amount);  // 支付金额
        $data['returnurl']   = $returnUrl;
        $data['notifyurl']   = $ServerUrl;

        $signStr             = self::getSignStr($data, true, true);
        $sign                = strtoupper(self::getMd5Sign($signStr.'&key=', $payConf['md5_private_key']));
        $data['sign']        = $sign;  // 签名
        $data['paytype'] = $bank ? $bank : '';
        
        if ($data['paytype'] == 'exemption') {
            $data['get_code'] = 1;
        }
        self::$reqType       = 'curl';
        self::$resType       = 'other';
        self::$payWay       = $payConf['pay_way'];
        if ($payConf['is_app'] == 1) {
           self::$isAPP = true;
        }
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['code'] == 1) {
            $result['payUrl'] = $result['pay_url'];
        }
        return $result;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $sign    = strtoupper($data['sign']);
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $mySign  = strtoupper(self::getMd5Sign($signStr.'&key=', $payConf['md5_private_key']));
        if ($mySign == $sign && $data['status'] == 1) {
            return true;
        } else {
            return false;
        }
    }
}