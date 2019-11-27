<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jiudingpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $datas = []; // 判断是否跳转APP 默认false




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
        $ServerUrl = $reqData['ServerUrl'];// 如、异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$method = 'header';
        self::$reqType='curl';
        self::$payWay = $payConf['pay_way'];
        self::$isAPP=true;
        self::$unit=2;//金额为分

        $data['merchantId'] =(int)$payConf['business_num'];
        $data['outTradeNo'] = $payConf['business_num'].$order;
        $data['body'] = 'nick';
        $data['totalFee'] = (int)$amount*100;
        $data['payType'] = $bank;
        $data['payMode']='perCode';

        $data['attach']=$order;
        $data['notifyUrl'] = $ServerUrl;
        $data['callbackUrl'] = $returnUrl;
        $data['nonceStr'] = md5(time());


        $signStr = self::getSignStr($data,true,true);
        $data['sign'] = strtoupper(self::getMd5Sign($signStr.'&key=',$payConf['md5_private_key']));
        $post['data'] = json_encode($data);
        $post['httpHeaders'] = [
            "Content-Type: application/json; charset=utf-8"
        ];
        $post['order']=$order;
        $post['amount']=$data['totalFee'];
        unset($reqData);
        return $post;
    }

    //回调金额处理
    public static function getVerifyResult($request)
    {
        $res                = $request->getContent();
        $arr                = json_decode($res, true);
        $data['payMoney']   = $arr['totalFee'] / 100; //金额
        $data['outTradeNo'] = $arr['attach']; //订单号
        self::$datas=$arr;
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $data=self::$datas;
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue = strtoupper(self::getMd5Sign($signStr.'&key=',$payConf['md5_private_key']));
        if (strtoupper($sign) == $signTrue ) {
            return true;
        } else {
            return false;
        }
    }

}
