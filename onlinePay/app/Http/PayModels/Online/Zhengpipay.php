<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zhengpipay extends ApiModel
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
     * @param array       $reqData 接口传递的参数
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

        //TODO: do something
        $data       = array();
        $data['zp_mer_id']  = $payConf['business_num']; //商户编号
        $data['zp_order_amount'] = $amount; //订单金额
        $data['zp_order_id']     = $order; //订单编号
        $data['zp_notify_url']   = $ServerUrl; //异步通知地址

        $signature = '';
        $key_private = openssl_get_privatekey($payConf['rsa_private_key']);
        if (!$key_private) {
            echo '商户私钥错误!请检查商户配置';exit();
        }
        ksort($data);
        $datas = urldecode(http_build_query($data));
        openssl_sign($datas, $signature, $key_private, OPENSSL_ALGO_SHA1);
        openssl_free_key($key_private);
        $data['zp_back_url'] = $returnUrl; //同步通知地址
        $data['zp_pay_type']     = $bank; //订单编号
        $data['zp_sign'] =  base64_encode($signature);
        $data['zp_sign_type'] = 'RSA';
        unset($reqData);
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['zp_sign'];
        unset($data['zp_sign']);
        unset($data['zp_desc']);
        unset($data['zp_attch']);
        unset($data['zp_datetime']);
        unset($data['zp_timezone']);
        $key_public = openssl_get_publickey($payConf['public_key']);
        if (!$key_public) {
            echo '商户公钥错误!请检查商户配置';exit();
        }
        ksort($data);
        $data = urldecode(http_build_query($data));
        $result = openssl_verify($data, base64_decode($sign), $key_public);
        openssl_free_key($key_public);

        if ($result && $data['zp_status_code'] == '200') {
            return true;
        }
        return false;
    }

}