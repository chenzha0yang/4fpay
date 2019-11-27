<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wangwpay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地
        self::$resType = 'other';
        self::$reqType = 'curl';
        self::$method = 'header';
        self::$payWay = $payConf['pay_way'];
        self::$isAPP = true;

        //开始创建订单，订单生成参数请根据相关参数自行调整。
        $post['paytype'] = $bank; //类型请自行调整
        $post['out_trade_no'] = $order; //订单号
        $post['goodsname'] = "ipad"; //商品名称
        $post['total_fee'] = $amount; //定单金额，不要带小数，必须是整数
        $post['requestip'] = self::getIp(); //IP
        $post['notify_url'] = $ServerUrl; //这个是订单回调地址，成功付款后定时通知队列会调这个地址。
        $post['return_url'] = $returnUrl; //这个是订单回调地址，成功付款后实时跳回这个地址。
        $str =  self::randStr(30); //随机数基本字符串
        $post1['mchid'] = $payConf['business_num']; //商户ID，请自行调整
        $post1['timestamp'] = time(); //时间戳
        $post1['nonce'] = substr(str_shuffle($str), mt_rand(0, strlen($str) - 11), 10); //随机码
        $data = array_merge($post, $post1);
        $signStr = self::getSignStr($data, true, true);
        $post1['sign'] = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        $post1['data'] = $post;
        $data['httpHeaders'] = array( //改为用JSON格式来提交
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($post1))
        );
        $data['data'] = json_encode($post1);
        $data['out_trade_no'] = $order; //订单号
        $data['total_fee'] = $amount; // 金额
        unset($reqData);
        return $data;
    }

    public static function getQrCode($res)
    {
        $result = json_decode($res, true);
        if((int)$result['error'] === 0){
            $result['payurl'] = $result['data']['payurl'];
        }
        return $result;
    }
}