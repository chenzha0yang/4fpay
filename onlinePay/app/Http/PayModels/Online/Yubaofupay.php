<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yubaofupay extends ApiModel
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
        self::$isAPP = true;
        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;

        $data['merNo']     = $payConf['business_num'];//商户号
        $data['money']       = $amount;//订单金额
        $data['orderId']     = $order;//订单号
        $data['payId']     = $bank;//银行编码
        $data['orderTime']   = date('YmdHis',time());
        $data['items']      = 'Yubaofu';
        $data['ip'] = self::getIp();
        $data['asyncNotifyUrl']   = $ServerUrl;
        $data['syncRedirectUrl'] = $returnUrl;
        $data['remark'] = 'Yubaofu';
        $signStr                 = self::getSignStr($data, true, true);
        $data['sign']            = md5($signStr . $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['returnCode'] == '0') {
            $data['qrCode'] = $data['content'];
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = md5($signStr . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['orderStatus'] == 's') {
            return true;
        }
        return false;
    }


}