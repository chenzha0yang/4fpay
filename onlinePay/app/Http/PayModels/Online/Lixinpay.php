<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Lixinpay extends ApiModel
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
        $order = $reqData['order'];//订单号
        $amount = $reqData['amount'];//金额
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$unit = '2';
        if (1 != $payConf['pay_way'] || 9 != $payConf['pay_way']) {
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
        }
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        $data = array();
        $data['amount']         = (string)($amount * 100); //交易金额，以“分”为单位，不能带小数点
        $data['subject']        = "VIP"; //商品名称
        $data['body']           = "miaoshu";  //商品描述
        $data['paymentType']    = $bank;  //支付类型
        $data['notifyUrl']      = $ServerUrl;//后台回调地址
        $data['frontUrl']       = $returnUrl;//前台回调地址
        $data['spbillCreateIp'] = '127.0.0.1';//客户端ip
        $data['tradeNo']        = $order;//商户订单号
        $data['merchantNo']     = $payConf['business_num'];//商户号
        $data['operationCode']  = "order.createOrder";//业务类型
        $data['version']        = "1.0";//接口版本号
        $data['date']           = time(); //当前时间戳

        if ($payConf['pay_way'] == 1) {
            $data['paymentType'] = 'BANK_GATEWAY';
            $data['bankCode'] = $bank;
        }
        if ($payConf['pay_way'] == 9) {
            $data['paymentType'] = 'KUAIJIE';
            $data['bankCode'] = $bank;
        }

        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&appkey=", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

}