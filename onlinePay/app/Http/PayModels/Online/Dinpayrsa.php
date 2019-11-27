<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Dinpayrsa extends ApiModel
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
     * @param array $reqData       接口传递的参数
     * @param array $payConf
     * @return array
     * @internal param null|string $user
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

        if($payConf['pay_way'] == 1){
            $bank = '40000501';
        } else {
            self::$reqType = 'curl';
            self::$resType = 'other';
            self::$payWay = $payConf['pay_way'];
            self::$httpBuildQuery = true;
        }
        if($bank == "card"){
            $bank = "";
        }
        $data=array();

        $data['order_no']      = $order;
        $data['order_amount']  = $amount;
        $data['notify_url']    = $ServerUrl;
        $data['order_time']    = date('Y-m-d H:i:s');
        $data['merchant_code'] = $payConf['business_num'];
        $data['product_code']  = "";
        $data['product_desc']  = "";
        $data['product_num']   = "";
        $data['extend_param']  = "";
        $data['extra_return_param'] = "";

        if ($payConf['pay_way'] == 1) {
            $data['bank_code'] = $bank;
            $data['return_url'] = $returnUrl;
            $data['service_type'] = "direct_pay";
            $data['input_charset'] = "UTF-8";
            $data['interface_version'] = "V3.0";
            $data['product_name'] = "pk";
            $data['client_ip'] = "";
            $data['pay_type'] = "";
            $data['show_url'] = "";
            $data['redo_flag'] = "";
        } else {
            $data['service_type'] = $bank;
            $data['interface_version'] = "V3.1";
            $data['product_name'] = "product_name";
            $data['client_ip'] = "127.0.0.1";
        }

        $signStr = self::getSignStr($data, true, true);
        $sign = self::getRsaSign($signStr, $payConf['rsa_private_key'], OPENSSL_ALGO_MD5);

        $data['sign_type'] = "RSA-S";
        $data['sign'] = $sign;
        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return array|bool
     */
    public static function getQrCode($response)
    {
        $res = (array) simplexml_load_string($response);
        if (!isset($res['response'])) {
            return false;
        }

        $results = (array) $res['response'];
        if ($results['resp_code'] == 'SUCCESS') {
            if( $results['result_code'] == '0'){
                $results['qrcode'] = urldecode($results['qrcode']);
                if(isset($results['isRedirect']) && $results['isRedirect'] == "Y"){
                    self::$isAPP = true;
                }
            } else {
                $results['resp_code'] = $results['result_code'];
                $results['resp_desc'] = $results['result_desc'];
            }
        }
        return $results;
    }

}