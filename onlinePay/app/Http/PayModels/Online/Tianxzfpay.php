<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;
use App\Extensions\File;


class Tianxzfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $dataJson = '';

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
        $order          = $reqData['order'];
        $amount         = $reqData['amount'];
        $bank           = $reqData['bank'];
        $ServerUrl      = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl      = $reqData['returnUrl']; // 同步通知地址
        //TODO: do something
        if ($payConf['pay_way'] != 9) {
            self::$reqType        = 'curl';
            self::$payWay         = $payConf['pay_way'];
            self::$resType        = 'other';
            self::$httpBuildQuery = true;
            //self::$method = 'get';
        }

        $params = [
            'out_trade_no' => $order,
            'pay_type' => $bank,
            'amount' => $amount,
            'time' => time(),
            'notify_url' => $ServerUrl
        ];

        ksort($params);
        $encode = json_encode($params);

        openssl_sign($encode, $sign, $payConf['rsa_private_key'], OPENSSL_ALGO_SHA256);
        $sign = self::url_safe_base64_encode($sign);

        $encrypted = '';
        $pub_id = openssl_get_publickey($payConf['public_key']);
        $key_len = openssl_pkey_get_details($pub_id)['bits'];
        $part_len = $key_len / 8 - 11;
        $parts = str_split($encode, $part_len);

        foreach ($parts as $part) {
            $encrypted_temp = '';
            openssl_private_encrypt($part, $encrypted_temp, $payConf['rsa_private_key']);
            $encrypted .= $encrypted_temp;
        }

        $encrypted =  self::url_safe_base64_encode($encrypted);

        $data = [
            'mch_id' => $payConf['business_num'],
            'sign' => $sign,
            'params' => $encrypted
        ];
        $data['out_trade_no'] = $params['out_trade_no'];
        $data['amount'] = $params['amount'];
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == "0") {
            $data['qrCode'] = $data['data']['qrcode'];
        }
        return $data;
    }

    //回调特殊处理  转换req值
    public static function getVerifyResult($request,$mod)
    {
        $arr = $request->all();
        if (isset($arr['params'])) {
            $params = $arr['params'];
        } else {
            $params = array();
        }
        $bankOrder = PayOrder::getOrderData($arr['out_trade_no']);//根据订单号 获取入款注单数据
        if (isset($bankOrder->merchant_id)) {
            $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        }else{
            //查询不到订单号时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
            File::logResult($request->all());
            return trans("success.{$mod}");
        }
        $pub_id = openssl_get_publickey($payConf['public_key']);
        $key_len = openssl_pkey_get_details($pub_id)['bits'];

        $decrypted = "";
        $part_len = $key_len / 8;
        $base64_decoded = self::url_safe_base64_decode($params);
        $parts = str_split($base64_decoded, $part_len);

        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_public_decrypt($part, $decrypted_temp, $payConf['public_key']);
            $decrypted .= $decrypted_temp;
        }

        $data = json_decode($decrypted,true);
        $data['params'] = $arr['params'];
        $data['out_trade_no'] = $arr['out_trade_no'];
        $data['sign'] = $arr['sign'];
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $params = $data['params'];
        $sign = $data['sign'];

        $pub_id = openssl_get_publickey($payConf['public_key']);
        $key_len = openssl_pkey_get_details($pub_id)['bits'];

        $decrypted = "";
        $part_len = $key_len / 8;
        $base64_decoded = self::url_safe_base64_decode($params);
        $parts = str_split($base64_decoded, $part_len);

        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_public_decrypt($part, $decrypted_temp, $payConf['public_key']);
            $decrypted .= $decrypted_temp;
        }

        $res = openssl_verify($decrypted, self::url_safe_base64_decode($sign), $pub_id, OPENSSL_ALGO_SHA256);
        if ($res) {
            return true;
        }
        return false;
    }

    public static function url_safe_base64_decode($data) {
        $base_64 = str_replace(array(
            '-',
            '_'
        ), array(
            '+',
            '/'
        ), $data);
        return base64_decode($base_64);
    }

    public static function url_safe_base64_encode($data) {
        return str_replace(array(
            '+',
            '/',
            '='
        ), array(
            '-',
            '_',
            ''
        ), base64_encode($data));
    }
}