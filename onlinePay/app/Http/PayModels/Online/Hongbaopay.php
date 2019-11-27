<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hongbaopay extends ApiModel
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

        $data = [];
        if ($payConf['pay_way'] == '10') {
            $data['merchantId']      = $payConf['business_num'];
            $data['merchantOrderNo'] = $order;
            $data['merchantUserId']  = $payConf['business_num'];
            $data['payAmount']       = $amount;
            $data['notifyUrl']       = $ServerUrl;
            $data['description']     = 'test';//商品信息
            $data['userIp']          = '127.0.0.1'; //用户IP
        } else if ($payConf['pay_way'] == '1') {
            $data['expireTime'] = '';
            $data['summary']    = 'zhif';
            $data['amount']     = $amount;
            $data['tradeDate']  = date('Y-m-d', time());
            $data['tradeNo']    = $order;
            $data['extra']      = 'tet';
            $data['service']    = 'B2C';
            $data['merId']      = $payConf['business_num'];
            $data['bankName']   = '银联WAP';
            $data['clientIp']   = '127.0.0.1';
            $data['notifyUrl']  = $ServerUrl;
            $data['version']    = '1';
        } else {
            $data['expireTime'] = 30;
            $data['summary']    = 'zhif';
            $data['amount']     = $amount;
            $data['tradeDate']  = date('Y-m-d', time());
            $data['tradeNo']    = $order;
            $data['extra']      = 'test';
            $data['service']    = 'SCANPAY';
            $data['merId']      = $payConf['business_num'];
            $data['clientIp']   = '127.0.0.1';
            $data['notifyUrl']  = $ServerUrl;
            $params['typeId']   = $bank;
            $data['version']    = '1.0.0.0';
        }
        $strToSign    = self::getSignStr($data, false, false, ',');
        $data['sign'] = self::getMd5Sign($strToSign, $payConf['md5_private_key']); //签名
        unset($reqData);
        return $data;
    }
}