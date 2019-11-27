<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bobtpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

    /***
     * @param $reqData
     * @param $payConf
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
        //TODO: do something

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';
        self::$isAPP   = true;
        self::$resType = 'other';

        $data                = [];
        $data['storeCode']   = $payConf['business_num'];//商户号
        $data['payType']     = $bank;//银行编码
        $data['orderCode']   = $order;//订单号
        $data['orderTotal']  = sprintf('%.2f', $amount);//订单金额
        $data['remarks']     = 'shuangyin';
        $data['noticeUrl']   = $ServerUrl;
        $signStr             = $data['storeCode'] . $data['orderCode'] . $data['payType'];
        $data['sign']        = self::getMd5Sign($signStr . "|", $payConf['md5_private_key']);
        $jsonData            = json_encode($data);
        $post['data']        = $jsonData;
        $post['httpHeaders'] = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData));
        $post['orderCode']   = $data['orderCode'];
        $post['orderTotal']  = $data['orderTotal'];
        unset($reqData);
        return $post;
    }

    /***
     * @param $resp
     * @return mixed
     */
    public static function getQrCode($resp)
    {
        $result = json_decode($resp, true);
        if ($result['code'] == '0') {
            $result['payUrl'] = $result['data']['comments'];
        }
        return $result;
    }

    /***
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = $data['seqNo'] . $data['orderCode'] . $data['payType'];
        $signTrue = self::getMd5Sign($signStr . "|", $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        } else {
            return false;
        }
    }
}