<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Debaopay extends ApiModel
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

        //TODO: do something

        if($payConf['pay_way'] != '1'){
            if(isset($payConf['is_app']) && $payConf['is_app'] == 1){
                self::$isAPP = true;
            }
            self::$reqType = 'curl';
            self::$resType = 'other';
            self::$httpBuildQuery = true;
            self::$payWay = $payConf['pay_way'];
        }
        $data = [];
        $data['merchant_code'] = $payConf['business_num'];
        $data['order_no'] = $order;                             //商户网站唯一订单号
        $data['notify_url'] = $ServerUrl;                       //服务器异步通知地址
        $data['order_time'] = date('Y-m-d H:i:s' );     //时间
        $data['order_amount'] = $amount;                        //金额
        $data['product_name'] = 'zhif';                         //商品名称
        $data['product_code'] = "";                             //商品编号
        $data['product_num'] = "";                              //商品数量
        $data['extend_param'] = "";
        $data['extra_return_param'] = "";
        $data['product_desc'] = "";                             //商品描述
        if($payConf['pay_way'] == '1'){
            $data['service_type'] ="direct_pay";                //接口类型
            $data['interface_version'] ="V3.0";
            $data['client_ip'] ="" ;
            $data['input_charset'] = "UTF-8";                   //编码格式
            $data['bank_code'] = $bank;                         //支付类型
            $data['return_url'] ="";
            $data['pay_type'] = "b2c";
            $data['redo_flag'] = 1;
            $data['show_url'] = "";
        } else {
            $data['service_type'] = $bank;
            $data['interface_version'] ='V3.1';                 //接口版本
            $data['client_ip'] = '127.0.0.1';
        }
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = self::getRsaSign($signStr, $payConf['rsa_private_key'], OPENSSL_ALGO_MD5);
        $data['sign_type'] = 'RSA-S';
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
            if ($results['result_code'] == '0'){
                if(isset($results['qrcode']) && !empty($results['qrcode'])){
                    $results['qrcode'] = urldecode($results['qrcode']);
                }
                if(isset($results['payURL']) && !empty($results['payURL'])){
                    $results['payURL'] = urldecode($results['payURL']);
                }
            } else {
                $results['resp_code'] = $results['result_code'];
                $results['resp_desc'] = $results['result_desc'];
            }
        }
        return $results;
    }

    /**
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        $result = trans("backField.{$mod}");
        $dinPaySign = base64_decode($data["sign"]);
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $true = self::verifyRSA($signStr, $dinPaySign, $payConf['public_key']);
        if ($true) {
            if ($data[$result['orderStatus'][0]]  == $result['orderStatus'][1]) {
                return true;
            }
        }
        return false;
    }
}