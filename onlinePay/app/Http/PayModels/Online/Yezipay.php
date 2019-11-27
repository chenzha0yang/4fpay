<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yezipay extends ApiModel
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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        $data                 = [];
        $merchant             = explode('@', $payConf['business_num']);
        $data['merchantNo']   = $merchant[0]; // 商户号
        $data['merchantName'] = $merchant[1]; // 商户名称
        $data['payKey']       = $merchant[2]; // 登陆商户后台-账户信息，获取支付key（非密钥，我方会根据该参数和商户号参数判断通道配置是否正确）
        $data['payWayCode']   = $bank; // 支付通道： 微信 : WEIXIN 支付宝 : ALIPAY 银联 : UNIONPAY
        $data['orderNo']      = $order; // 订单号
        $data['payGateWay']   = '10002'; // 产品类型编号（10002）我方会定期提供该参数调整的
        $data['productName']  = $order . $reqData['username']; // 产品名称：（平台玩家id+zfcs+orderNo） 备注：平台玩家ID必须唯一，用于区分公众号首次授权
        $data['orderPrice']   = $amount; // 订单金额（单位：元）
        $data['returnUrl']    = $returnUrl; // 支付成功跳转地址
        $data['notifyUrl']    = $ServerUrl; // 异步通知地址
        $data['orderPeriod']  = '30'; // 订单有效期（单位：分钟）如传入10，则订单有效期为10分钟
        $data['ismobile']     = 0; // H5-1，扫码-0
        $data['orderDate']    = date('Y-m-d H:i:s', time()); // 下单日期（yyyy-MM-dd HH:mm:ss）
        $data['orderTime']    = date('Y-m-d H:i:s', time()); // 与orderDate值相同加入验签
        $signStr              = self::getSignStr($data, true, true);
        $data['sign']         = strtoupper(self::getMd5Sign("{$signStr}&paySecret=", $payConf['md5_private_key'])); // MD5签名结果
        unset($reqData);
        return $data;
    }

    public static function SignOther($mod, $signData, $payConf)
    {
        $sign = strtoupper($signData['sign']);
        unset($signData['sign']);
        unset($signData['msg']);
        $signStr  = self::getSignStr($signData, true, true);
        $signTrue = strtoupper(self::getMd5Sign("{$signStr}&paySecret=", $payConf['md5_private_key'])); // MD5签名结果
        if ($sign == $signTrue) {
            return true;
        } else {
            return false;
        }
    }
}