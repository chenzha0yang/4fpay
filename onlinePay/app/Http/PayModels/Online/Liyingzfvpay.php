<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Liyingzfvpay extends ApiModel
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

        $data['mch_id'] = $payConf['business_num'];//商户号
        if ($payConf['pay_way'] == 0) {
            $data['trade_type'] = '10';
            $data['bank_id']    = $bank;
        } else {
            $data['trade_type'] = $bank;//银行编码
            $data['bank_id']    = '';
        }
        $data['out_trade_no'] = $order;//订单号
        $data['total_fee']    = $amount * 100;//订单金额
        $data['time_start']   = date('YmdHis', time());
        $data['return_url']   = $returnUrl;
        $data['notify_url']   = $ServerUrl;
        $data['nonce_str']    = self::getRandomStr(30, false);
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $value) {
            if ($key == 'return_url' || $key == 'notify_url') {
                $value = urlencode($value);
            }
            $signStr .= $key . "=" . $value . "&";
        }
        $data['sign'] = strtoupper(md5($signStr . "key=" . $payConf['md5_private_key']));

        unset($reqData);
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

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['total_fee'])) {
            $arr['total_fee'] = $arr['total_fee'] / 100;
        }
        return $arr;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $key . "=" . $value . "&";
        }
        $signTrue = strtoupper(md5($signStr . "key=" . $payConf['md5_private_key']));
        if (strtoupper($sign) == $signTrue) {
            return true;
        }
        return false;
    }

}