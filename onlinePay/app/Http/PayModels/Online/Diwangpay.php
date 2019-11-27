<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Diwangpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

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

        self::$unit    = 2;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method  = 'header';
        self::$isAPP   = true;

        //TODO: do something
        $data = [
            'SignMethod' => 'MD5',
            'UserId'     => $order,
            'Version'    => '1.0',
            'MerNo'      => $payConf['business_num'],   //商户号
            'TxSN'       => $order,
            'Amount'     => $amount * 100,
            'PdtName'    => 'VIP',
            'NotifyUrl'  => $ServerUrl,                 //异步通知地址
            'ReqTime'    => date('YmdHis'),
        ];
        if($payConf['pay_way'] == 1){   //网银
            $data['TxCode']    = 'b2cpayplat';
            $data['ProductId'] = '0612';
        }
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {  //H5
            $data['ProductId'] = $bank;
            $data['TxCode']    = 'h5pay';
        }else{
            $data['ProductId'] = $bank;
            $data['TxCode']    = 'qrpay';
        }
        ksort($data);
        $signStr    = '';
        $base64Keys = ['CodeUrl', 'ImgUrl', 'Token_Id', 'PayInfo', 'sPayUrl', 'PayUrl'
            , 'NotifyUrl', 'ReturnUrl'];
        foreach ($data as $key => $value) {
            if ($key != 'SignMethod' && $key != 'Signature') {
                $signStr .= $key . '=' . $value . '&';
            }
        }
        $signStr = rtrim($signStr,'&') . $payConf['md5_private_key'];
        $data['Signature'] = md5($signStr);
        $reqMsg = '';
        foreach ($data as $key => $value) {
            if (in_array($key, $base64Keys,true)) {
                $value = base64_encode($value);
                $value = str_replace("+", "%2b", $value);
            }
            $reqMsg .= sprintf("%s=%s&", $key, urlencode($value));
        }
        $reqMsg = rtrim($reqMsg,'&');
        //提交的参数
        $post['data'] = $reqMsg;
        //header头
        $post['httpHeaders'] = [
            'content-type: application/x-www-form-urlencoded; charset=UTF-8'
        ];
        $post['TxSN']   = $data['TxSN'];
        $post['Amount'] = $data['Amount'];

        unset($reqData);
        return $post;
    }

    /**
     * 二维码及语言包处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $res    = urldecode($response);
        //组合成数组
        parse_str($res, $result);
        //将64位二维码转换为地址
        if($result['Status'] == '1'){
            $url = base64_decode($result['ImgUrl']);
            $data['ImgUrl'] = urldecode(urlencode($url));
        }else{
            $data['RspCod'] = $result['RspCod'];
            $data['RspMsg'] = $result['RspMsg'];
        }
        return $data;
    }

    /**
     * 回掉签名处理
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf){
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $value) {
            if ($key != 'SignMethod' && $key != 'Signature') {
                $signStr .= $key . '=' . $value . '&';
            }
        }
        $signStr = rtrim($signStr,'&') . $payConf['pay_key'];
        $signTrue = md5($signStr);
        if ($data['Signature'] == $signTrue && $data['Status'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}