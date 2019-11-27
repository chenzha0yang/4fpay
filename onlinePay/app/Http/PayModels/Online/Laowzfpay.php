<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Laowzfpay extends ApiModel
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

        //TODO: do something
        self::$unit = 2;

        $data               = [];
        $data['merchNo']    = $payConf['business_num'];
        $data['orderNo']    = $order;
        $data['amount']     = $amount * 100;
        $data['currency']   = 'CNY';
        $data['outChannel'] = $bank;
        $data['bankCode']   = '';
        $data['product']    = 'goodsName';
        $data['memo']       = $amount;
        $data['returnUrl']  = $returnUrl;
        $data['notifyUrl']  = $ServerUrl;
        $data['reqTime']    = date('YmdHis');
        if ($payConf['pay_way'] == '1') {
            $data['outChannel'] = 'YIN';
            $data['bankCode']   = $bank;
        }
        $data['key']  = $payConf['md5_private_key'];
        $data['sign'] = md5(self::getSignStr($data, true, true));
        unset($data['key']);
        unset($reqData);
        return $data;
    }

    //回调金额处理
    public static function getVerifyResult($request)
    {
        $res                = $request->all();
        $data['amount']     = $res['amount'] / 100;
        $data['orderNo']     = $res['orderNo'];
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign        = $data['sign'];
        $data['key'] = $payConf['md5_private_key'];
        unset($data['sign']);
        $mySign = md5(self::getSignStr($data, true, true));
        if (strtoupper($sign) == strtoupper($mySign) && $data['orderState'] == '00') {
            return true;
        } else {
            return false;
        }
    }
}