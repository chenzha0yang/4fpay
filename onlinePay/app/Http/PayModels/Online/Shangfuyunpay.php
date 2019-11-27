<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shangfuyunpay extends ApiModel
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
        if ($payConf['pay_way'] == '1') {
            $service = 'TRADE.B2C';
        } elseif ($payConf['is_pc_wap'] == '2') {
            $service = 'TRADE.H5PAY';
        } else {
            $service = 'TRADE.SCANPAY';
        }
        $data['service'] = $service;//接口名字固定值：“TRADE.SCANPAY”
        $data['version'] = '1.0.0.0';//接口版本  version 是   7   固定值：“1.0.0.0”
        $data['merId']   = $payConf['business_num'];//商户编号
        if ($payConf['pay_way'] != '1') {
            $data['typeId'] = $bank;//支付平台
        }
        $data['tradeNo']    = $order;//商户订单号
        $data['tradeDate']  = date('Ymd');//交易日期
        $data['amount']     = number_format($amount, 2, '.', '');//订单金额    必输  以分为单位
        $data['notifyUrl']  = $ServerUrl;//异步通知URL
        $data['extra']      = $order;
        $data['summary']    = 'apple';
        $data['expireTime'] = '30';
        $data['clientIp']   = '127.0.0.1';
        if ($payConf['pay_way'] == '1') {
            $data['bankId'] = $bank;
        }

        // 对含有中文的参数进行UTF-8编码
        // 将中文转换为UTF-8
        if (!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['notifyUrl'])) {
            $data['notifyUrl'] = iconv("GBK", "UTF-8", $data['notifyUrl']);
        }


        if (!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['extra'])) {
            $data['extra'] = iconv("GBK", "UTF-8", $data['extra']);
        }

        if (!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['summary'])) {
            $data['summary'] = iconv("GBK", "UTF-8", $data['summary']);
        }
        $signStr      = self::prepareSign($data);
        $data['sign'] = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        if ($payConf['pay_way'] == '0') {
            $data['act'] = 'postsub';
        } else {
            $data['act']    = 'Shangfypay';
            $data['payway'] = $payConf['pay_way'];
            $data['pcwap']  = $payConf['is_pc_wap'];
        }
        unset($reqData);
        return $data;
    }

    private static function prepareSign($data)
    {
        //1网银支付
        if ($data['service'] == 'TRADE.B2C') {
            $result = sprintf(
                "service=%s&version=%s&merId=%s&tradeNo=%s&tradeDate=%s&amount=%s&notifyUrl=%s&extra=%s&summary=%s&expireTime=%s&clientIp=%s&bankId=%s",
                $data['service'],
                $data['version'],
                $data['merId'],
                $data['tradeNo'],
                $data['tradeDate'],
                $data['amount'],
                $data['notifyUrl'],
                $data['extra'],
                $data['summary'],
                $data['expireTime'],
                $data['clientIp'],
                $data['bankId']
            );
            return $result;
            //2扫码支付
        } else if ($data['service'] == 'TRADE.SCANPAY') {
            $result = sprintf(
                "service=%s&version=%s&merId=%s&typeId=%s&tradeNo=%s&tradeDate=%s&amount=%s&notifyUrl=%s&extra=%s&summary=%s&expireTime=%s&clientIp=%s",
                $data['service'],
                $data['version'],
                $data['merId'],
                $data['typeId'],
                $data['tradeNo'],
                $data['tradeDate'],
                $data['amount'],
                $data['notifyUrl'],
                $data['extra'],
                $data['summary'],
                $data['expireTime'],
                $data['clientIp']
            );
            return $result;
            //3 H5
        } else if ($data['service'] == 'TRADE.H5PAY') {
            $result = sprintf(
                "service=%s&version=%s&merId=%s&typeId=%s&tradeNo=%s&tradeDate=%s&amount=%s&notifyUrl=%s&extra=%s&summary=%s&expireTime=%s&clientIp=%s",
                $data['service'],
                $data['version'],
                $data['merId'],
                $data['typeId'],
                $data['tradeNo'],
                $data['tradeDate'],
                $data['amount'],
                $data['notifyUrl'],
                $data['extra'],
                $data['summary'],
                $data['expireTime'],
                $data['clientIp']
            );
            return $result;
        }
    }
}