<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Antspay extends ApiModel
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

        self::$isAPP = true;
        self::$unit = 2;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        $data['app_id']      = $payConf['business_num'];
        $data['nonce_str'] = self::getRandomStr(30,false);
        $data['trade_type']     = $bank;
        $data['total_amount']       = $amount*100;
        $data['out_trade_no']   = $order;
        $data['trade_time'] = date('Y-m-d H:i:s',time());
        $data['notify_url'] = $ServerUrl;
        $data['user_ip'] = self::getIp();

        $signStr = self::getSignStr($data,true,true);
        $data['sign']        = strtoupper(md5($signStr.'&key='.$payConf['md5_private_key']));

        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['out_trade_no'] = $data['out_trade_no'];
        $postData['total_amount']  = $data['total_amount'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '0000') {
            $data['qrCode'] = $data['code_url'];
        }else{
            if(isset($data['sub_msg'])){
                $data['msg'] = $data['sub_msg'];
            }
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $data['out_trade_no'] = $res['out_trade_no'];
        $data['total_amount'] = $res['total_amount']/100;
        return $data;
    }
    
    public static function signOther($model, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $sign     = $data['sign'];
        $amount = $data['total_amount'];
        unset($data['sign']);
        unset($data['code']);
        unset($data['msg']);
        unset($data['sub_code']);
        unset($data['sub_msg']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue = strtoupper(md5($signStr.'&key='.$payConf['md5_private_key']));
        if ($signTrue == strtoupper($sign) && $data['trade_status'] == 1) {
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