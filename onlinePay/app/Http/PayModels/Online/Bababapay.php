<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bababapay extends ApiModel
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
//        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
//        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';
        self::$unit = 2;
        if($payConf['pay_way'] == 1){
            self::$isAPP = true;
        }
        $data = array(
            'appid'         => $payConf['business_num'],   //商户号
            'productID'     => '1',
            'price'         => $amount * 100,
            'productCount'  => '1',
            'userID'        => '1',
        );
        $signStr      = self::getSignStr($data, true,false);
        $data['sign'] = self::getMd5Sign("{$signStr}&sign=", $payConf['md5_private_key']); //MD5签名
        $data['thirdOrderID'] = $order;
        $data['payType']      = $bank;
        $data['action']       = 'getQRCode';

        unset($reqData);
        return $data;
    }

    /**
     * 二维码链接地址处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $responseData = json_decode($response,true);
        if($responseData['result'] == 0){
            $data['qrcodeContent'] = $responseData['qrcodeContent'];
        }else{
            $data['result'] = $responseData['result'];
        }

        return $data;
    }
}