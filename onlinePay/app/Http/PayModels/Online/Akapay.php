<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\File;

class Akapay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    // 扫码返回数据
    public static $headerToArray = true; // 将头信息转换为数组 status header body

    /*  回调数据  */
    public static $headers = ''; //回调头部信息

    public static $body    = ''; //回调数据

    public static $url     = ''; //回调url


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

        //TODO: do something

        if($payConf['pay_way'] == '1'){
            $bank = 'DEBIT_BANK_CARD_PAY';
        }
        $parameters = $headers = array();
        $parameters["merchantNo"] = $payConf['business_num']; //商户号，请到商户后台查询
        $parameters["outTradeNo"] = $order; //商户订单号，请确保本商户内唯一
        $parameters["currency"] = "CNY";//货币类型，CNY 或 USD，目前仅支持CNY，默认CNY
        $parameters["amount"] = $amount*100;//交易金额，单位分
        $parameters["payType"] = $bank;//支付类型
        $parameters["content"] = "remark";//交易描述，最多200个字符
        $parameters["callbackURL"] = $ServerUrl;//交易状态回调地址
        $apiName = 'com.opentech.cloud.easypay.trade.create';//API名称
        $apiVersion = '0.0.1';//API版本
        $headers['x-oapi-pv'] = '0.0.1';
        $headers['x-oapi-sdkv'] = '0.0.1/php';
        $headers['x-oapi-sk'] = $payConf['message1']; //证书key
        $url = $reqData['formUrl'] . "/". $apiName. "/" . $apiVersion;
        $body = NULL;
        $body = json_encode($parameters);
        $headers['x-oapi-sign'] = self::getSign($url, $headers, $body,$reqData); //证书key
        $httpHeaders = array();
        array_push($httpHeaders, "Content-Type:application/json;charset=utf-8");
        foreach ($headers as $key => $value) {
            array_push($httpHeaders, $key . ':' . $value);
        }

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method  = 'header';

        $postData = array();
        $postData['formUrl'] = $url;
        $postData['data'] = $body;
        $postData['outTradeNo'] = $order;
        $postData['amount'] = $amount;
        $postData['httpHeaders'] = $httpHeaders;
        unset($reqData);
        return $postData;
    }

    /**
     * @param $url
     * @param $headers
     * @param $body
     * @param $reqData
     * @return string
     */
    public static function getSign($url, $headers, $body,$reqData)
    {
        $agentId = $reqData['agentId'];//代理线
        $agentNum = $reqData['agentNum'];//子代理线
        $className = self::replaceName(__CLASS__);//类名
        $fileTypePwd = '_password.txt';//
        $fileTypePri = '_prikey.pfx';//秘钥文件
        $password = File::getPubKey($agentId, $agentNum , $className , $fileTypePwd);
        $priKey = File::getPubKey($agentId, $agentNum , $className , $fileTypePri);
        openssl_pkcs12_read($priKey, $privateKey, $password);
        ksort($headers);
        $str = $url;
        foreach ($headers as $key => $value) {
            if(strpos($key, 'x-oapi-') === 0) {
                $str = $str . "&" . $key . "=" . $value ;
            }
        }
        if(isset($body)) {
            $str = $str . "&" . $body;
        }
        openssl_sign($str, $signature, $privateKey['pkey']);
        return base64_encode($signature);
    }

    /**
     * @param $data
     * @return array
     */
    public static function toCurl($data)
    {
        $url = $data['formUrl'];
        $httpHeaders = $data['httpHeaders'];
        $body = $data['data'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'HTTP Request Error: ' . curl_error($ch);exit;
        }
        $parsedResponse = static::parseHttpResponse($ch, $response);
        curl_close($ch);
        return $parsedResponse;
    }

    /**
     * @param $ch
     * @param $response
     * @return array
     */
    public static function parseHttpResponse($ch, $response)
    {
        $headers = array();
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerText = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $status = 0;
        foreach (explode("\r\n", $headerText) as $i => $line) {
            if(!$line) {
                continue;
            }
            if ($i === 0) {
                list ($protocol, $status) = explode(' ', $line);
                $status = $status;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[strtolower($key)] = urldecode($value);
            }
        }
        $result = array("status" => $status, "headers" => $headers, "body" => $body);
        return $result;
    }


    /** 二维码处理
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res)
    {
        $headers = $res["headers"];
        $body = $res["body"];
        $msgData = json_decode($body,true);
        if($msgData){
            if($msgData['status'] == 'WAITING_PAY'){
                $returnMsg['paymentInfo'] = $msgData['paymentInfo'];
                //$returnMsg['status']  = 'WAITING_PAY';
            }else{
                $returnMsg['errorCode'] = $msgData['x-oapi-error-code'];
                $returnMsg['errorMsg']  = $msgData['x-oapi-msg'];
                $returnMsg['status']  = '';
            }
        }else{
            $returnMsg['errorCode'] = $headers['x-oapi-error-code'];
            $returnMsg['errorMsg']  = $headers['x-oapi-msg'];
            $returnMsg['status']  = '';
        }
        return $returnMsg;
    }


    /**回调处理
     * @param $request
     * @return mixed
     */
    public static function getVerifyResult($request)
    {
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $headers = self::getAllHeaders();
        self::$headers = $headers;
        self::$body = $request->all();
        self::$url = $url;
        $amount = self::$body['amount'];//金额  分
        $order = self::$body['outTradeNo'];//订单号
        $res['amount'] = $amount / 100;
        $res['order'] = $order;
        return $res;
    }

    public static function getAllHeaders()
    {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $key = str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))));
                if(0 == strcmp($key, 'x-oapi-sign')) {
                    $headers[$key] = $value;
                } else {
                    $headers[$key] = urldecode($value);
                }
            }
        }
        return $headers;
    }

    /**
     * @param $type    模型名
     * @param $sign    签名参数
     * @param $payConf 商户资料
     * @return bool
     */
    public static function SignOther($type, $sign, $payConf)
    {
        $headers = self::$headers;
        $body    = self::$body;
        $url     = self::$url;
        $signed = $headers['x-oapi-sign'];
        unset($headers['x-oapi-sign']);
        return self::validateSign(base64_decode($signed), $url, $headers, json_encode($body),$payConf);

    }

    /**
     * @param $signed
     * @param $url
     * @param $headers
     * @param $body
     * @param $payConf
     * @return bool
     */
    public static function validateSign($signed, $url, $headers, $body,$payConf)
    {

        $agentId = $payConf['agent_id'];//代理线
        $agentNum = $payConf['agent_num'];//子代理线
        $className = __CLASS__;//类名
        $fileTypePub = '_pubkey.cer';//公钥文件
        $pubKey = File::getPubKey($agentId, $agentNum , $className , $fileTypePub);
        $publicKey = openssl_pkey_get_public($pubKey);
        $str = $url;

        ksort($headers);
        foreach ($headers as $key => $value) {
            if(strpos($key, 'x-oapi-') === 0) {
                $str = $str . "&" . strtolower($key) . "=" . $value ;
            }
        }
        $str = $str . "&" . $body;
        return (bool) openssl_verify($str, $signed, $publicKey);
    }
}