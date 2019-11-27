<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qibapay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $array = [];

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$isAPP = true;
        self::$unit = 2;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method = 'header';
        //得到13位时间戳
        list($t1, $t2) = explode(' ', microtime());
        $time =(float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
        $payInfo = explode('@', $payConf['business_num']);

        $data['mch_id'] = $payInfo[0];
        $data['service_id'] = $payInfo[1];
        $data['pay_type'] = $bank;
        $data['total_fee'] = $amount * 100;
        $data['title'] = 'goodsDesc';
        $data['description'] = 'nice';
        $data['out_trade_no_mch'] = $order;
        $data['notify_url'] = $ServerUrl;
        $data['callback_url'] = $returnUrl;
        $data['timestamp'] = $time;
        $data['ip'] = self::getIp();
        $data['version'] = '1';
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr.'&'.$payConf['md5_private_key']));

        $post['data'] = json_encode($data);
        $post['httpHeaders'] = [
            "Content-Type: application/json; charset=utf-8"
        ];
        $post['out_trade_no_mch'] = $data['out_trade_no_mch'];
        $post['total_fee'] = $data['total_fee'];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response,true);
        if(isset($data['status']) || isset($data['payUrl'])){
            return $data;
        }else {
            echo $response;
            die;
        }
    }

    //回调金额处理
    public static function getVerifyResult($request)
    {
        $res = $request->getContent();
        $data = json_decode($res,true);
        self::$array = $data;
        $data['amount'] = $data['total_fee'] / 100;
        return $data;
    }

    public static function SignOther($type, $json, $payConf)
    {
        $data = self::$array;
        $sign = $data['sign'];
        $mySign = strtoupper(md5("out_trade_no_mch=".$data['out_trade_no_mch']."&total_fee=".$data['total_fee']."&version=".$data['version'].'&'.$payConf['md5_private_key']));
        if (strtoupper($sign) == $mySign && $data['status'] == '0' ) {
            return true;
        } else {
            return false;
        }
    }


}
