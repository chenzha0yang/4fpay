<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Ruiyingpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //TODO: do something
        if($payConf['pay_way'] != 6){
            self::$isAPP = true;
        }
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $context               = [];
        $context['merchant_id']    = $payConf['business_num'];
        $context['product_id'] = $bank;//银行编码
        $context['payment_id'] = $bank;
        $context['order_id']    = $order;
        $context['user_no'] = self::$UserName;
        $context['amount']     = sprintf('%.2f', $amount);
        $context['body'] = 'test';
        $context['notify_url']  = $ServerUrl;
        $context['html']     = 'html';
        $context['nonce_str'] = self::getRandomStr(30,false);
        ksort($context);
        $signStr = '';
        foreach ($context as $key => $value) {
            $signStr .= $value;
        }
        $context['sign'] = md5($signStr.$payConf['md5_private_key']);

        unset($reqData);
        return $context;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response,true);
        if ($data['code'] == '00') {
            $data['payUrl'] = $data['pay_url'];
        }
        return $data;
    }

    public static function signOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $value;
        }
        $myStr = md5($signStr.$payConf['md5_private_key']);
        if (strtoupper($myStr) == strtoupper($sign) && $data['status'] == '3') {
            return true;
        } else {
            return false;
        }
    }

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
}