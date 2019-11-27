<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yicaitongpay extends ApiModel
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
        self::$unit = 2;
        if ($payConf['pay_way'] == '1') {
            $data['pay_type'] = 'gateway';
        } else {
            $data['pay_type'] = $bank;
        }
        $data['payment_id']   = $order;
        $data['app_id']       = $payConf['business_num'];
        $data['total_amount'] = $amount * 100;
        $data['notify_url']   = $ServerUrl;
        $data['subject']      = $order;
        $signStr              = self::getSignStr($data, true, true);
        $data['sign']         = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        if ($payConf['pay_way'] == '1') {
            $data['bankCode'] = $bank;
        }
        $data['return_url'] = $returnUrl;
        if ($payConf['is_app'] == 2 || $payConf['pay_way'] == 9 || $payConf['pay_way'] == '1') {
            self::$reqType = 'form';
        } else {
            self::$reqType       = 'curl';
            self::$payWay        = $payConf['pay_way'];
            self::$method        = 'header';
            $data['httpHeaders'] = 'Content-Type:application/json;charset=utf-8';
            $data['data']        = json_encode($data);
        }
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request)
    {
        $json                = $request->getContent();
        $res                 = json_decode($json, true);
        $res['total_amount'] = $res['total_amount'] / 100;
        return $res;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        if ($sign == $signTrue && $data['status'] == '1') {
            return true;
        }
        return false;
    }
}