<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zhongyizpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        if ($payConf['pay_way'] == '1') {
            $bank     = '901';
        }
        if ($bank =='102'|| $bank =='106'||$bank =='202'||$bank =='302'||$bank =='502'|| $bank == '901'){
            self::$isAPP = true;
        }
        self::$unit= 2;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType='other';
        $data = [];
        $data['amount']       = $amount *100;
        $data['merchNo']      = $payConf['business_num'];//商户编号
        $data['netway'] =  $bank;
        $data['notifyUrl']     = $ServerUrl;//支付结果通知地址
        $data['orderNo']      = $order;//商户订单号
        $data['product']   = 'ipad';
        $data['returnUrl']   = $returnUrl;//版本号
        $data['randomStr']=self::getRandomStr(10, false);
        $signStr              = self::getSignStr($data, true,true);
        $data['sign']      = strtoupper(  self::getMd5Sign("{$signStr}"."&key=", $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $result = json_decode($response, true);

        return $result;

    }

    public static function getVerifyResult($request)
    {
        $data                = $request->all();
        $data['amount']   = $data['amount'] / 100;
        return $data;
    }

    /**
     * @param $type    string 模型名
     * @param $data    array  回调数据
     * @param $payConf array  商户信息
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true,  true);
        $mysign  = self::getMd5Sign("{$signStr}&key=",$payConf['md5_private_key']);
        if(strtoupper($mysign) == strtoupper($sign) && $data['statusCode']= '00' ){
            return true;
        }else{
            return false;
        }
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
}