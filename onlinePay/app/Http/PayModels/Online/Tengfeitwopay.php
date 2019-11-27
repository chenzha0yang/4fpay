<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tengfeitwopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $array = [];
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
        //TODO: do something

        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$isAPP = true;
        self::$method  = 'header';

        $data['openid']       = $payConf['business_num'];
        $data['mch_secret']       = $payConf['public_key'];
        $data['nonce_str']       = md5(uniqid(microtime(true),true));
        $data['body']       = 'goods_name';
        $data['out_trade_no'] = $order;
        $data['trade_type']     = $bank;
        $data['total_fee'] = $amount;
        $data['notify_url']   = $ServerUrl;
        $data['device_info']    = 'app';
        $data['time_Stamp']    = (string)time();
        $signStr              = self::getSignStr($data, true, true);
        $data['sign']         =strtoupper(md5($signStr."&key=".$payConf['md5_private_key']));
        $post['data'] = json_encode($data);
        $post['httpHeaders']=[
        "Content-Type:application/json;charset=utf-8",
        "Accept:application/json;charset=utf-8"
            ];
        $post['out_trade_no'] =$order;
        $post['total_fee'] =$amount;
        unset($reqData);
        return $post;
    }

    public static function getVerifyResult($request,$mod)
    {
        $arr = $request->getContent();
        $data =  json_decode($arr,true);
        self::$array = $data;
        return $data;
    }

    public static function SignOther($type, $request, $payConf)
    {
        $data = self::$array;
        $sign = $data['sign'];
        $status = $data['code'];
        unset($data['sign'],$data['txCode'],$data['txMsg'],$data['code'],$data['message']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtoupper(md5($signStr."&key=".$payConf['md5_private_key']));
        if (strtoupper($sign) == $signTrue && $status == '0000') {
            return true;
        } else {
            return false;
        }

    }
}