<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Lionzfpay extends ApiModel
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
        } else {
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$method = 'header';
            $header = array(
                'Content-Type: application/json; charset=utf-8'
            );
        }

        //TODO: do something
        $bankCode = '';
        switch ($bank) {
            case 'WAP_ALPAY_H5':
                $bankCode = 'ALSP';
                break;
            case 'WAP_ALPAY_WAP':
                $bankCode = 'ALWAP';
                break;
        }
        $data['apiName'] = $bank;
        $data['apiVersion'] = '1.0.0.0';
        $data['platformID'] = $payConf['business_num'];
        $data['merchNo'] = $payConf['business_num'];
        $data['orderNo'] = $order;
        $data['tradeDate'] = date('Ymd');
        $data['amt'] = sprintf('%.2f',$amount);
        $data['merchUrl'] = $ServerUrl;
        $data['merchParam'] = 'test';
        $data['tradeSummary'] = 'goodsName';
        if($payConf['is_app'] != 1){
            $data['customerIP'] = self::getIp();
        }
        $str = self::prepareSign($data);
        $data['signMsg'] = md5($str .$payConf['md5_private_key']);
        if($bankCode){
            $data['bankCode'] = $bankCode;
        }
        $post['data']        = http_build_query($data);
        $post['httpHeaders'] = $header;
        $post['orderNo'] = $data['orderNo'];
        $post['amt']     = $data['amt'];

        unset($reqData);
        if ($payConf['is_app'] == 1) {
            return $data;
        } else {
            return $post;
        }


    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['signMsg'];
        unset($data['signMsg'],$data['notifyType']);
        $str = self::getSignStr($data,true,false);
        $mySign = md5($str . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($mySign)) {
            return true;
        }
        return false;
    }


    public static function prepareSign($data) {
        if($data['apiName'] == 'MOBO_TRAN_QUERY') {
            $result = sprintf(
                "apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&orderNo=%s&tradeDate=%s&amt=%s",
                $data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['orderNo'], $data['tradeDate'], $data['amt']
            );
            return $result;
        } else if ($data['apiName'] == 'AUTO_SETT_QUERY') {
            $result = sprintf(
                "apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&startDate=%s&endDate=%s&startIndex=%s&endIndex=%s",
                $data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['startDate'], $data['endDate'], $data['startIndex'],$data['endIndex']
            );
            return $result;
        } else if ((($data['apiName'] == 'WEB_PAY_B2C') ||($data['apiName'] == 'WAP_PAY_B2C'))&& ($data['apiVersion'] == '1.0.0.0')) {
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
        }else if ((($data['apiName'] == 'WEB_PAY_B2C') ||($data['apiName'] == 'WAP_PAY_B2C')) && ($data['apiVersion'] == '1.0.0.1')) {
            $result = sprintf(
                "apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&orderNo=%s&tradeDate=%s&amt=%s&merchUrl=%s&merchParam=%s&tradeSummary=%s&customerIP=%s",
                $data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['orderNo'], $data['tradeDate'], $data['amt'], $data['merchUrl'], $data['merchParam'], $data['tradeSummary'],$data['customerIP']
            );
            return $result;
        }else if ($data['apiName'] == 'SINGLE_ENTRUST_SETT') {
            $result = sprintf(
                "apiName=%s&apiVersion=%s&platformID=%s&merchNo=%s&orderNo=%s&tradeDate=%s&merchUrl=%s&merchParam=%s&bankAccNo=%s&bankAccName=%s&bankCode=%s&bankName=%s&Amt=%s&tradeSummary=%s",
                $data['apiName'], $data['apiVersion'], $data['platformID'], $data['merchNo'], $data['orderNo'], $data['tradeDate'], $data['merchUrl'], $data['merchParam'], $data['bankAccNo'], $data['bankAccName'],$data['bankCode'], $data['bankName'],$data['Amt'], $data['tradeSummary']
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