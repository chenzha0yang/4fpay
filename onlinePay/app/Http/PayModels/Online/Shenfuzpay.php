<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shenfuzpay extends ApiModel
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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        if ( $payConf['is_app'] == 1 ) {
            self::$isAPP = true;
        }
        self::$unit = 2;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        $payInfo = explode('@', $payConf['business_num']);

        if(!isset($payInfo[1])){
            echo '绑定格式有误!请参考：商户id@APPID';exit();
        }

        $data['mch_no']       = $payInfo[0];
        $data['app_id']       = $payInfo[1];
        $data['nonce_str']    = rand(100000, 999999);
        $data['trade_type']   = $bank;
        $data['total_fee']    = $amount * 100;
        $data['body']         = 'goodsName';
        $data['notify_url']   = $ServerUrl;
        $data['front_url']    = $returnUrl;
        $data['out_trade_no'] = $order;

        $signStr = self::getSignStr($data,true,true);
        $data['sign']        = strtoupper(md5($signStr.'&key='.$payConf['md5_private_key']));

        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['out_trade_no'] = $data['out_trade_no'];
        $postData['total_fee']  = $data['total_fee'];
        unset($reqData);
        return $postData;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $data['out_trade_no'] = $res['out_trade_no'];
        $data['total_fee'] = $res['total_fee']/100;
        return $data;
    }
    
    public static function signOther($model, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $sign     = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue = strtoupper(md5($signStr.'&key='.$payConf['md5_private_key']));
        if ($signTrue == strtoupper($sign) && $data['trade_status'] == 'SUCCESS') {
            return true;
        }
        return false;
    }

}