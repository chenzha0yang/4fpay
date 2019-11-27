<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Redbullpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';

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
        $order          = $reqData['order'];
        $amount         = $reqData['amount'];
        $bank           = $reqData['bank'];
        $ServerUrl      = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl      = $reqData['returnUrl'];// 同步通知地址
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        self::$method   = 'header';
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$resType  = 'other';
        self::$isAPP    = true;

        $data                 = [];
        $data['brandNo']      = $payConf['business_num'];//商户号
        $data['orderNo']      = $order;//订单号
        $data['price']        = sprintf('%.2f', $amount);//订单金额
        $data['serviceType']  = $bank;//银行编码
        $data['userName']     = self::$UserName;
        $data['clientIP']     = self::getIp();
        $signStr              = self::getSignStr($data, true, true);
        $merchant_private_key = openssl_get_privatekey($payConf['rsa_private_key']);
        openssl_sign($signStr, $sign_info, $merchant_private_key, OPENSSL_ALGO_MD5);
        $data['signature']   = base64_encode($sign_info);
        $data['callbackUrl'] = $ServerUrl;
        $data['frontUrl']    = $returnUrl;
        $data['signType']    = 'RSA-S';
        $jsonData            = json_encode($data);

        $header                  = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']        = $jsonData;
        $postData['httpHeaders'] = $header;
        $postData['orderNo']     = $data['orderNo'];
        $postData['price']       = $data['price'];
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
        if ($result['isSuccess']) {
            $result['payUrl'] = $result['data']['payUrl'];
        }
        return $result;
    }

    /***
     * 回调金额处理
     * @param Request $request
     * @return array
     */
    public static function getVerifyResult($request, $mod)
    {
        $arr  = $request->getContent();
        $data = json_decode($arr, true);
        return $data;
    }

    /**
     * 回调处理
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $datas, $payConf)
    {
        $post = file_get_contents('php://input');
        $data = json_decode($post, true);
        $sign = $data['signature'];
        $sign = urldecode($sign);
        unset($data['signature']);
        unset($data['code']);
        unset($data['message']);
        $data['dealTime'] = date("YmdHis",strtotime($data['dealTime']));
        $data['orderTime'] = date("YmdHis",strtotime($data['orderTime']));
        $signStr = self::getSignStr($data, true, true);
        $pu      = openssl_get_publickey($payConf['public_key']);
        $result  = (bool)openssl_verify($signStr, base64_decode($sign), $pu, OPENSSL_ALGO_MD5);
        openssl_free_key($pu);
        if ($result && $data['orderStatus'] == "1") {
            return true;
        } else {
            return false;
        }
    }
}