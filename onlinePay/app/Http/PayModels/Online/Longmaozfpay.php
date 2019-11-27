<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Longmaozfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $changeUrl = false;

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

        $payInfo = explode('@', $payConf['business_num']);
        if(!isset($payInfo[1])){
            echo '绑定格式错误！请参考: 商户号@APP_KEY';exit();
        }

        self::$isAPP = true;
        self::$unit = 2;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';
        self::$changeUrl = true;

        $getData['app_key'] = $data['app_key'] = $payInfo[1];
        $getData['v'] = $data['v'] = '1';
        $getData['timestamp'] = $data['timestamp'] = time();

        $postJsonData['code'] = $data['code'] = $payInfo[0];
        $postJsonData['amount'] = $data['amount']       = $amount*100;
        $postJsonData['shopOutTradeId'] = $data['shopOutTradeId']   = $order;
        $postJsonData['callbackUrl'] = $data['callbackUrl'] = $ServerUrl;
        $postJsonData['channleType'] = $data['channleType'] = (int)$bank;

        $signStr = self::getSignStr($data,true,true);
        $getData['sign'] = $data['sign']        = md5($signStr.$payConf['md5_private_key']);

        $jsonData = json_encode($postJsonData);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['queryUrl'] = $reqData['formUrl'].'?'.http_build_query($getData);
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['shopOutTradeId'] = $data['shopOutTradeId'];
        $postData['amount']  = $data['amount'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['resultCode'] == '10000') {
            $data['qrCode'] = $data['data']['url'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $data['shopOutTradeId'] = $res['shopOutTradeId'];
        $data['amount'] = $res['amount']/100;
        return $data;
    }
    
    public static function signOther($model, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $sign     = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue = strtoupper(md5($signStr.$payConf['md5_private_key']));
        if ($signTrue == strtoupper($sign) && $data['orderStatus'] == 3) {
            return true;
        }
        return false;
    }

}