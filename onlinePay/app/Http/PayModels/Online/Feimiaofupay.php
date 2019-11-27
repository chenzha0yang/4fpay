<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Feimiaofupay extends ApiModel
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

        $data                       = [];
        $data['merchant_code']      = $payConf['business_num']; //商家号
        $data['service_type']       = $bank;
        $data['notify_url']         = $ServerUrl; //服务器异步通知地址
        $data['interface_version']  = 'V3.1';//接口版本
        $data['order_no']           = $order; //商户网站唯一订单号
        $data['client_ip']          = '127.0.0.1'; //客户端IP
        $data['order_time']         = date('Y-m-d h:i:s'); //商户订单时间
        $data['order_amount']       = $amount; //商户订单总金额
        $data['product_name']       = 'product'; //商品名称
        $data['product_code']       = '';
        $data['product_num']        = '';
        $data['product_desc']       = '';
        $data['extend_param']       = '';
        $data['extra_return_param'] = '';
        $signStr                    = self::getSignStr($data, false, true);
        $pay_key                    = openssl_get_privatekey($payConf['rsa_private_key']);
        openssl_sign($signStr, $sign_info, $pay_key, OPENSSL_ALGO_MD5);
        $sign = base64_encode($sign_info);

        $data['sign']      = $sign;//签名
        $data['sign_type'] = 'RSA-S';//签名方式

        unset($reqData);
        return $data;
    }

}