<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Chuangszfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$unit    = 2;
        self::$httpBuildQuery = true;


        $data['version'] = '1.0.0';
        $data['transType'] = 'SALES';
        $data['merNo'] = $payConf['business_num'];//商户号
        $data['productId'] = $bank;//银行编码
        $data['orderNo'] = $order;//订单号
        $data['transAmt'] = $amount*100;//订单金额
        $data['orderDate'] = date('Ymd',time());
        $data['commodityName'] = 'test';
        $data['commodityDetail'] = 'test';
        $data['custId'] = time();
        $data['notifyUrl'] = $ServerUrl;
        $data['returnUrl'] = $returnUrl;
        $signStr =  self::getSignStr($data, true, true);
        //获取私钥
        $pi = openssl_pkey_get_private($payConf['md5_private_key']);
        openssl_sign($signStr, $sign, $pi, OPENSSL_ALGO_SHA1);
        $data['signature'] = base64_encode($sign);

        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $res = json_decode($response,true);
        if($res['respCode']=='P000'){
            if(self::$isAPP){
                $res['payUrl'] = $res['payInfo'];
            }else{
                $res['payUrl'] = $res['payQRCodeUrl'];
            }
        }
        return $res;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->all();
        $arr['transAmt'] = $arr['transAmt'] / 100;
        return $arr;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['signature'];
        unset($data['signature']);
        $signStr =  self::getSignStr($data, true, true);
        $pu = openssl_get_publickey($payConf['public_key']);
        //openssl_public_encrypt($signStr,$panText,$pu,OPENSSL_PKCS1_PADDING);
        $res = openssl_verify($signStr, base64_decode($sign), $pu, OPENSSL_PKCS1_PADDING);
        if ($res && $data['respCode'] == '0000') {
            return true;
        } else {
            return true;
        }
    }
}