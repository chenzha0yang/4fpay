<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayMerchant;

class Zuanshipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signData = [];

    /**
     * @param array       $reqData 接口传递的参数
     * @param PayMerchant $payConf object PayMerchant类型的对象
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

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;


        self::$isAPP = true;


        $data['appKey']   = $payConf['business_num'];
        $data['outOrderId'] = $order;
        $data['orderFund'] = $amount;
        $data['callbackUrl'] = $ServerUrl;
        $data['payType'] = $bank;

        $str =  'appKey='.$data['appKey'].'outOrderId='.$data['outOrderId'].'orderFund='.$data['orderFund'].'callbackUrl='.$data['callbackUrl'].'key='.$payConf['md5_private_key'];
        $data['sign'] = md5($str);
        unset($reqData);
        return $data;

    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);

        $str =  'appKey='.$payConf['business_num'].'outOrderId='.$data['outOrderId'].'orderFund='.$data['orderFund'].'orderId='.$data['orderId'].'realOrderFund='.$data['realOrderFund'].'key='.$payConf['md5_private_key'];
        $mySign = md5($str);
        if ($mySign == $sign) {
            return true;
        }
        return false;
    }



    /**
     * 二维码链接处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){

        $responseData = json_decode($response,true);

        $data = [];
        if($responseData['code'] == '0'){
            $data['url'] = $responseData['data']['pcUrl'];
        }
        return $data;
    }

}