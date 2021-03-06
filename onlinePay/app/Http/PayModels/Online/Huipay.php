<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huipay extends ApiModel
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
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$isAPP = true;

        $data['appKey']      = $payConf['business_num'];//商户号
        $data['payType']     = $bank;//银行编码
        $data['orderCode']   = $order;//订单号
        $data['price']       = sprintf('%.2f', $amount);//订单金额
        $data['frontUrl']    = $returnUrl;
        $data['callback']    = $ServerUrl;
        $data['productName'] = 'test';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            $data['payMode'] = 'H5';
        } else {
            $data['payMode'] = 'SCAN';
        }

        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($payConf['md5_private_key'] . $signStr));
        unset($reqData);
        return $data;
    }


    public static function getQrCode($res)
    {
        $return = [];
        $data = json_decode($res,true);
        if (array_key_exists("code",$data) && $data['code'] != 1) {
            $return = $data;
        } else {
            $return['code'] = isset($data['code'])?$data['code']:'404';
            $return['msg'] = isset($data['msg'])?$data['msg']:'没有提示';
            $return['qrcode'] = json_decode($data['content'],true)['qrcode'];
        }
        return $return;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr =  self::getSignStr($data);
        $signTrue = strtoupper(md5($payConf['md5_private_key'].$signStr));
        if (strtoupper($sign) == $signTrue ) {
            return true;
        } else {
            return false;
        }
    }
}