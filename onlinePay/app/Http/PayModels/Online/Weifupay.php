<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Weifupay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method  = 'header';
        self::$isAPP   = true;
        $data = array(
            'BuCode'        => $payConf['business_num'],
            'OrderId'       => $order,
            'PayChannel'    => $bank,
            'OrderAccount'  => $payConf['business_num'],
            'Amount'        => $amount,
        );

        $signStr = self::getSignStr($data, true, false);
        $data['Sign'] = self::getMd5Sign("{$signStr}&Key=",$payConf['md5_private_key']);//签名
        $data['NotifyURL'] = $ServerUrl;//签名方式
        $post = [];
        $post['httpHeaders'] = array(
            'Content-Type: charset=utf-8;application/json',
        );
        $post['data']    = json_encode($data);
        $post['OrderId'] = $data['OrderId'];
        $post['Amount']  = $data['Amount'];

        unset($reqData);
        return $post;
    }

    /**
     * 二维码链接处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $responseData = json_decode($response,true);
        if($responseData['status'] == true){
            $data['redirectURL']   = $responseData['data']['redirectURL'];
        }else{
            $data['err_code']   = $responseData['err_code'];
            $data['err_msg']    = $responseData['err_msg'];
        }

        return $data;
    }
}