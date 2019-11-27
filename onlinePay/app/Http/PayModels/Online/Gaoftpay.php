<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Gaoftpay extends ApiModel
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

        //TODO: do something
        self::$reqType = 'curl';
        self::$httpBuildQuery = true;
        self::$payWay = $payConf['pay_way'];
        self::$unit = '2';

        $data = [];
        $data['merchantNo'] = $payConf['business_num'];
        $data['orderAmount'] = $amount * 100;
        $data['orderNo'] = $order;
        $data['notifyUrl'] = $ServerUrl;
        $data['callbackUrl'] = $returnUrl;
        if ( $payConf['pay_way'] == '1' ) {
            $data['bankName'] = $bank;//银行
            $data['currencyType'] = 'CNY';//货币类型
            $data['productName'] = 'apple';//商品名称
            $data['productDesc'] = 'desc';//商品描述
            $data['cardType'] = '1';//必填  1 借记卡
            $data['businessType'] = '1';
        } else {
            $data['payType'] = $bank;
        }
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtolower(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }
}