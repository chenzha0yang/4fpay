<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jinshunzfpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit     = 2; // 单位 ： 分
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$postType = true;
        self::$resType  = 'other';

        $str                      = '<?xml version="1.0" encoding="utf-8" standalone="no"?>
        <message application="' . $bank . '" 
        version="1.0.1" 
	    timestamp="' . date("YmdHis") . '" 
	    merchantId="' . $payConf['business_num'] . '" 
	    merchantOrderId="' . $order . '" 
	    merchantOrderAmt="' . $amount * 100 . '" 
	    merchantOrderDesc="goodsName" 
	    userName="" 
	    payerId="" 
	    salerId="" 
	    guaranteeAmt="0" 
	    merchantPayNotifyUrl="' . $ServerUrl . '" 
	    />';
        $strMD5                   = MD5($str, true);
        $strsign                  = self::signPem($strMD5, $payConf['rsa_private_key']);
        $base64_src               = base64_encode($str);
        $data['data']             = $base64_src . "|" . $strsign;
        $data['merchantOrderId']  = $order;
        $data['merchantOrderAmt'] = $amount * 100;

        unset($reqData);
        return $data;
    }

    public static function getRequestByType($data)
    {
        return $data['data'];
    }

    public static function signPem($data, $rsaKey)
    {
        $pkeyid    = openssl_pkey_get_private($rsaKey); //其中password为你的证书密码
        $signature = '';
        openssl_sign($data, $signature, $pkeyid);
        return base64_encode($signature);
    }

    public static function getQrcode($req)
    {
        $tmp  = explode("|", $req);
        $xml  = base64_decode($tmp[0]);
        $data = self::xmlToArray($xml);
        return $data['@attributes'];
    }

    public static function xmlToArray($xml, $isFile = false)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        if ($isFile) {
            if (!file_exists($xml)) return false;
            $xmlStr = file_get_contents($xml);
        } else {
            $xmlStr = $xml;
        }
        $result = json_decode(json_encode(simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }

    public static function verity($data, $signature, $pubKey)
    {
        $res    = openssl_get_publickey($pubKey);
        $result = (bool)openssl_verify($data, base64_decode($signature), $res);
        openssl_free_key($res);
        return $result;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $tmp       = explode("|", $data);
        $resp_xml  = base64_decode($tmp[0]);
        $resp_sign = $tmp[1];
        if (self::verity(MD5($resp_xml, true), $resp_sign, $payConf['public_key'])) {//验签
            return true;
        } else {
            return false;
        }
    }
}