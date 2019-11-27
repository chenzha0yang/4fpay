<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Tiantzfvspay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $pc_wap = '';

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

        //TODO: do something
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType        = 'other';
        self::$pc_wap         = $payConf['is_app'];

        $data                  = [];
        $data['merchant_code'] = $payConf['business_num']; //商户号
        $data['service_type']  = $bank; //扫码支付类型
        if ($payConf['pay_way'] == '1') {
            $data['service_type'] = 'direct_pay'; //网银b2c
            $data['bank_code']    = $bank;
        }
        $data['notify_url']        = $ServerUrl; //服务器异步通知地址
        $data['interface_version'] = 'V3.1'; //接口版本
        $data['client_ip']         = '112.97.53.122'; //ip
        $data['input_charset']     = 'UTF-8';
        $data['order_no']          = $order; //商户网站唯一订单号
        $data['order_time']        = date('Y-m-d H:i:s'); //商户订单时间
        $data['order_amount']      = $amount; //商户订单总金额
        $data['product_name']      = 'vivo'; //商品名称
        $signStr                   = self::getSignStr($data, false, true);
        $data['sign_type']         = 'RSA-S';
        $merchant_private_key      = openssl_get_privatekey($payConf['rsa_private_key']);
        openssl_sign($signStr, $sign_info, $merchant_private_key, OPENSSL_ALGO_MD5);
        $data['sign'] = base64_encode($sign_info);
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $res = (array)simplexml_load_string($response);
        if (!isset($res['response'])) {
            return false;
        }
        $result = (array)$res['response'];
        if ($result['resp_code'] == 'SUCCESS' ) {
            if((int)$result['result_code'] === 0){
                if (self::$pc_wap == '1') {
                    $result['payUrl'] = urldecode($result['payurl']);
                } else {
                    $result['payUrl'] = urldecode($result['qrcode']);
                }
            } else {
                $result['code'] = $result['error_code'];
                $result['msg'] = $result['result_desc'];
            }
        } else {
            $result['code'] = $result['resp_code'];
            $result['msg']  = $result['resp_desc'];
        }
        return $result;
    }


    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $post['merchant_code']     = $data["merchant_code"];
        $post['interface_version'] = $data["interface_version"];
        $post['notify_type']       = $data["notify_type"];
        $post['notify_id']         = $data["notify_id"];
        $post['order_no']          = $data["order_no"]; //订单号
        $post['order_time']        = $data["order_time"];
        $post['order_amount']      = $data["order_amount"]; //金额
        $post['trade_status']      = $data["trade_status"];
        $post['trade_time']        = $data["trade_time"];
        $post['trade_no']          = $data["trade_no"];
        echo "SUCCESS";
        $signStr           = self::getSignStr($post, false, true);
        $dinpay_public_key = openssl_get_publickey($payConf['public_key']);
        $flag              = openssl_verify($signStr, base64_decode($data["sign"]), $dinpay_public_key, OPENSSL_ALGO_MD5);
        if ($flag && $data['trade_status'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
}