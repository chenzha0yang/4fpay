<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Goldmypay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

    /**
     * @param array $reqData 接口传递的参数
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order  = $reqData['order'];
        $amount = $reqData['amount'];
        $bank   = $reqData['bank'];
        //TODO: do something

        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$isAPP          = true;

        $data                     = [];
        $data['platform_code']    = $payConf['business_num'];//商户号
        $data['body']             = 'test';
        $data['nonce_str']        = self::getRandomStr(30, false);
        $data['out_trade_no']     = $order;//订单号
        $data['spbill_create_ip'] = self::getIp();
        $data['trade_type']       = $bank;//银行编码
        $data['total_fee']        = $amount;//订单金额
        $signStr                  = self::getSignStr($data, true, true);
        $data['sign']             = strtoupper(self::getMd5Sign($signStr . "&key=", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    public static function getRandomStr($len, $special = true)
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        if ($special) {
            $chars = array_merge($chars, array(
                "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
                "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
                "}", "<", ">", "~", "+", "=", ",", "."
            ));
        }
        $charsLen = count($chars) - 1;
        shuffle($chars);                            //打乱数组顺序
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            for ($i = 0; $i < $len; $i++) {
                $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
            }
            return $str;
        }
    }


    //回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $xmlData = $request->getContent();
        ### 把xml转换为数组
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        //先把xml转换为simplexml对象，再把simplexml对象转换成 json，再将 json 转换成数组。
        $data          = json_decode(json_encode(simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        self::$resData = $data;
        return $data;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $json, $payConf)
    {
        $data = self::$resData;
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtoupper(self::getMd5Sign($signStr . "&key=", $payConf['md5_private_key']));
        if (strtoupper($sign) == $signTrue && $data['result_code'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
}