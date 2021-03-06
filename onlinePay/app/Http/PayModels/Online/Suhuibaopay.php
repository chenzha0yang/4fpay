<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Suhuibaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str/other

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

        //TODO: do something

        $payType      = "";
        $bankCode     = "";
        $redoFlag     = "";
        $extendParam  = "";
        if($payConf['pay_way'] == '1'){
            $payType      = 'b2c';
            $serviceType  ="direct_pay";
            $interfaceVersion ="V3.0";
            $redoFlag     = "1";
            $bankCode     = $bank;
            $extendParam  = "customer_name^ChongZhi|customer_idNumber^130206197610066454";
        }else{
            $serviceType = $bank;
            $interfaceVersion ="V3.1";
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
            self::$method = 'get';
            self::$resType = 'other';
        }

        $data = array();
        $data["merchant_code"]      = $payConf['business_num'];
        $data["bank_code"]          = $bankCode;
        $data["order_no"]           = $order;
        $data["order_amount"]       = $amount;
        $data["service_type"]       = $serviceType;
        $data["input_charset"]      = "UTF-8";
        $data["notify_url"]         = $ServerUrl;
        $data["interface_version"]  = $interfaceVersion;
        $data["order_time"]         = date( 'Y-m-d H:i:s' );
        $data["product_name"]       = "product";
        $data["client_ip"]          = "127.0.0.1";
        $data["extend_param"]       = $extendParam;
        $data["extra_return_param"] = "";
        $data["pay_type"]           = $payType;
        $data["product_code"]       = "";
        $data["product_desc"]       = "ChongZhi";
        $data["product_num"]        = "1";
        $data["return_url"]         = $returnUrl;
        $data["show_url"]           = "";
        $data["redo_flag"]          = $redoFlag;

        $signStr = self::getSignStr($data, true, true);
        $sign    = self::getRsaSign("{$signStr}", $payConf['rsa_private_key'], OPENSSL_ALGO_MD5);//   获取sign值（RSA-S加密）
        $data['sign_type'] = 'RSA-S';//签名方式
        $data['sign'] = $sign;//签名
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
}