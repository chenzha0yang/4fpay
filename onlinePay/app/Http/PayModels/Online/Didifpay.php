<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Didifpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $order         = $reqData['order'];
        $amount        = $reqData['amount'];
        $bank          = $reqData['bank'];
        $ServerUrl     = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl     = $reqData['returnUrl'];// 同步通知地址
        self::$unit    = 2;
        self::$method  = 'header';
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$isAPP   = true;

        $data                    = [];
        $data['mchntCode']       = $payConf['business_num'];
        $data['channelCode']     = $bank;
        $data['ts']              = date('Ymdhis');
        $data['mchntOrderNo']    = $order;
        $data['orderAmount']     = $amount * 100;
        $data['clientIp']        = self::getIp();
        $data['subject']         = 'honor';
        $data['body']            = 'PAY';
        $data['notifyUrl']       = $ServerUrl;
        $data['pageUrl']         = $returnUrl;
        $data['orderTime']       = date('Ymdhis');
        $data['orderExpireTime'] = date('Ymdhis', strtotime("+10 minute"));
        $data['description']     = 'goods';
        $data['extra1']          = '{"notifyVersion":"1.1"}';
        if ($payConf['pay_way'] == "1") {
            $data['channelCode'] = 'gateway_pay';
            $data['bankCode']    = $bank;
        }
        $signStr                  = self::getSignStr($data, true, true);
        $data['sign']             = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data)),
        ];
        $postData['data']         = json_encode($data);
        $postData['httpHeaders']  = $header;
        $postData['mchntOrderNo'] = $data['mchntOrderNo'];
        $postData['orderAmount']  = $data['orderAmount'];
        unset($reqData);
        return $postData;
    }

    /****
     * 返回信息处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['retCode'] == "0000") {
            if (isset($result['codeUrl'])) {
                $result['payUrl'] = $result['codeUrl'];
            } elseif (isset($result['imgSrc'])) {
                $result['payUrl'] = $result['imgSrc'];
            } elseif (isset($result['payUrl'])) {
                $result['payUrl'] = $result['payUrl'];
            }
        }
        return $result;
    }

    /***
     * 回调金额处理
     * @param Request $request
     * @return array
     */
    public static function getVerifyResult(Request $request)
    {
        $data              = $request->all();
        $data['orderAmount'] = $data['orderAmount'] / 100;
        return $data;
    }

    /**
     * 回调处理
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        $sign    = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true,true);
        $signTrue = strtoupper(md5("{$signStr}{$payConf['md5_private_key']}"));
        if (strtoupper($sign) == $signTrue && $data['payResult'] == "1") {
            return true;
        } else {
            return false;
        }
    }
}