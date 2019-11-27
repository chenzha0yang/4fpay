<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Weisutpay extends ApiModel
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
    public static function getAllInfo ($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //TODO: do something
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        $data = array();
        $data['merchant_code'] = $payConf['business_num']; //商家号
        $data['service_type'] = $bank;
        $data['notify_url'] = $ServerUrl; //服务器异步通知地址
        $data['interface_version'] = 'V3.1';//接口版本
        if ( $payConf['pay_way'] == 1 || $payConf['pay_way'] == 9 ) {
            $data['service_type'] = 'direct_pay';
            $data['interface_version'] = 'V3.0';//接口版本
            $data['input_charset'] = 'UTF-8';
            $data['return_url'] = '';
            $data['pay_type'] = '';
            $data['client_ip_check'] = '';
            $data['bank_code'] = 'b2c';
            if ( $payConf['pay_way'] == 9 ) {
                $data['bank_code'] = $bank;
            }
            $data['redo_flag'] = '1';
            $data['show_url'] = '';
            $data['orders_info'] = '';
        }
        $data['client_ip'] = '127.0.0.1'; //客户端IP
        $data['order_no'] = $order; //商户网站唯一订单号
        $data['order_time'] = date('Y-m-d h:i:s'); //商户订单时间
        $data['order_amount'] = $amount; //商户订单总金额
        $data['product_name'] = 'product'; //商品名称
        $data['product_code'] = '';
        $data['product_num'] = '';
        $data['product_desc'] = '';
        $data['extend_param'] = '';
        $data['extra_return_param'] = '';
        $signStr = self ::getSignStr($data, true, true);
        $pay_key = openssl_get_privatekey($payConf['rsa_private_key']);
        openssl_sign($signStr, $sign_info, $pay_key, OPENSSL_ALGO_MD5);
        $sign = base64_encode($sign_info);
        $data['sign_type'] = 'RSA-S';//签名方式
        $data['sign'] = $sign;//签名
        if ( $payConf['pay_way'] <> '1' ) {
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
            self::$httpBuildQuery = true;
            self::$resType = 'other';
        }
        unset($reqData);
        return $data;
    }

    public static function getQrCode ($res, $payConf)
    {
        $result = json_decode(json_encode(simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        if ( $result['response']['resp_code'] == 'SUCCESS' && $result['response']['result_code'] == '0' ) {
            if(isset($result['response']['payURL'])){
                $payURL = urldecode($result['response']['payURL']);
            }else{
                $payURL = urldecode($result['response']['qrcode']);
            }
            $data['payURL'] = $payURL;
        } else {
            if (!isset($result['response']['result_code'])) $data['code'] = $result['response']['resp_code'];
            else $data['code'] = $result['response']['result_code'];

            if (!isset($result['response']['result_desc'])) $data['msg'] = $result['response']['resp_desc'];
            else $data['msg'] = $result['response']['result_desc'];
        }
        return $data;
    }

    public static function SignOther ($mod, $data, $payConf)
    {
        $sign = base64_decode($data['sign']);
        unset($data['sign']);
        unset($data['sign_type']);
        $signStr = self ::getSignStr($data, true, true);
        $pub_key = openssl_get_publickey($payConf['public_key']);
        $flag = (bool)openssl_verify($signStr, $sign, $pub_key, OPENSSL_ALGO_MD5);
        if ( $flag && $data['trade_status'] == 'SUCCESS' ) {
            return true;
        } else {
            return false;
        }
    }
}