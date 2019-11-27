<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayMerchant;

class Anyipay extends ApiModel
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
     * @param array       $reqData 接口传递的参数
     * @param PayMerchant $payConf object PayMerchant类型的对象
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
//        self::$reqType = 'curl';
//        self::$payWay  = $payConf['pay_way'];


        $data['head'] = [
            "serviceName" => "fund_gpay_payment",
            "traceNo"     => $order,
            "version"     => "3.0",
            "charset"     => "utf-8",
            "senderId"    => $payConf['business_num'],
            "sendTime"    => date('YmdHi')
        ];
        $data['body'] = [
            'transTime'     => date('YmdHis'),
            'transAmount'   => (string)($amount * 100),
            'transCurrency' => 'CNY',
            'transType'     => '1',
            'orderNo'       => $order,
            'orderDesc'     => $order,
            'productName'   => 'goods',
            'payerAccType'  => 'DEBIT',
            'notifyUrl'     => $ServerUrl,
            'pageReturnUrl' => $returnUrl,
        ];
        if ($payConf['pay_way'] == '1') {
            $data['body']['payMode']     = 'Bank';
            $data['body']['payerInstId'] = $bank;
        } else {
            $data['body']['payMode']     = $bank;
            $data['body']['payerInstId'] = '';
        }
        ksort($data['head']);
        ksort($data['body']);
        $data     = json_encode($data);
        $data     = urlencode($data);
        $pub_key  = openssl_pkey_get_public($payConf['public_key']);
        $ret      = '';
        $strArray = str_split($data, 117);
        foreach ($strArray as $cip) {
            if (openssl_public_encrypt($cip, $result, $pub_key, OPENSSL_PKCS1_PADDING)) {
                $ret .= $result;
            }
        }
        $ret                      = base64_encode($ret);
        $data                     = preg_replace('/%5C/', '', $data);
        $md5msg                   = md5($data . $payConf['md5_private_key']);
        $postData['partner_id']   = $payConf['business_num'];
        $postData['service_name'] = "fund_gpay_payment";
        $postData['rsamsg']       = $ret;
        $postData['md5msg']       = $md5msg;
        $postData['version']      = '3.0';


        unset($reqData);
        return $postData;
    }

    public static function getVerifyResult($request, $mod)
    {
        $json              = $request->all();
        self::$signData    = $json;
        $body              = json_decode($json['body'], true);
        $head              = json_decode($json['head'], true);
        $arr['payAmount']  = sprintf('%2f', $body['payAmount'] / 100);
        $arr['outTradeNo'] = $head['traceNo'];
        return $arr;
    }

    public static function signOther($model, $data, $payConf)
    {

        $data            = self::$signData;
        $signArr['head'] = json_decode($data['head'], true);
        $signArr['body'] = json_decode($data['body'], true);
        ksort($signArr['head']);
        ksort($signArr['body']);
        $signArr  = json_encode($signArr);
        $signArr  = urlencode($signArr);
        $signArr  = preg_replace('/%5C/', '', $signArr);
        $signTrue = md5($signArr . $payConf['md5_private_key']);

        if ($signTrue == $data['md5msg'] && $data['code'] == "0000") {
            return true;
        }
        return false;
    }


}