<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Motttopay extends ApiModel
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

        $data['merchantId'] = $payConf['business_num'];
        $data['orderSn'] = $order;
        $data['type'] = '1';
        $data['currency'] = '1';
        $data['amount'] = $amount * 100;
        $data['notifyUrl'] = $ServerUrl;
        $data['returnUrl'] = $returnUrl;
        $data['goodsName'] = 'goods_name';
        $data['sign'] = md5($data['merchantId'].$data['orderSn'].$data['currency'].$data['amount'].$data['notifyUrl'].$data['type'].$payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response,true);
        if( $data['code'] == '200'){
            $data['gopayUrl']  = $data['data']['gopayUrl'];
        }
        return $data;
    }

    //回调金额处理
    public static function getVerifyResult($request)
    {
        $res = $request->getContent();
        $data = json_decode($res,true);
        self::$array = $data;
        $data['amount'] = $res['amount'] / 100;
        return $data;
    }

    public static function SignOther($type, $json, $payConf)
    {
        $data = self::$array;
        $sign = $data['sign'];
        $mySign = strtoupper(md5($data['merchantId'].$data['orderSn'].$data['amount'].$payConf['md5_private_key']));
        if (strtoupper($sign) == $mySign && $data['status'] == '3' ) {
            return true;
        } else {
            return false;
        }
    }


}
