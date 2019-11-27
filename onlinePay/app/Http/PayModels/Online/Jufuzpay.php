<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jufuzpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $UserName = '';

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        self::$reqType = 'curl'; // 有即时返回json时，用curl请求
        self::$resType = 'other'; //请求支付时，返回来的即时json数据，做特殊处理
        self::$payWay = $payConf['pay_way']; //生成二维码
        self::$method = 'header';
        self::$unit = 2;
        self::$isAPP = true;
        if ($payConf['pay_way'] == '1') {
            $bank = 'gateway_pay';
        }
        $data['mchntCode'] = $payConf['business_num'];//商户编号
        $data['channelCode'] = $bank;//支付类型
        $data['ts'] = self::ts_time('YmdHisu');
        $data['mchntOrderNo'] = $order; //订单号
        $data['orderAmount'] = $amount * 100;//订单金额
        $data['clientIp'] = self::getIp();
        $data['subject'] = 'ipad';
        $data['body'] = 'chongzhi';
        $data['notifyUrl'] = $ServerUrl;//异步通知URL
        $data['orderTime'] = date('YmdHis', time());
        $data['orderExpireTime'] = date('YmdHis', time() + 300);
        $data['pageUrl'] = $returnUrl;
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        $header = array(
            "Accept: application/json",
            "Content-Type: application/json;charset=utf-8",
        );
        $post['data'] = json_encode($data);
        $post['httpHeaders'] = $header;
        $post['orderAmount'] =$data['orderAmount'];
        $post['mchntOrderNo']=$data['mchntOrderNo'];
        unset($reqData);
        return $post;
    }

    /**
     * 二维码、链接处理
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res)
    {

        $result = json_decode($res, true);
        if ($result['retCode'] == '0000') {
        if (isset($result['codeUrl']))
            $result['qrCode'] = $result['data']['codeUrl'];
        }
        return $result;
    }

    public static function getVerifyResult($request)
    {
        $arr = $request->all();
        $res['orderAmount'] = $arr['orderAmount'] / 100;
        $res['mchntOrderNo'] = $arr['mchntOrderNo'];
        return $res;
    }


    public static function SignOther($model, $data, $payConf)
    {
        $sign = strtoupper($data['sign']);
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $mySign = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));

        if ($sign == $mySign && $data['payResult'] == 1) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 年月日、时分秒 + 3位毫秒数
     * @param string $format
     * @param null $utimestamp
     * @return false|string
     */
    public static function ts_time($format = 'u', $utimestamp = null)
    {
        if (is_null($utimestamp)) {
            $utimestamp = microtime(true);
        }
        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000);
        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
}