<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bolezfpay extends ApiModel
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

        $data['SHid'] = $payConf['business_num'];//商户号
        $data['orderNo'] = $order;//订单号
        $data['payMoney'] = $amount;//订单金额
        $data['orderUid'] = $order;
        $data['notifyUrl'] = $ServerUrl;
        $signStr =  'notifyUrl='.$data['notifyUrl'].'&orderNo='.$data['orderNo'].'&orderUid='.$data['orderUid'].'&payMoney='.$data['payMoney'].'&SHid='.$data['SHid'];
        $data['sign'] = md5($signStr . "&md5Key=" . $payConf['md5_private_key']);

        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$isAPP   = true;
        self::$payWay  = $payConf['pay_way'];
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['status'] == 'OK') {
            $data['qrCode'] = $data['data']['payUrl'];
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        $signStr  = 'orderNo='.$data['orderNo'].'&payMoney='.$data['payMoney'].'&shOrderNo='.$data['shOrderNo'];
        $signTrue = md5($signStr . "&key=" . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }


}