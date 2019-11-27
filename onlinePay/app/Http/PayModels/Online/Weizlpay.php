<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Weizlpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /*  回调数据  */
    public static $reqData = [];

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

        $data              = [];
        $data['gymchtId'] = $payConf['business_num'];
        $data['tradeSn'] = $order;
        $data['orderAmount'] = (int)$amount*100;
        $data['goodsName'] = 'wzf';
        $data['notifyUrl'] = $ServerUrl;
        $data['expirySecond'] = '';
        if($payConf['pay_way'] == '1'){
            $data['bankSegment'] = $bank;//银行代号
            $data['cardType'] = '01';//,01-借记卡
            $data['callbackUrl'] = $returnUrl;
            $data['channelType'] = '1';
            $data['nonce'] = md5(time());//随机字符串;
        }else{
            $data['tradeSource'] = $bank;
        }
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}", "&key=".$payConf['md5_private_key']));
        self::$reqType = 'curl';
        self::$unit = 2;
        self::$httpBuildQuery = true;
        if ($payConf['is_app'] == '1' || $payConf['pay_way'] == '1') {
           self::$isAPP = true;
           self::$resType = 'other';
        }

        unset($reqData);
        return $data;
    }

    public static function getQrCode($res)
    {
        if($res['resultCode'] == '00000'){
            if ($res['payUrl'] != '') {
                $data['qrcodeurl'] = $res['payUrl'];
            }elseif ($res['code_url'] != '') {
                $data['qrcodeurl'] = $res['code_url'];
            }
        }
        return $data;
    }

    /**
     * @param $requset
     * @return mixed
     */
    public static function getVerifyResult($requset)
    {
        $data = json_decode($requset->all(), true);
        self::$reqData = $data;
        $res['order']  = $data['tradeSn'];
        $res['amount'] = $data['orderAmount']/100;
        return $res;
    }


    /**
     * @param $mod
     * @param $sign
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $sign, $payConf)
    {
        $data = self::$reqData;
        ksort($data);
        $arr = '';
        foreach ($data as $key => $v){
            if ($key !== 'sign'){ #删除签名
                $arr .= $key."=".$value."&";
            }
        }
        $sign = strtoupper(md5($signStr.'key='.$payConf['md5_private_key'])); #生成签名
        if (strtoupper($sign) == strtoupper($data['sign'])) {
            return true;
        }
        return false;
    }
}