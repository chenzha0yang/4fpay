<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Ugbipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $piKey = '';
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

        self::$isAPP = true;
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$resType        = 'other';
        self::$httpBuildQuery = true;
        self::$piKey = $payConf['rsa_private_key'];
        $keyInfo = explode('@', $payConf['md5_private_key']);
        //TODO: do something

        $data['merchCardNo']       = $keyInfo[1];//收币地址
        $data['charset']           = 'UTF-8';
        $data['goodsName']         = 'UG';
        $data['merchNo']           = $payConf['business_num'];//商户号
        $data['random']            = self::randomStr(4);
        $data['version']           = 'V1.0.0';
        $data['amount']            = $amount;
        $data['merchOrderSn']      = $order;
        $data['payCallBackUrl']    = $ServerUrl;
        $data['payViewUrl']        = $returnUrl;

        ksort($data);
        $arrJsonStr = str_replace("\\/", "/", json_encode($data));
        $sign = md5($arrJsonStr.$keyInfo[0]);
        $data["sign"] = strtoupper($sign);
        $arrJsonStr = json_encode($data);

        //加密
        $encryptData = self::publicEncrypt($payConf['public_key'],$arrJsonStr);
        $request = array();
        $request["data"] = urlencode($encryptData);
        $request["merchNo"] = $data["merchNo"];
        $request["version"] = $data["version"];
        $request["merchOrderSn"] = $data["merchOrderSn"];
        $request["amount"] = $data["amount"];
        unset($reqData);
        return $request;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == "0") {
            $data['qrCode'] = $data['data']['webRrcode'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $json = $request->getContent();
        $data =  json_decode($json,true);
        $dataJsonStr = self::privateDecrypt($data["data"],self::$piKey);
        //json字符转转数组
        $dataArr = json_decode($dataJsonStr,true);
        return $dataArr;
    }

    public static function SignOther($model, $datas, $payConf)
    {
        $keyInfo = explode('@', $payConf['md5_private_key']);
        $json = file_get_contents('php://input');
        $data = json_decode($json,true);
        $dataJsonStr = self::privateDecrypt($data["data"],$payConf['rsa_private_key']);
        //json字符转转数组
        $dataArr = json_decode($dataJsonStr,true);
        $res = self::verify($dataArr,$keyInfo[2]);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public static function randomStr($length = 6){
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $tNonceStr = "";
        for ($i = 0; $i < $length; $i ++) {
            $tNonceStr .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $tNonceStr;
    }

    /**
     * [publicEncrypt 公钥加密]
     * @param  [type] $publicKey 公钥
     * @param  [type] $data      加密字符串
     * @return [type]            [description]
     */
    public static function publicEncrypt($publicKey, $data) {

        $key = openssl_get_publickey($publicKey);

        $original_arr = str_split($data,117);
        foreach($original_arr as $o) {
            $sub_enc = null;
            openssl_public_encrypt($o,$sub_enc,$key);
            $original_enc_arr[] = $sub_enc;
        }
        openssl_free_key($key);
        $original_enc_str = self::url_safe_base64_encode(implode('',$original_enc_arr));
        return $original_enc_str;
    }

    public static function url_safe_base64_encode($data)
    {
        return str_replace(array('+','/','='), array( '-','_',), base64_encode($data));
    }

    //验签
    public static function verify($result,$md5){
        $signStr = $result['sign'];
        $sign_array = array();
        foreach ($result as $k => $v) {
            if ($k !== 'sign'){
                $sign_array[$k] = $v."";
            }
        }
        ksort($sign_array);
        $signJsonStr =  str_replace("\\/", "/", json_encode($sign_array));
        $sign  = md5($signJsonStr.$md5);
        if($signStr != $sign){
            return false;
        }
        return true;
    }

    /**
     * [decode 私钥解密]
     * @param  [type] $data       [待解密字符串]
     * @param  [type] $privateKey [私钥]
     * @return [type]             [description]
     */
    public static function privateDecrypt($data,$privateKey){
        //读取秘钥
        $pr_key = openssl_pkey_get_private($privateKey);
        if ($pr_key == false){
            echo "打开密钥出错";
            die;
        }
        $data = self::url_safe_base64_decode($data);
        $crypto = '';
        //分段解密
        foreach (str_split($data, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $pr_key);
            $crypto .= $decryptData;
        }
        return $crypto;
    }
}