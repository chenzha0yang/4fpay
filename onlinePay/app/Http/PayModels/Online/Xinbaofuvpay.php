<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinbaofuvpay extends ApiModel
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

        self::$unit = 2;

        $data['nonceStr'] = self::getRandomStr(30, true);
        $data['merchantNo'] = $payConf['business_num'];//商户号
        if($payConf['pay_way'] == 1){
            $data['productCode'] = '1601';
            $data['bankCode'] = $bank;
        }else{
            $data['productCode'] = $bank;//银行编码
        }
        $data['outOrderNo'] = $order;//订单号
        $data['amount'] = $amount*100;//订单金额
        $data['startTime'] = date('YmdHis',time());
        $data['timestamp'] = time();
        $data['client_ip'] = self::getIp();
        $data['returnUrl'] = $returnUrl;
        $data['notifyUrl'] = $ServerUrl;
        $signStr      = self::getSignStr($data, true,true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key'])); //MD5签名

        unset($reqData);
        return $data;
    }


    //回调金额化分为元
    public static function getVerifyResult($request)
    {
        $data = $request->all();
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }

    //获取随机字符串
    public static function getRandomStr($len, $special=true){
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        if($special){
            $chars = array_merge($chars, array(
                "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
                "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
                "}", "<", ">", "~", "+", "=", ",", "."
            ));
        }
        $charsLen = count($chars) - 1;
        shuffle($chars);                            //打乱数组顺序
        $str = '';
        for($i=0; $i<$len; $i++){
            $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
        }
        return $str;
    }


    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr      = self::getSignStr($data, true,true);
        $signTrue = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key'])); //MD5签名
        if (strtoupper($sign) == $signTrue && $data['orderStatus'] == '1') {
            return true;
        } else {
            return false;
        }
    }

}