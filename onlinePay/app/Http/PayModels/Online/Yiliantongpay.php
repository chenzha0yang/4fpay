<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yiliantongpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$unit = 2;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay  = $payConf['pay_way'];
        self::$postType = true;
        if (1 == $payConf['is_app']) {  //H5
            self::$isAPP = true;
        }

        //TODO: do something
        $data = [
            'funCode'     => '2005',                     //交易类型
            'platOrderId' => $order,                     //订单号
            'platMerId'   => $payConf['business_num'],   //商户ID
            'tradeTime'   => time(),
            'amt'         => $amount*100,                //金额
            'body'        => 'VIP',
            'subject'     => 'VIP',
            'channelId'   => 'NO',
            'funName'     => 'prepay',
            'orderTime'   => 20,
            'notifyUrl'   => $ServerUrl,                 //异步通知地址
            'frontUrl'    => $returnUrl,                 //同步通知地址
        ];
        if('1' == $payConf['pay_way']){ //网银
            $data['tradeType'] = $bank;
            $data['payMethod'] = '7';
        }else{
            $data['payMethod'] = $bank;
        }

        $signStr = self::getSignStr($data,true,true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=",$payConf['md5_private_key'])); //MD5签名
        $json = "reqJson=" . json_encode($data);
        //将json字符串放入新数组
        $post= [
            'json'          => $json,
            'platOrderId'   => $data['platOrderId'],
            'amt'           => $data['amt']
        ];

        unset($reqData);
        return $post;
    }

    /**
     * 提交数据
     * @param $data
     * @return mixed
     */
    public static function getRequestByType($data)
    {
        return $data['json'];
    }

    /**
     * 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $result = json_decode($response,true);
        if('0000' == $result['retCode']){
            $data['codeUrl']  = $result['codeUrl'];
            $data['shortUrl'] = $result['shortUrl'];
        }else{
            $data['retCode'] = $result['retCode'];
            $data['retMsg']  = $result['retMsg'];
        }
        return $data;
    }
}