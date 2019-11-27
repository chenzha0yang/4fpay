<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yilianpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str/other

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    //回调数据
    public static $requestData = null;

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        $data = array(
            "inputCharset" => '1',
            "partnerId" => $payConf['business_num'],
            "notifyUrl" => $ServerUrl,
            "returnUrl" => $returnUrl,
            "orderNo" => $order,
            "orderAmount" => $amount*100,
            "orderCurrency" => '156',
            "orderDatetime" => date("YmdHis",strtotime(" +12 hour")),
            "payMode" => '2',
            "isPhone" =>'0',
            "subject" => 'test',
            "body" => 'jjk',
            "extraCommonParam" => 'phone'
        );
        $signStr = self::getSignStr($data, false, true);
        $sign = self::getRsaSign("{$signStr}", $payConf['rsa_private_key']);
        $data['signMsg'] = $sign;//	签名
        $data['signType'] = '1';
        unset($reqData);
        return $data;

    }

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        self::$requestData = $request->all();
        $result = trans("backField.{$mod}");
        $res['order']  = $request->$result['order'];
        $res['amount'] = $request->$result['amount']/100;
        return $res;
    }

    /**
     * @param $type
     * @param $sign
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $sign, $payConf)
    {
        $data = self::$requestData;
        $signMsg = $data['signMsg'];
        if(isset($data['signType'])){
            unset($data['signType']);
        }
        if(isset($data['signMsg'])){
            unset($data['signMsg']);
        }
        $signStr = self::getSignStr($data, false, true);
        $res = openssl_get_publickey($payConf['public_key']);
        $result = (bool)openssl_verify($signStr, base64_decode($signMsg), $res,OPENSSL_ALGO_SHA1);
        openssl_free_key($res);
        if($result && isset($data["payResult"]) && $data["payResult"] == "1"){
            return true;
        }
        self::$requestData = null;

        return false;

    }
}