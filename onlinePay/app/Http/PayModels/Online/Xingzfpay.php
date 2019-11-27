<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xingzfpay extends ApiModel
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
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        $data['merchantCode']    = $payConf['business_num'];
        $data['merchantOrderId'] = $order;
        $data['tradeAmount']     = $amount;
        $data['tradeTime']       = date("Y-m-d H:i:s");
        $data['payType']         = $bank;
        $data['goods']           = 'xg';
        $data['notifUrl']        = $ServerUrl;
        $data['returnUrl']       = $returnUrl;
        $signStr                 = stripslashes(self::jsonencode($data, $payConf['md5_private_key']));
        $data['sign']            = strtoupper(md5($signStr));

        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['merchantOrderId'] = $data['merchantOrderId'];
        $postData['tradeAmount']  = $data['tradeAmount'];

        unset($reqData);
        return $postData;
    }

    public static function jsonencode($data, $key)
    {
        ksort($data);
        $data['key'] = $key;
        return json_encode($data);
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '200') {
            $data['qrCode'] = $data['obj']['payUrl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $data  = $res['obj'];
        return $data;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $dataArr=json_decode($json,true);

        $data  = $dataArr['obj'];

        $sign = $data['sign'];
        unset($data['sign']);

        $signStr = stripslashes(self::jsonencode($data, $payConf['md5_private_key']));
        $signTrue  = strtoupper(md5($signStr));

        if (strtoupper($sign) == strtoupper($signTrue)  && $data['code'] == '200') {
            return true;
        }
        return false;
    }


}