<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Huobanzfpay extends ApiModel
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

        $data = [];

        $data['version']          = 'V1.0.0';
        $data['mch_id']           = $payConf['business_num'];
        $data['nonce_str']        = self::createNonceStr();
        $data['body']             = 'goodsName';
        $data['out_trade_no']     = $order;
        $data['total_fee']        = $amount * 100;
        $data['spbill_create_ip'] = self::getIp();
        $data['notify_url']       = $ServerUrl;
        $data['trade_type']       = 'trade.' . $bank . '.native';
        $data['fee_type']         = 'CNY';
        $data['limit_pay']        = '';
        $data['detail']           = 'goodsDesc';
        if (self::$isAPP) {
            $data['trade_type'] = 'trade.' . $bank . '.h5pay';
        }

        $signStr =  self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));

        $xml                  = self::getXml($data);
        $post['xml']          = $xml;
        $post['total_fee']    = $data['total_fee'];
        $post['out_trade_no'] = $data['out_trade_no'];
        self::$unit           = 2;
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$resType        = 'other';
        self::$postType       = true;
        unset($reqData);
        return $post;
    }

    public static function getRequestByType($req)
    {
        unset($req['total_fee']);
        unset($req['out_trade_no']);
        return $req['xml'];
    }

    //回调金额处理
    public static function getVerifyResult(Request $request)
    {
        $res           = $request->getContent();
        self::$resData = $res;
        libxml_disable_entity_loader(true);
        $data            = json_decode(json_encode(simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $data['amount']  = $res['total_fee'] / 100;
        $data['orderNo'] = $res['out_trade_no'];
        return $data;
    }

    public static function SignOther($type, $json, $payConf)
    {
        $res = self::$resData;
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $sign = $data['sign'];
        unset($data['sign']);

        $signStr =  self::getSignStr($data, true, true);
        $mySign = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));

        if ($sign == $mySign && $data['result_code'] == 'SUCCESS' && $data['return_code'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }

    public static function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public static function getXml($data)
    {
        $srting = '<xml>';
        foreach ($data as $key => $row) {
            if ($row != '') {
                $srting .= '<' . $key . '>' . $row . '</' . $key . '>';
            }
        }
        $srting .= '</xml>';
        return $srting;
    }

    public static function getQrCode($response)
    {
        $res = self::analysisXML($response);
        return $res;
    }


    public static function analysisXML($reback)
    {
        $obj     = simplexml_load_string($reback, 'SimpleXMLElement', LIBXML_NOCDATA);
        $reponse = [];
        foreach ($obj as $key => $row) {
            if ($row != '' && $key != 'sign') {
                $reponse[$key] = (string)$obj->$key;
            }
        }
        return $reponse;
    }
}