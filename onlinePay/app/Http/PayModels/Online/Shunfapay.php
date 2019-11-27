<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shunfapay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = []; //回调响应数组结果集

    public static  $sign=''; //三方返回的签名

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;
        //TODO: do something

        $data['merchant'] = $payConf['business_num'];
        $data['outTradeNo'] = $order;
        $data['type'] = (int)$bank;
        $data['money'] = $amount;
        $data['time'] = round(self::getMillisecond() / 1000);
        $data['notifyUrl']=$ServerUrl;

        $jsonData = json_encode($data);
        $signStr = self::getSignStr($data, true, true);
        $sign = md5($signStr . "&key=" . $payConf['public_key']);
        $rsaStr = self::getRsaPublicSign($jsonData, self::format_secret_key($payConf['public_key']), $sign);

        $post['merchant'] = $payConf['business_num'];
        $post['outTradeNo'] = $data['outTradeNo'];
        $post['money'] = $data['money'];
        $post['data'] = $rsaStr;
        $post['sign'] = $sign; //签名
        unset($reqData);
        return $post;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['code'] == 'success') {
            $res = json_decode(base64_decode($result['orderInfo']), true);
            $result['payUrl']=$res['payUrl'];
        }
        return $result;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        if(empty($data['orderInfo'])){
            return false;
        }
        $res = json_decode(base64_decode($data['orderInfo']), true);
        self::$sign=$data['sign'];
        self::$resData=$res;
        return $res;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $data=self::$resData;//数组结果集
        $signTrue=self::$sign;//三方sign签名
        $signStr = self::getSignStr($data, true, true)."&key=" .$payConf['public_key'];
        $pub_id = openssl_get_publickey(self::format_secret_key($payConf['public_key']));
        $res = openssl_verify($signStr, base64_decode($signTrue), $pub_id, OPENSSL_ALGO_SHA1);
        if ($res && $data['status'] == '3') {
            return true;
        } else {
            return false;
        }

    }
    public static function format_secret_key($secret_key){
        //64个英文字符后接换行符"\n",最后再接换行符"\n"
        $key = (wordwrap($secret_key, 64, "\n", true))."\n";
        //添加pem格式头和尾
            $pem_key = "-----BEGIN PUBLIC KEY-----\n" . $key . "-----END PUBLIC KEY-----\n";
        return $pem_key;
    }

    /*
     * 毫秒时间戳
     */
    public static function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }



}