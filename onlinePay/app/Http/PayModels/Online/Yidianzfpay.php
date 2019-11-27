<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yidianzfpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        $data = array(
            "mch_id" => $payConf['business_num'], // 商户ID
            "out_trade_no" =>  $order, // 必填:商户自己的订单号
            "amount" =>  $amount * 100, // 必填:金额,单位:分
            "product_name" =>  'test', //非必填:产品名称
            "notify_url" =>  $ServerUrl,  //异步通知地址,不能有参数. 接口返回:success则通知成功,否则将阶梯性重试.
            "return_url" =>  $returnUrl,  //用户支付完成后跳转地址,只有部分渠道有效
            "trade_type" =>  $bank,  //支付渠道
            "content_type" =>  'text',  //返回类型 text（form方式提交） 或 json
            "nonce_str" =>  self::getRandomStr(30,false) //随机字符串
        );
        $signStr                 = self::getSignStr($data, true, true);
        $data['sign']            = md5(utf8_encode($signStr .'&'. $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }


    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['amount'])) {
            $arr['amount'] = $arr['amount'] / 100;
        }
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = md5(utf8_encode($signStr .'&'. $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue) && $data['status'] == 'success') {
            return true;
        }
        return false;
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