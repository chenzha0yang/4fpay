<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;

class Tuozhongpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $public_key = '';

    public static $array = [];

    public static $changeUrl = true;


    /**
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        /**
         * 参数赋值， 方法间传递数组
         */
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        //$returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method = 'header';
        self::$public_key = $payConf['public_key'];
        if($payConf['is_app'] == 1){
           self::$isAPP = true;
        }

        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        $aesKey = self::randomkeys(16);//随机生成16为AES秘钥
        $key = self::rsaEncrypt($payConf['rsa_private_key'], $aesKey);//Rsa 私钥加密
        $data['merchantOrderNo'] = $order;
        $data['amount'] = $amount;
        $data['model'] = $bank;
        $data['memberNo'] = $payConf['business_num'];
        $data['notifyUrl'] = $ServerUrl;
        $dataStr = json_encode($data, JSON_UNESCAPED_SLASHES);//带AES加密json字符串

        $arr['merchantId'] = $payConf['business_num'];
        $arr['version'] = '1.0.0';
        $arr['key'] = $key;
        $arr['data'] = self::aesEncrypt($dataStr, $aesKey);

        $post['queryUrl'] = $reqData['formUrl'].'/create';
        $post['merchantOrderNo'] = $data['merchantOrderNo'];
        $post['amount'] = $data['amount'];
        $post['data'] = json_encode($arr);
        $post['httpHeaders'] = [
            "Content-Type: application/json; charset=utf-8"
        ];
        unset($reqData);
        return $post;
    }

    /**
     * 返回结果 - 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $backData = json_decode($response, true);
        if ($backData['code'] == '0') {
            //读取公钥
            $publicKey = self::$public_key;
            //RSA公钥解密key
            $resKey = self::rsaDecrypt($publicKey, $backData["key"]);
            //解密data
            $resData = self::aesDecrypt($backData["data"], $resKey);
            //json字符串 转数组
            $resArr = json_decode($resData, true);
            $res['url'] = $resArr['url'];
        } else {
            $res['code'] = $backData['code'];
            $res['message'] = $backData['message'];
        }
        return $res;
    }

    //回调处理数据 解密
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $data = json_decode($arr, true);
        $bankOrder = PayOrder::getOrderData($data['merchantOrderNo']);//根据订单号 获取入款注单数据
        $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        //RSA公钥解密key
        $resKey = self::rsaDecrypt($payConf['public_key'], $data["key"]);
        //解密data
        $resData = self::aesDecrypt($data["data"], $resKey);
        //json字符串 转数组
        $resArr = json_decode($resData, true);
        self::$array = $resArr;
        return $resArr;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $data = self::$array;
        if ($data['status'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 随机生成16为AES秘钥
     * */
    public static function randomkeys($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = "";
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{mt_rand(0, 35)};    //生成php随机数
        }
        return $key;
    }

    /**
     * 私钥加密
     */
    public static function rsaEncrypt($privateKey, $data)
    {
        $key = openssl_get_privatekey($privateKey);
        $original_arr = str_split($data, 117);

        foreach ($original_arr as $o) {
            $sub_enc = null;

            openssl_private_encrypt($o, $sub_enc, $key);
            $original_enc_arr[] = $sub_enc;
        }
        openssl_free_key($key);
        $original_enc_str = base64_encode(implode('', $original_enc_arr));
        return $original_enc_str;
    }


    /**
     * AES加密
     */
    public static function aesEncrypt($input, $key)
    {
            return base64_encode(openssl_encrypt($input, 'AES-128-ECB', $key, OPENSSL_RAW_DATA));
    }


    /**
     *  AES 解密
     */
    public static function aesDecrypt($sStr, $sKey)
    {
            $encrypted = base64_decode($sStr);
            return openssl_decrypt($encrypted, 'AES-128-ECB', $sKey, OPENSSL_RAW_DATA);
    }

    /**
     * 拼接私钥字符串
     *
     */
    public static function privateKeyStr($privatekey)
    {

        $private_key = "-----BEGIN PRIVATE KEY-----\r\n";
        foreach (str_split($privatekey, 64) as $str) {
            $private_key .= $str . "\r\n";
        }
        $private_key .= "-----END PRIVATE KEY-----";

        return $private_key;
    }

    /**
     * 公钥解密
     */
    public static function rsaDecrypt($publicKey, $data)
    {

        //读取秘钥
        $pr_key = openssl_pkey_get_public($publicKey);
        if ($pr_key == false) {
            echo "打开密钥出错";
            die;
        }
        $data = base64_decode($data);
        $crypto = '';
        //分段解密
        foreach (str_split($data, 128) as $chunk) {
            openssl_public_decrypt($chunk, $decryptData, $pr_key);
            $crypto .= $decryptData;
        }
        return $crypto;
    }
}