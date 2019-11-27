<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;
use App\Http\Models\CallbackMsg;

class Waterstarpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        //self::$httpBuildQuery = true;

        $payInfo = explode('&', $payConf['business_num']);
        $amount                  = $amount * 100;
        $data['version']         = 'V4.0.0.0';
        $data['merNo']           = $payInfo[0]; //商户号
        $data['subMerNo']        = $payInfo[0];
        $data['netway']          = $bank; //银行编码
        $data['random']          = (string) rand(1000, 9999);
        $data['orderNum']        = $order; //订单号
        $data['amount']          = (string) $amount; //订单金额
        $data['goodsName']       = 'goods';
        $data['callBackUrl']     = $ServerUrl;
        $data['callBackViewUrl'] = $returnUrl;
        $data['charset']         = 'UTF-8';
        ksort($data);
        $sign                = strtoupper(md5(json_encode($data, 320) . $payInfo[1]));
        $data['sign']        = $sign;
        $json                = json_encode($data, 320);
        $dataStr             = self::encode_pay($json, $payConf['public_key']);
        // $param['data'] = urlencode($dataStr);
        // $param['merchNo'] = $payInfo[0];
        // $param['version'] = $data['version'];
        $param               = 'data=' . urlencode($dataStr) . '&merchNo=' . $payInfo[0] . '&version=V4.0.0.0';
        //$param['orderNum'] = $data['orderNum'];
        //$param['amount'] = $data['amount'];
        unset($reqData);
        return $param;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['stateCode'] == '00') {
            $data['qrCode'] = $data['qrcodeUrl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if(isset($arr['orderNum'])){
            $bankOrder = PayOrder::getOrderData($arr['orderNum']);//根据订单号 获取入款注单数据
            if (isset($bankOrder->merchant_id)) {
            $payConf = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
            } else {
                echo trans("success.{$mod}");
                CallbackMsg::AddCallbackMsg($request, $bankOrder, 1);
                exit();
            }
            $payInfo = explode('&', $payConf['business_num']);
            $data = self::decode($data['data'], $payConf['rsa_private_key']);
            $json = json_decode($data, true);
            $arr['amount'] =$json['amount']/100;
        }else{
            $arr['orderNum'] = '';
            $arr['amount'] = '';
        }

        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $payInfo = explode('&', $payConf['business_num']);
        $data = self::decode($data['data'], $payConf['rsa_private_key']);
        $json = json_decode($data, true);
        $res = self::callback_to_array($data, $payInfo[1]);
        if ($res && $json['payResult'] == '00') {
            return true;
        }
        return false;
    }

    public static function encode_pay($data, $pubicKey)
    {
        $pu_key = openssl_pkey_get_public($pubicKey);
        if ($pu_key == false) {
            echo "打开密钥出错";
            die;
        }
        $encryptData = '';
        $crypto      = '';
        foreach (str_split($data, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $pu_key);
            $crypto = $crypto . $encryptData;
        }
        $crypto = base64_encode($crypto);
        return $crypto;

    }

    public static function decode($data, $privateKey)
    {
        $pr_key = openssl_get_privatekey($privateKey);
        if ($pr_key == false) {
            echo "打开密钥出错";
            die;
        }
        $data   = base64_decode($data);
        $crypto = '';
        foreach (str_split($data, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $pr_key);
            $crypto .= $decryptData;
        }
        return $crypto;
    }

    public static function callback_to_array($json, $key)
    {
        $array       = json_decode($json,true);
        $sign_string = $array['sign'];
        ksort($array);
        $sign_array = array();
        foreach ($array as $k => $v) {
            if ($k !== 'sign') {
                $sign_array[$k] = $v;
            }
        }
        $md5 = strtoupper(md5(json_encode($sign_array, 320) . $key));
        if ($md5 == $sign_string) {
            return true;
        } else {
            return false;
        }

    }
}