<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zhifutpay extends ApiModel
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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self::$reqType = 'curl';
        self::$method  = 'header';
        self::$payWay = $payConf['pay_way'];
        self::$unit = '2';

        $data = [];
        $data['tranTp'] = '0';                          //结算类型
        $data['amount'] = $amount * 100;
        $data['subject'] = 'vivo';                      //商品标题
        $data['notifyUrl'] = $ServerUrl;
        $data['orgOrderNo'] = $order;                   //订单
        $data['body'] = 'X9S';                          //订单描述
        $data['account'] = $payConf['business_num'];    //商户号
        if ( $payConf['is_app'] == '1' ) {
            $data['source'] = $bank;                    //支付方式
            $data['orderTp'] = '1';                     //订单类型,1  wap
            $data['callbackUrl'] = $returnUrl;
        } elseif ( $payConf['pay_way'] == 1 ) {
            $data['bankType'] = $bank;                  //支付银行
            $data['callbackUrl'] = $returnUrl;
            $data['source'] = '2';                      //支付方式
            $data['orderTp'] = '4';                     //订单类型,4: 网银
        } else {
            $data['source'] = $bank;                    //支付方式
            $data['orderTp'] = '0';                     //订单类型,0:扫码
        }
        ksort($data);
        $postStr = '';
        $postStr = urldecode(http_build_query($data));
        $merchant_private_key = openssl_get_privatekey($payConf['rsa_private_key']);
        openssl_sign($postStr, $signature, $merchant_private_key);
        openssl_free_key($merchant_private_key);
        $data['signature'] = base64_encode($signature);
        $post['data'] = stripslashes(json_encode($data));
        $post['httpHeaders'] = [
            "Content-type:application/json;charset=utf-8",
            "Accept:application/json"
        ];
        $post['orgOrderNo'] = $data['orgOrderNo'];
        $post['amount'] = $data['amount'];
        unset($reqData);
        return $post;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $json = json_decode(urldecode($data), true);
        $pbkey = openssl_pkey_get_public($payConf['public_key']);
        $sign = base64_decode($json['signature']);
        unset($json['signature']);
        ksort($json);
        $signStr = urldecode(http_build_query($json));
        $flag = openssl_verify($signStr, $sign, $pbkey, OPENSSL_ALGO_SHA1);
        if ( $flag && $json['paySt'] == '2' ) {
            return true;
        } else {
            return false;
        }
    }
}