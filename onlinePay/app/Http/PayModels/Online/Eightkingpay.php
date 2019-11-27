<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Eightkingpay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址

        self::$unit           = 2;
        $data                 = array();
        $data['attach']       = $order;//附加信息
        $data['body']         = 'apple';//商品描述
        $data['cid']          = $payConf['business_num'];//商户号
        $data['create_ip']    = '127.0.0.1';
        $data['notify_url']   = $ServerUrl;
        $data['out_trade_no'] = $order;//订单
        $data['paytype']      = $bank;
        $data['return_url']   = $returnUrl;
        $data['time_expire']  = '';
        $data['time_start']   = '';
        $data['total_fee']    = $amount * 100;

        $signStr        = self::getSignStr($data, false, true);
        $data['sign']   = md5(strtolower($signStr . 'ckey=' . $payConf['md5_private_key']));//签名
        $data['payway'] = $payConf['pay_way'];
        unset($reqData);
        return $data;
    }
}