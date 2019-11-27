<?php

namespace App;

use App\Extensions\Common;
use App\Extensions\File;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;
use App\Http\Models\CallbackMsg;

class ApiModel
{
    use Common;


    public static $result = [
        'appPath' => '',
        'qrCode'  => '',
        'code'    => '',
        'msg'     => '',
    ];

    /**
     * 自定义错误信息
     * @var array
     */
    public static $errorData = [
        'order'  => '',
        'amount' => '',
        'msg'    => '',
    ];


    /**getSignStr
     * @param        $para array 待拼接的数组
     * @param bool   $isNull
     * @param bool   $sort
     * @param string $space
     * @return bool|string
     */
    public static function getSignStr($para, $isNull = true, $sort = false, $space = '&')
    {
        if ($sort) {
            ksort($para);
        }

        $arg = "";
        foreach ($para as $key => $value) {
            if ($isNull) {
                if ($value != '' && $value != null) {
                    $arg .= "{$key}={$value}{$space}";
                }
            } else {
                $arg .= "{$key}={$value}{$space}";
            }
        }

        //去掉最后一个&字符
        $arg = rtrim($arg, $space);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * @param      $para array 待转数组
     * @param bool $isNull
     * @param bool $sort
     * @return bool|JSON
     *
     */
    public static function getJsonStr($para, $isNull = true, $sort = false)
    {
        if ($sort) {
            ksort($para);
        }
        $array = [];
        foreach ($para as $key => $value) {
            if ($isNull) {
                if ($value) {
                    $array[$key] = $value;
                }
            } else {
                $array[$key] = $value;
            }
        }
        $jsonData = json_encode($array);
        return $jsonData;
    }

    /**
     * @param $para string 待签名数据签名串Md5
     * @param $priKey string 秘钥
     * @return string
     */
    public static function getMd5Sign($para, $priKey)
    {
        $preStr = $para . $priKey;
        //把最终的字符串签名，获得签名结果
        $mySign = md5($preStr);
        return $mySign;
    }

    /**
     * @param string $para 待签名数据签名串RSA
     * @param string $priKey 秘钥
     * @param int    $sslType 秘钥
     * @return string
     */
    public static function getRsaSign($para, $priKey, $sslType = OPENSSL_ALGO_SHA1)
    {
        $res = openssl_get_privatekey($priKey);
        if (!$res) {
            return false;
        }
        // 调用openssl内置签名方法，生成签名$sign
        openssl_sign($para, $sign, $res, $sslType);

        // 释放资源
        openssl_free_key($res);

        // base64编码
        $mySign = base64_encode($sign);

        return $mySign;
    }

    /**
     * @param string $para 待签名数据签名串RSA
     * @param string $pubKey 公钥
     * @param int    $sslType 秘钥
     * @return string
     */
    public static function getRsaPublicSign($para, $pubKey)
    {
        $res = openssl_pkey_get_public($pubKey);
        if (!$res) {
            return false;
        }

        $encryptData = '';
        $str         = '';
        foreach (str_split($para, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $pubKey);
            $str = $str . $encryptData;
        }
        $mySign = base64_encode($str);

        return $mySign;
    }

    /**
     * @param $decode
     * @param $pubKey
     * @return string
     */
    public static function getPubData($decode, $pubKey)
    {
        $prKey = openssl_pkey_get_private($pubKey);
        if (!$prKey) {
            return false;
        }
        $json   = base64_decode($decode);
        $cryPto = '';
        //分段解密
        foreach (str_split($json, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $prKey);
            $cryPto .= $decryptData;
        }
        return $cryPto;
    }

    /**
     * @param $data string 待签名的数据
     * @param $sign string 需要验证的签名
     * @param $pubKey string 验签用的公钥
     * @param $sslType int  验签用的公钥
     * @return bool 验签是否通过 bool值
     */
    public static function verifyRSA($data, $sign, $pubKey, $sslType = OPENSSL_ALGO_SHA1)
    {
        $res = openssl_get_publickey($pubKey);
        // 调用openssl内置方法验签，返回bool值
        $verify = openssl_verify($data, base64_decode($sign), $res, $sslType);
        if ($verify) {

            return true;

        } else {

            return false;

        }
    }

    /**
     * 随机字符串
     *
     * @param $lenth
     * @return string
     */
    public static function randStr($lenth)
    {
        $chars    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        $password = '';
        for ($i = 0; $i < $lenth; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * @param $data
     * @param $key
     * @return string
     */
    public static function decrypt($data, $key)
    {
        $key  = md5($key);
        $x    = 0;
        $data = base64_decode($data);
        $len  = strlen($data);
        $l    = strlen($key);
        $char = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    }

    //获取IP
    public static function getIp(){
        $realip = '';
        $unknown = 'unknown';
        if (isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach($arr as $ip){
                    $ip = trim($ip);
                    if ($ip != 'unknown'){
                        $realip = $ip;
                        break;
                    }
                }
            }else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){
                $realip = $_SERVER['REMOTE_ADDR'];
            }else{
                $realip = $unknown;
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            }else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){
                $realip = getenv("HTTP_CLIENT_IP");
            }else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){
                $realip = getenv("REMOTE_ADDR");
            }else{
                $realip = $unknown;
            }
        }
        $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
        return $realip;
    }

    public static $order = []; // 注单数据

    /**
     * 获取配置相关信息
     *
     * @param string $orderNum
     * @return bool
     */
    public static function getPayConf(string $orderNum)
    {
        $bankOrder = PayOrder::getOrderData($orderNum);//根据订单号 获取入款注单数据
        if (empty($bankOrder)) {
            File::logResult($orderNum . '查询不到订单数据', 'logs/callback2.log');
            return false;
        }

        self::$order = $bankOrder;
        return PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
    }

    /**
     * callback log
     * 
     * @param $request
     * @param $amount
     */
    public static function addCallbackMsg($request){
        CallbackMsg::AddCallbackMsg($request, self::$order, 1);
    }
}