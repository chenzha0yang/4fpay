<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Newnorthbearpay extends ApiModel
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
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo ($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        self ::$unit = 2;
        self ::$reqType = 'curl';
        self ::$payWay = $payConf['pay_way'];
        self ::$isAPP = true;
        self ::$postType = true;


        //获取token
        $randomStr = self ::getRandomStr(30, false);
        $tokenUrl = 'http://api.qwebank.top/open/v1/getAccessToken/merchant';
        $tokenArr['merchantNo'] = $payConf['business_num'];
        $tokenArr['key'] = $payConf['md5_private_key'];
        $tokenArr['nonce'] = $randomStr;
        $tokenArr['timestamp'] = time();
        $tokenSignStr = 'merchantNo=' . $tokenArr['merchantNo'] . '&nonce=' . $tokenArr['nonce'] . '&timestamp=' . $tokenArr['timestamp'] . '&key=' . $tokenArr['key'];
        $tokenArr['sign'] = strtoupper(md5($tokenSignStr));
        $tokenArr['token'] = null;
        $header = array("Content-Type: application/json");
        $tokenRes = self ::httpGet($tokenUrl, json_encode($tokenArr), $header);
        $tokenJsonRes = json_decode($tokenRes,true);
        $accessToken = $tokenJsonRes['value']['accessToken'];
        $data = [];
        $jsonData['accessToken'] = $accessToken;
        $data['type'] = 'T0';
        $data['pay_memberid'] = $payConf['business_num'];//商户号
        $data['outTradeNo'] = $order;//订单号
        $data['money'] = $amount * 100;//订单金额
        $data['successUrl'] = $returnUrl;
        $data['notifyUrl'] = $ServerUrl;
        $data['body'] = 'test';
        $data['detail'] = 'test';
        $data['productId'] = '11';
        if ( $payConf['pay_way'] == '2' && $payConf['is_app'] == '1' ) {
            $data['merchantIp'] = self ::getIp();
        }
        if ( $payConf['pay_way'] == '1' ) {
            $data['bankName'] = $bank;
        }
        if ( $payConf['pay_way'] == '9' ) {
            $data['cardName'] = '123';
            $data['cardNo'] = '123';
            $data['bank'] = '123';
            $data['idType'] = '1';
            $data['cardPhone'] = '123';
            $data['cardIdNumber'] = '123';
        }
        $post['param'] = $data;
        $post['outTradeNo'] = $data['outTradeNo'];
        $post['money'] = $data['money'];
        unset($reqData);
        return $post;
    }

    public static function getVerifyResult ($request)
    {
        $res = $request->all();
        $arr['money'] = $res['money'] / 100;
        $arr['outTradeNo'] = $res['outTradeNo'];
        return $arr;
    }

    public static function getRequestByType ($data)
    {
        $post = $data;
        unset($post['outTradeNo']);
        unset($post['money']);
        $params = json_encode($post['param']);
        return $params;
    }

    //获取随机字符串
    public static function getRandomStr ($len, $special = true)
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        if ( $special ) {
            $chars = array_merge($chars, array(
                "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
                "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
                "}", "<", ">", "~", "+", "=", ",", "."
            ));
        }
        $charsLen = count($chars) - 1;
        shuffle($chars);                            //打乱数组顺序
        $str = '';
        for ($i = 0; $i < $len; $i ++) {
            $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
        }
        return $str;
    }

    public static function httpGet ($url, $data = null, $header = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_URL, $url);
        $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
        if ( $ssl ) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        if ( !empty($data) ) {
            curl_setopt($curl, CURLOPT_POST, 3);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if ( !empty($header) ) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}