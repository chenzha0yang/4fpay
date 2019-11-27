<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Yifubaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /**
     * @param array       $reqData 接口传递的参数
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
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        self::$unit    = 2;
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        //TODO: do something
        $data = array(
            'mchntCode'         => $payConf['business_num'],
            'channelCode'       => $bank,
            'mchntOrderNo'      => $order,
            'orderAmount'       => $amount * 100,
            'clientIp'          => request()->ip(),
            'subject'           => 'goodsName',
            'body'              => 'goodsName',
            'notifyUrl'         => $ServerUrl,
            'pageUrl'           => $returnUrl,
            'orderTime'         => date('YmdHis'),
            'description'       => $order,
            'orderExpireTime'   => date('YmdHis',time()+300),
            'ts'                => self::ts_time('YmdHisu'),
        );
        $signStr = self::getSignStr($data,true,true);
        $data['sign'] = strtoupper(self::getMd5Sign($signStr,$payConf['md5_private_key']));
        //请求路径
        $url = $reqData['formUrl'];
        //组合新数组
        $post['url']         = $url;
        $post['mchntOrderNo'] = $data['mchntOrderNo'];
        $post['orderAmount']  = $data['orderAmount'];
        $post['data'] = $data;

        unset($reqData);
        return $post;
    }

    public static function getQrCode($response){
        Curl::$method = 'header';
        Curl::$headerToArray = true;
        $post = $response['data']['data'];
        Curl::$header = [
            'Content-Type: application/json',
        ];
        Curl::$request = json_encode($post);
        //请求网关
        Curl::$url = $response['data']['url'];
        //请求
        $result = Curl::Request();
        $responseData = json_decode($result['body'],true);
        if($result['status'] == '200' && $responseData['retCode'] == '0000'){
            $data['codeUrl'] = $responseData['codeUrl'];
        }else{
            $data['retCode'] = $responseData['retCode'];
            $data['retMsg']  = $responseData['retMsg'];
        }

        return $data;
    }

    /**
     * @param string $format
     * @param null $utimestamp
     * @return false|string
     */
    public static function ts_time($format = 'u', $utimestamp = null) {
        if (is_null($utimestamp)){
            $utimestamp = microtime(true);
        }

        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }

}