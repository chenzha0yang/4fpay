<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Common;


class Yichongzf extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'curl'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = true; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $data = [];

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';


        $data['member_id'] = $payConf['business_num'];//商户号
        $data['submit_order_id'] = $order;
        $data['type_name'] = $bank;
        $data['notify_url'] = $ServerUrl;
        $data['callback_url'] = $returnUrl;
        $data['amount'] = $amount;
        $signStr = self::getSignStr($data, true, true);
        $sign = self::getMd5Sign($signStr.'&key=', $payConf['md5_private_key']);
        $data['sign'] = strtoupper($sign);
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $responseData = json_decode($response,true);

        if(array_key_exists("code",$responseData) && $responseData['code'] == '200'){
            $data['amount'] = $responseData['data']['amount'];
            $data['url'] = $responseData['data']['qrcode'];
            $data['msg'] = $responseData['msg'];
        }
        $data['code'] = $responseData['code'];
        $data['msg'] = $responseData['msg'];
        return $data;
    }



    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $md5sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $sign = self::getMd5Sign($signStr.'&key=', $payConf['md5_private_key']);
        $signTrue = strtoupper($sign);
        if ($signTrue == strtoupper($md5sign) && $data['return_code'] == '0') {
            return true;
        } else {
            return false;
        }
    }
}