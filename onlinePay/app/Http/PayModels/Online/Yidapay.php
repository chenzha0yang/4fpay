<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yidapay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        self::$isAPP = true;

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data = array(
            'inputCharset'     => '1',                      //固定填1；1代表UTF-8
            'partnerId'        => $payConf['business_num'], //商户号
            'notifyUrl'        => $ServerUrl,               //异步通知地址
            'returnUrl'        => $returnUrl,               //同步通知地址
            'orderNo'          => $order,                   //订单号
            'orderAmount'      => $amount * 100,            //支付金额
            'orderCurrency'    => '156',                    //固定填156;人民币
            'orderDatetime'    => date('YmdHis'),    //日期
            'payMode'          => $bank,                    //支付方式
            'subject'          => 'dulex',                  //交易名称
            'body'             => 'miaosu',                 //订单描述
            'extraCommonParam' => 'any',                    //自定义参数
            'ip'               => self::getIp(),      //客户的真实ip
        );
        if ($payConf['pay_way'] == 1) {
            $data['cardNo'] = $payConf['message1'];//支付卡号
            $data['bnkCd']  = $bank;//银行编码
            $data['accTyp'] = 0;//卡类型 ，网银需要 0-借记 1-贷记
        }
        $signStr          = self::getSignStr($data, true, true);
        $data['signMsg']  = self::getRsaSign($signStr, $payConf['rsa_private_key']);
        $data['signType'] = '1'; //1代表RSA

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response,true);
        if(isset($data['qrCode']) && !empty($data['qrCode']) && $data['errCode'] = '0000'){
            $data['qrPath'] = $data['qrCode'];
        }
        if(isset($data['retHtml'])&& !empty($data['retHtml']) &&$data['errCode'] = '0000'){
            $data['qrPath'] = $data['retHtml'];
        }
        return $data;
    }
    //回调金额化分为元
    public static function getVerifyResult($request)
    {
        $result                = $request->all();
        $result['orderAmount'] = $result['orderAmount'] / 100;
        return $result;
    }
}