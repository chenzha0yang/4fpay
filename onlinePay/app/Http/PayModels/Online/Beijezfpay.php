<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Beijezfpay extends ApiModel
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
        self::$httpBuildQuery = true;

        $amount = $amount*100;
        $data['inputCharset'] = '1';
        $data['partnerId'] = $payConf['business_num'];//商户号
        $data['notifyUrl'] = $ServerUrl;
        $data['returnUrl'] = $ServerUrl;
        $data['orderNo'] = $order;//订单号
        $data['orderAmount'] = (string)$amount;//订单金额
        $data['orderCurrency'] = '156';
        $data['orderDatetime'] = date('YmdHis',time());
        $data['payMode'] = $bank;//银行编码
        $data['subject'] = 'test';
        $data['body'] = 'test';
        if((int)$payConf['pay_way'] === 1){
            $data['cardNo'] = '';
            $data['bnkCd'] = $bank;
            $data['accTyp'] = 1;
            $data['payMode'] = '3';
        }
        $data['ip'] = self::getIp();
        ksort($data);
        $signStr = "";
        foreach ($data as $k => $v) {
            if ($k != "SIGN_DAT" && $v != "" && !is_array($v)) {
                $signStr .= $k . "=" . $v . "&";
            }
        }

        $signStr = trim($signStr, "&");
        $ret = false;
        if (openssl_sign($signStr, $ret, $payConf['rsa_private_key'],OPENSSL_ALGO_SHA1)){
            $ret = base64_encode(''.$ret);
        }

        $data['signMsg'] = $ret;
        $data['signType'] = '1';

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['errCode'] == '0000') {
            if(isset($data['retHtml'])){
                echo $data['retHtml'];exit();
            }
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['orderAmount'])) {
            $arr['orderAmount'] = $arr['orderAmount'] / 100;
        }
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        if (isset($data['payResult']) && $data['payResult'] == 1) {
            $data = $_REQUEST;
        } else {
            $post = file_get_contents('php://input');
            $data = json_decode($post,true);
        }
        $sign = $data['signMsg'];
        unset($data['signMsg']);
        unset($data['signType']);
        ksort($data);
        $signStr = "";
        foreach ($data as $k => $v) {
            if ($k != "SIGN_DAT" && $v != "" && !is_array($v)) {
                $signStr .= $k . "=" . $v . "&";
            }
        }
        $signStr = trim($signStr, "&");
        $pu = openssl_get_publickey($payConf['public_key']);
        $result = (bool)openssl_verify($signStr, base64_decode($sign), $pu,OPENSSL_ALGO_SHA1);
        openssl_free_key($pu);
        if ($result  && $data['payResult'] == '1') {
            return true;
        }
        return false;
    }


}