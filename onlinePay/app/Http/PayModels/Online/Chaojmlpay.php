<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Chaojmlpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $array = []; // 判断是否跳转APP 默认false

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


        //TODO: do something
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method = 'header';
        self::$resType = 'other';

        $data['amount']       = $amount;
        $data['outOrderNo']       = $order;
        $data['payType']       = $bank;
        $data['attach']       = 'PK订单';
        $data['orderDesc']       = 'nick';
        $data['timestamp']       = time()*1000;
        $data['nonceStr']       = self::createNonceStr(10);
        $data['returnUrl']       = $returnUrl;
        $data['notifyUrl']       = $ServerUrl;
        $data['appId']       = $payConf['business_num'];
        $data['userUnqueNo']       = $payConf['business_num'];
        $data['signature']  =self::getSign($data['outOrderNo'],$data['amount'],$data['payType'],$data['attach'],$data['appId'],$data['timestamp'],$data['nonceStr'],$payConf['md5_private_key']);

        $post['data'] = json_encode($data);
        $post['httpHeaders'] = [
            'Content-Type: application/json; charset=utf-8',
        ];
        $post['amount'] = $data['amount'];
        $post['outOrderNo'] = $data['outOrderNo'];

        unset($reqData);
        return $post;
    }

    public static function getSign($outOrderNo,$amount,$payType,$attach,$appId,$timestamp,$nonceStr,$secret){
        $params=array("outOrderNo"=>$outOrderNo,"amount"=>$amount,"payType"=>$payType,"attach"=>$attach);
        ksort($params);//把key，从小到大排序
        $paramUrl = "?".http_build_query($params, '' , '&');//封装成url参数
        $md5Value=strtolower(md5($paramUrl.$appId.$timestamp.$nonceStr));
        return strtoupper(md5($md5Value.$secret));
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['code'] == '1') {
            $result['returnUrl'] = $result['data']['returnUrl'];
        }
        return $result;
    }

    public static function getVerifyResult($request, $mod)
    {
        $json=$request->getContent();
        $data = json_decode($json,true);
        self::$array = $data;
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $data = self::$array;
        $sign    = $data['signature'];
        $signTrue = self::getSign($data['outOrderNo'],$data['amount'],$data['payType'],$data['attach'],$payConf['business_num'],$data['timestamp'],$data['nonceStr'],$payConf['md5_private_key']);
        if (strtoupper($sign) == $signTrue && $data['status'] == '2') {
            return true;
        } else {
            return false;
        }
    }

    public static function createNonceStr($length = 10)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}