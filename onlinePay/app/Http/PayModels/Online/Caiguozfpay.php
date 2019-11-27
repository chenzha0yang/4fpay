<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Caiguozfpay extends ApiModel
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
        self::$isAPP = true;
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method  = 'header';
        $data['merchantName']      = 'Caiguopay';
        $data['merchantCode']     = $payConf['business_num'];//商户号
        $data['merchantOrderNo']     = $order;//订单号
        $data['payPrice']       = $amount * 100;//订单金额
        $data['payType']     = $bank;//银行编码
        $signStr                 = self::getSignStr($data, true, false);
        $data['sign']            = md5($signStr . "&" . $payConf['md5_private_key']);
        $data['callbackUrl']   = $ServerUrl;
        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['merchantOrderNo'] = $data['merchantOrderNo'];
        $postData['payPrice']  = $data['payPrice'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['result'] == '0') {
            $data['qrCode'] = $data['datas']['payPic'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['payPrice'])) {
            $arr['payPrice'] = $arr['payPrice'] / 100;
        }
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        $signStr  = 'merchantName='.$data['merchantName'].'&merchantOrderNo='.$data['merchantOrderNo'].'&noticeType='.$data['noticeType'].'&payPrice='.$data['payPrice'].'&payType='.$data['payType'];
        $signTrue = md5($signStr . "&" . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['noticeType'] == '1') {
            return true;
        }
        return false;
    }


}