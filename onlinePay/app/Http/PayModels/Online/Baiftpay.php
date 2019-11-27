<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Baiftpay extends ApiModel
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
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        $data = [];
        $data['merchantId'] = $payConf['business_num'];         //商户号
	    $data['notifyUrl']  = $ServerUrl;                       // 外网能访问
	    $data['outOrderId'] = $order;                           //交易号
	    $data['subject']    = 'zhif';                           //订单名称
	    $data['body']       = 'zhif';                           // 订单描述
	    $data['transAmt']   = $amount;                          //交易金额
	    $data['scanType']   = $bank;                            //扫码类型
        $signStr = self::getSignStr($data, false , true);
        $data['sign'] = self::getRsaSign("{$signStr}", $payConf['rsa_private_key']);
        unset($reqData);
        return $data;
    }

    /**
     * @param $type
     * @param $sign
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $sign, $payConf)
    {
        $MySign = self::getReSignature($sign, $payConf['public_key']);
        if ($MySign) {
            if ($sign['respCode'] == '00') {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * 回调参数签名
     *
     * @param      <type>  $PaRm    参数
     * @param      <type>  $MeRcHo  秘钥
     *
     * @return     <type>  The re signature.
     */
    public static function getReSignature($PaRm, $MeRcHo)
    {
        $data = array();
        foreach ($PaRm as $k => $v) {
            if ($k == 'sign' || $k == 'signType' || $v == '') {
                continue;
            }
            $data[$k] = $v;
        }
        $sign = self::getSignStr($data, false , true);
        return self::verifyRSA($sign, $PaRm['sign'], $MeRcHo);
    }
}