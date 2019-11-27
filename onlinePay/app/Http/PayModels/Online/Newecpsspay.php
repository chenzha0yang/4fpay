<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Newecpsspay extends ApiModel
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


        $data['MerNo']     = $payConf['business_num'];
        $data['BillNo']    = $order;
        $data['Amount']    = sprintf('%.2f', $amount);
        $data['ReturnURL'] = $returnUrl;
        $data['AdviceURL'] = $ServerUrl;
        $data['OrderTime'] = date('YmdHis', time());
        if ($payConf['pay_way'] == '1') {
            $data['defaultBankNumber'] = $bank;
            $data['payType']           = 'B2CDebit';
        } else {
            $data['payType'] = $bank;
        }
        //MerNo=您的注册商户号&BillNo=您的网站订单号&Amount=您的订单金额&OrderTime=您的订单日期字符串&ReturnURL=您的页面接收地址&AdviceURL=您的服务端接收地址
        $signStr = 'MerNo=' . $data['MerNo'] . '&BillNo=' . $data['BillNo'] . '&Amount=' . $data['Amount'] . '&OrderTime=' . $data['OrderTime'] . '&ReturnURL=' . $data['ReturnURL'] . '&AdviceURL=' . $data['AdviceURL'];
        openssl_sign($signStr, $sign, $payConf['rsa_private_key']);
        $data['SignInfo'] = base64_encode($sign);

        if ($payConf['pay_way'] == '1' || $payConf['pay_way'] == '6' || $payConf['pay_way'] == '9') {
            unset($reqData);
            return $data;
        } else {
            $xml = '';
            foreach ($data as $key => $value) {
                $xml .= '<' . $key . '>' . $value . '</' . $key . '>';
            }
            $xml              = '<?xml version="1.0" encoding="utf-8"?> <ScanPayRequest>' . $xml . '</ScanPayRequest>';

            unset($reqData);
            $post =  ['requestDomain' => base64_encode($xml)];
            return $post;
        }


    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['SignInfo'];
        $signStr = 'MerNo='.$data['MerNo'].'&BillNo='.$data["BillNo"].'&OrderNo='.$data['OrderNo'].'&Amount='.$data['Amount'].'&Succeed='.$data['Succeed'];
        $pubKey = openssl_get_publickey($payConf['public_key']);
        $res = openssl_verify($signStr,self::url_safe_base64_decode($sign),$pubKey);
        if ($res && $data['Succeed'] == "88") {
            return true;
        } else {
            return false;
        }
    }

    static function url_safe_base64_decode($base_64) {
        $base_64 = str_replace(" ","+", $base_64);
        return base64_decode($base_64);
    }

}