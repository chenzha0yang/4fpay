<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jinjupay extends ApiModel
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

        self::$unit        = 2;
        $data              = [];
        $data['mechno']    = $payConf['business_num'];//商户号   由平台分配
        $data['orderip']   = '127.0.0.1';//ip地址，必须填写下单客户的手机IP地址，不能填服务器IP
        $data['amount']    = $amount * 100;//支付金额
        $data['body']      = 'zhif';//商户名称
        $data['notifyurl'] = $ServerUrl;//异步通知地址
        $data['returl']    = $returnUrl;//成功时 回调地址
        $data['orderno']   = $order;//商户订单号
        if ($payConf['pay_way'] == '2') {
            $data['payway'] = 'WECHAT';//支付方式
        }
        if ($payConf['pay_way'] == '3') {
            $data['payway'] = 'ALIPAY';//支付方式
        }
        if ($payConf['pay_way'] == '4') {
            $data['payway'] = 'QQ';//支付方式
        }
        $data['paytype'] = $bank;//支付类别

        $signStr      = self::getSignStr($data, false, true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }
}