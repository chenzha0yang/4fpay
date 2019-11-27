<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;
use App\Http\Models\CallbackMsg;

class Xinzhonpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signData = [];
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
        self::$unit = 2; // 单位 ： 分

        $data                        = [];
        $post                        = [];
        $data['loginId']             = $payConf['business_num'];
        $data['merOrdid']            = $order;
        $data['orderName']           = 'xz';
        $data['tradeMoney']          = (string)($amount * 100);
        $data['asynNotificationUrl'] = $ServerUrl;
        $data['syncNotificationUrl'] = $returnUrl;
        $data['paymentName']         = $bank;
        $data['sence']               = '1001';
        if ($payConf['is_app'] == 1) {
            $data['sence'] = '1002';
        }
        $json   = json_encode($data, JSON_UNESCAPED_UNICODE);
        $crypto = '';
        foreach (str_split($json, 64) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, openssl_pkey_get_public($payConf['public_key']));//公钥加密
            $crypto .= $encryptData;
        }
        openssl_sign($crypto, $sign_info, openssl_pkey_get_private($payConf['rsa_private_key']));
        $post['loginId']  = $data['loginId'];
        $post['data']     = base64_encode($crypto);
        $post['signType'] = 'RSA';
        $post['sign']     = base64_encode($sign_info);

        unset($reqData);
        return $post;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr       = $request->all();
        $order     = $arr['merOrdid'];  //订单号
        $bankOrder = PayOrder::getOrderData($order);//根据订单号 获取入款注单数据
        if (isset($bankOrder->merchant_id)) {
            $payConf = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        } else {
            echo trans("success.{$mod}");
            CallbackMsg::AddCallbackMsg($request, $bankOrder, 1);
            exit();
        }
        $pi = openssl_pkey_get_private($payConf['rsa_private_key']);

        $sign     = base64_decode($arr['sign']);
        $encParam = base64_decode($arr['data']);
        $crypto = '';
        foreach (str_split($encParam, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $pi);
            $crypto .= $decryptData;
        }
        $datas = json_decode($crypto, true);

        $return               = [];
        $return['tradeMoney'] = $datas['tradeMoney'] / 100;
        $return['merOrdid']   = $order;
        self::$signData['encParam']   = $encParam;
        self::$signData['sign'] = $sign;


        return $return;

    }

    public static function signOther($mod, $data, $payConf)
    {
        $data = self::$signData;
        $pu = openssl_pkey_get_public($payConf['public_key']);
        return (bool)openssl_verify($data['encParam'], self::$signData['sign'], $pu);
    }

}