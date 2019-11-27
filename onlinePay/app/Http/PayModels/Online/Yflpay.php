<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Yflpay extends ApiModel
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

        // TODO SOMETHING
        $data['merchantId']    = $payConf['business_num'];//商户号
        $data['orderNo']       = $order;//订单
        $data['orderAmount']   = $amount * 100;
        $data['orderDatetime'] = date('YmdHis');//订单时间
        $data['notifyUrl']     = $ServerUrl;
        $data['signType']      = 'MD5';
        if ($payConf['pay_way'] == '1') {
            $data['version'] = '1.0.1';//版本号
            $data['bankId']  = $bank;//银行号
            $data['payType'] = 'wgpay';//支付类型
            $data['pageUrl'] = $returnUrl;
        } else if ($payConf['pay_way'] == '9') {
            $data['version'] = '1.0.2';//版本号
            $data['payType'] = $bank;//支付类型
            $data['pageUrl'] = $returnUrl;
        } else if ($payConf['is_app'] != '1' && $payConf['pay_way'] != '9' && $payConf['pay_way'] != '1') {
            $data['version'] = '1.0.0';//版本号
            $data['payType'] = $bank;//支付类型
        } else if ($payConf['is_app'] == '1' && $payConf['pay_way'] != '9' && $payConf['pay_way'] != '1') {
            $data['version'] = '1.0.2';//版本号
            $data['payType'] = $bank;//支付类型
            $data['pageUrl'] = $returnUrl;
        }
        $signStr      = self::getSignStr($data, true, true);
        $sign         = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        $data['sign'] = $sign;//签名
        if ($payConf['pay_way'] != '1' && $payConf['pay_way'] != '9') {
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$unit    = 2;
        }
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult(Request $request, $mod)
    {
        $data  = $request->all();
        $order = $data['orderNo'];
        $mount = $data['orderAmount'] / 100;
        return ['orderNo' => $order, 'orderAmount' => $mount];
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }
}