<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;
use App\Http\Extensions\RedisConPool;

class Guangxinpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        //设置请求方式
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];

        if (RedisConPool::get('accessToken')) {
            //如果有缓存token
            $accessToken = RedisConPool::get('accessToken');
        } else {
            //token 时间过期 重新获取
            $accessToken = self::getAccessToken($payConf['business_num'],$payConf['md5_private_key']);
        }

        //开始支付请求
        self::$method = 'header';
        self::$unit = 2;  //单位：分
        //组装支付请求参数
        $data['accessToken']         = $accessToken;
        $data['param']['outTradeNo'] = $order;
        $data['param']['money']      = $amount * 100;
        $data['param']['type']       = 'T0';
        $data['param']['body']       = 'goodsName';
        $data['param']['detail']     = 'PK';
        $data['param']['notifyUrl']  = $ServerUrl;
        $data['param']['productId']  = $order;
        $data['param']['successUrl'] = $returnUrl;

        $post['data']        = json_encode($data, true);
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
        );
        $post['money']       = $amount * 100;
        $post['outTradeNo']  = $order;

        unset($reqData);
        return $post;
    }

    public static function getAccessToken($business_num,$key)
    {
        //获取accessToken地址
        $getTokenUrl = 'http://api.6899q.cn/open/v1/getAccessToken/merchant';

        //组装获取accessToken参数
        $tokenData          = array(
            'merchantNo' => $business_num,
            'nonce'      => self::randStr(16),
            'timestamp'  => time(),
            'key'        => $key,
        );
        $signStr            = self::getSignStr($tokenData, true, false);
        $tokenData['sign']  = strtoupper(md5($signStr));
        $tokenData['token'] = '';
        $json               = json_encode($tokenData);

        //请求得到accessToken
        Curl::$request = $json;//提交数据
        Curl::$url     = $getTokenUrl;//获取accessToken 网关
        Curl::$method  = 'header';//提交方式
        Curl::$header  = ['Content-Type: application/json; charset=utf-8'];

        //得到响应结果
        $tokenResult = json_decode(Curl::Request(), true);
        //存token
        RedisConPool::setEx(
            'accessToken',
            7000,
            $tokenResult['value']['accessToken']
        );
        return $tokenResult['value']['accessToken'];
    }

}