<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinzhifupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址


        self::$unit = 2;
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$resType        = 'other';
        self::$method         = 'header';

        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        $data                = array();
        $data['body']        = "buy";
        $data['requestIp']   = "127.0.0.1";
        $data['payTypeKey']  = $bank;
        $data['tradeNo']     = $payConf['business_num'];
        $data['outTradeNo']  = $order;
        $data['totalFee']    = $amount * 100;
        $data['nonceStr']    = "123456";
        $data['payIdentity'] = time();
        $data['notifyUrl']   = $ServerUrl;
        $params              = $data;
        $sign_params         = $params;
        $signStr             = self::getSignStr($params,true,true);
        $sign                = strtoupper(self::getMd5Sign("{$signStr}", "&key=" . $payConf['md5_private_key']));
        $sign_params['sign'] = $sign;
        $post_data           = json_encode($sign_params);
        $header              = array('Content-Type: application/json; charset=utf-8', 'Content-Length:' . strlen($post_data));
        $post['data']        = $post_data;
        $post['httpHeaders'] = $header;
        $post['outTradeNo']  = $data['outTradeNo'];
        $post['totalFee']    = $data['totalFee'];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($response){
        header("Content-type:text/html;charset=utf-8");
        $result = json_decode($response, true);
        if ($result['returnCode'] == "SUCCESS") {
            $result['redirectUrl'] = $result['redirectUrl'];
            $result['codeUrl'] = $result['codeUrl'];
        }
        return $result;
    }

    public static function getVerifyResult($request){
        $result = $request->all();
        $result['total_fee'] = $result['total_fee']/100;
        return $result;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr             = self::getSignStr($data,true,true);
        $mySign    = strtoupper(self::getMd5Sign("{$signStr}", "&key=" . $payConf['md5_private_key']));
        if($sign == $mySign && $data['trade_state'] == 'SUCCESS'){
            return true;
        }else{
            return false;
        }
    }
}