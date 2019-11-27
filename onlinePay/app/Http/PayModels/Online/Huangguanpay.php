<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huangguanpay extends ApiModel
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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$unit = 2;
        $data = [];
        $data['merId'] = $payConf['business_num'];//商户号
        $data['merOrderId'] = $order;//商户订单号
        $data['subject'] = 'zhif';//商品标题
        $data['body'] = 'abc';//商品描述
        if ($payConf['pay_way'] == '1') {
            $data['bizType'] = '61';//业务类型
        } else {
            $data['bizType'] = $bank;//业务类型
        }

        $data['txnType'] = '00';//交易类型
        $data['txnSubType'] = '01';//交易子类型
        if ($payConf['pay_way'] == '1') {
            $data['bankCode'] = $bank;//银行代码
            $data['cardType'] = '1';//银行代码
            $data['userType'] = '1';//银行代码

        }
        $data['txnAmt'] = $amount * 100;//交易金额
        $data['currency'] = 'CNY';//交易币种
        $data['notifyUrl'] = $ServerUrl;//后台通知地址
        $data['returnUrl'] = $returnUrl;//成功跳转地址
        $data['sendIp'] = '1.1.1.1';//商户终端IP
        $data['txnTime'] = date('YmdHis');
        $data['attach'] = 'abc';
        $signStr = self::getSignStr($data, false, true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        $data['signMethod'] = 'MD5';

        unset($reqData);
        return $data;
    }

}