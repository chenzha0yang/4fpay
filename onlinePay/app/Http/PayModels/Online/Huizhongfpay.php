<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huizhongfpay extends ApiModel
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

        self::$isAPP = true;
        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method = 'get';
        self::$resType = 'other';

        $data['mch_id']     = $payConf['business_num'];//商户号
        $data['trade_type']     = $bank;//银行编码
        $data['amount']       = $amount * 100;//订单金额
        $data['out_trade_no']     = $order;//订单号
        $data['notify_url']   = $ServerUrl;
        $data['back_url'] = $returnUrl;
        $data['attach']      = 'test';
        $data['mch_create_ip'] = self::getIp();
        $data['body'] = 'test';

        $signStr                 = self::getSignStr($data, true, true);
        $data['sign']            = md5($signStr .'&key='. $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['respCode'] == '00000') {
            $data['payUrl'] = $data['payUrl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        return $res;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $post = file_get_contents('php://input');
        $data  = json_decode($post,true);
        $sign = $data['sign'];
        $respCode = $data['respCode'];
        unset($data['sign']);
        unset($data['respCode']);
        unset($data['respMsg']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = md5($signStr .'&key='. $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $respCode == '00000') {
            return true;
        }
        return false;
    }

    //获取随机字符串
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
            $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
        }
        return $str;
    }
}