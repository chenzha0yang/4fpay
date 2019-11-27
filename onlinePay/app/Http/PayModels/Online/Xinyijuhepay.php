<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinyijuhepay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$unit         = 2;
        $data               = [];
        $data['version']    = '1.0.0';
        $data['subject']    = 'abc';
        $data['describe']   = 'ahs';
        $data['remark']     = 'hgb';
        $data['userIP']     = self::getIP();
        $data['merOrderId'] = $order;
        if ($payConf['pay_way'] !== '1') {
            $data['tradeType'] = $bank;
            $data['payMode']   = '0701';
        } else {
            $data['payMode']   = '0201';
            $data['bankCode']  = $bank;
            $data['tradeType'] = '52';
        }
        $data['tradeTime']    = date('YmdHis');
        $data['tradeSubtype'] = '01';
        $data['currency']     = 'CNY';
        $data['amount']       = strval($amount * 100);
        $data['urlBack']      = $ServerUrl;
        $data['urlJump']      = $returnUrl;
        $data['merId']        = $payConf['business_num'];
        $data['merUserId']    = '';
        $signStr              = self::getSignStr($data, false, true);
        $data['signature']    = base64_encode(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        $data['subject']      = base64_encode($data['subject']);
        $data['describe']     = base64_encode($data['describe']);
        $data['remark']       = base64_encode($data['remark']);
        $data['signMethod']   = 'MD5';
        unset($reqData);
        return $data;
    }

    private static function getIP()
    {
        $ip = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = 'Unknow';
        }
        return $ip;
    }
}