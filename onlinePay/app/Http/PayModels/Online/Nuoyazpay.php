<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Nuoyazpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /**
     * @param array       $reqData 接口传递的参数
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        $data['apiName']    = "WEB_PAY_B2C"; // 商户APINMAE，WEB渠道一般支付
        $data['apiVersion'] = "1.0.0.0"; // 商户API版本
        $data['platformID'] = $payConf['business_num']; // 商户在Mo宝支付的平台号
        $data['merchNo']    = $payConf['business_num']; // Mo宝支付分配给商户的账号
        $data['merchUrl']   = $ServerUrl; // 商户通知地址
        $data['bankCode']   = $bank; // 银行代码，不传输此参数则跳转Mo宝收银台
        if ($payConf['pay_way'] != 1) {
            $data['bankCode'] = "";
            $data['choosePayType'] = $bank;
        }
        $data['orderNo']      = $order; //商户订单号
        $data['tradeDate']    = date("Ymd", time()); // 商户订单日期
        $data['amt']          = $amount; // 商户交易金额
        $data['merchParam']   = "abcd"; // 商户参数
        $data['tradeSummary'] = "chongzhi"; // 商户交易摘要

        // 准备待签名数据
        $str_to_sign = self::prepareSign($data);
        // 数据签名
        $data['signMsg'] = md5($str_to_sign.$payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['signMsg'];
        unset($data['signMsg']);
        unset($data['notifyType']);
        // 准备待签名数据
        $str_to_sign = self::prepareSign($data);
        // 数据签名
        $signTrue = md5($str_to_sign.$payConf['md5_private_key']);
        if (strtolower($sign) == strtolower($signTrue) && $data['orderStatus'] == '1') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @name    准备签名/验签字符串
     * @desc prepare urlencode data
     * @mobaopay_tran_query
     * #apiName,apiVersion,platformID,merchNo,orderNo,tradeDate,amt
     * #@mobaopay_tran_return
     * #apiName,apiVersion,platformID,merchNo,orderNo,tradeDate,amt,tradeSummary
     * #@web_pay_b2c,wap_pay_b2c
     * #apiName,apiVersion,platformID,merchNo,orderNo,tradeDate,amt,merchUrl,merchParam,tradeSummary
     * #@pay_result_notify
     * #apiName,notifyTime,tradeAmt,merchNo,merchParam,orderNo,tradeDate,accNo,accDate,orderStatus
     */
    public static function prepareSign($data) {
        if($data['apiName'] == 'MOBO_TRAN_QUERY') {
            $result = sprintf(
                "apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&orderNo=%s&tradeDate=%s&amt=%s",
                $data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['orderNo'], $data['tradeDate'], $data['amt']
            );
            return $result;
        #} else if (($data['apiName'] == 'WEB_PAY_B2C') || ($data['apiName'] == 'WAP_PAY_B2C')) {
        } else if ($data['apiName'] == 'WEB_PAY_B2C') {
            $result = sprintf(
                "apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&orderNo=%s&tradeDate=%s&amt=%s&merchUrl=%s&merchParam=%s&tradeSummary=%s",
            $data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['orderNo'], $data['tradeDate'], $data['amt'], $data['merchUrl'], $data['merchParam'], $data['tradeSummary']
            );
            return $result;
        } else if ($data['apiName'] == 'MOBO_USER_WEB_PAY') {
            $result = sprintf(
                "apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&userNo=%s&accNo=%s&orderNo=%s&tradeDate=%s&amt=%s&merchUrl=%s&merchParam=%s&tradeSummary=%s",
            $data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['userNo'], $data['accNo'], $data['orderNo'], $data['tradeDate'], $data['amt'], $data['merchUrl'], $data['merchParam'], $data['tradeSummary']
            );
            return $result;
        } else if ($data['apiName'] == 'MOBO_TRAN_RETURN') {
            $result = sprintf(
                "apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&orderNo=%s&tradeDate=%s&amt=%s&tradeSummary=%s",
                $data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['orderNo'], $data['tradeDate'], $data['amt'], $data['tradeSummary']
            );
            return $result;
        } else if ($data['apiName'] == 'PAY_RESULT_NOTIFY') {
            $result = sprintf(
                "apiName=%s&notifyTime=%s&tradeAmt=%s&merchNo=%s&merchParam=%s&orderNo=%s&tradeDate=%s&accNo=%s&accDate=%s&orderStatus=%s",
                $data['apiName'], $data['notifyTime'], $data['tradeAmt'], $data['merchNo'], $data['merchParam'], $data['orderNo'], $data['tradeDate'], $data['accNo'], $data['accDate'], $data['orderStatus']
            );
            return $result;
        } 
        
        $array = array();
        foreach ($data as $key=>$value) {
            array_push($array, $key.'='.$value);
        }
        return implode($array, '&');
    }
}