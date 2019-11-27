<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jingzhunpay extends ApiModel
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

        $data = [];
        if ($payConf['is_pc_wap'] == '2') {
            $api = 'WAP_PAY_B2C';
        } else {
            $api = 'WEB_PAY_B2C';
        }
        $data['apiName']      = $api;
        $data['apiVersion']   = '1.0.0.0';//版本号
        $data['platformID']   = $payConf['pay_id'];//商户ID
        $data['merchNo']      = $payConf['business_num'];//商户编号
        $data['orderNo']      = $order;//商户订单号
        $data['tradeDate']    = date('Ymd');//订单日期
        $data['amt']          = number_format($amount, 2, '.', '');//订单金额
        $data['merchUrl']     = $ServerUrl;//支付结果通知地址
        $data['merchParam']   = 'param';
        $data['tradeSummary'] = 'sum';
        $signStr              = self::getSignStr($data, false);
        $data['signMsg']      = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        if ($payConf['pay_way'] == '1') {
            $data['bankCode']      = $bank;
            $data['choosePayType'] = '1';
        } else {
            $data['bankCode'] = '';
        }
        unset($reqData);
        return $data;
    }
}