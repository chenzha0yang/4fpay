<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bywypay extends ApiModel
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

        self::$unit = 2;
        self::$method = 'get';

        $data['amount']        = (string)($amount*100);
        $data['mch_id']        = $payConf['business_num'];//商户号
        $data['notify_url']    = $ServerUrl; #通知地址
        $data['out_trade_no']  = $order; #商户订单号
        $data['mch_create_ip'] = '127.0.0.1';
        $data['time_start']    = date("YmdHms");
        $data['body']          = 'honor';
        $data['attach']        = $amount*100;#附加
        $data['nonce_str']     = self::randStr1();
        $data['trade_type']    = '02';
        $data['paytype']       = $bank;
        if($payConf['pay_way'] == '1'){
            $data['paytype']   = 'wy';
        }
        $data['back_url']      = $returnUrl;
        $data['phone']         = self::getmobile();#手机号
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }
    //手机号
    public static function getmobile(){
        $arr = array(
            130,131,132,133,134,135,136,137,138,139,
            144,147,
            150,151,152,153,155,156,157,158,159,
            176,177,178,
            180,181,182,183,184,185,186,187,188,189,
        );

        return $arr[array_rand($arr)].mt_rand(1000,9999).mt_rand(1000,9999);
    }
    //随机数
    public static function randStr1(){
        $password = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        for ( $i = 0; $i < 8; $i++ ){
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }
}