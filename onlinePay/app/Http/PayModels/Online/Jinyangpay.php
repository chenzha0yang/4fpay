<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jinyangpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $imgSrc = false; // 返回数据为图片地址

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        if ($payConf['pay_way'] != '1' && $payConf['pay_way'] != '9' && $bank != 'WEIXINBARCODE' && $bank != 'UNIONWAPPAY' && $bank != 'ALIPAYWAP' && $bank != 'UNIONFASTPAY' && $bank != 'WEIXINWAP') {                    //扫码curl提交
            self::$reqType        = 'curl';
            self::$payWay         = $payConf['pay_way'];
            self::$httpBuildQuery = true;
            self::$resType        = 'other';
            self::$imgSrc = true;
        }

        if (isset($reqData['isApp']) && $reqData['isApp'] == '1') {                     //开通app
            self::$isAPP = true;
        }

        $data                   = [];
        $data['p1_mchtid']      = $payConf['business_num'];      // 商户ID
        $data['p2_paytype']     = $bank;                        //支付方式
        $data['p3_paymoney']    = sprintf('%.2f',$amount);                     //支付金额
        $data['p4_orderno']     = $order;                       //商户平台唯一订单号
        $data['p5_callbackurl'] = $ServerUrl;               //商户异步回调通知地址
        $data['p6_notifyurl']   = $returnUrl;                 //商户同步通知地址
        $data['p7_version']     = 'v2.8';                       //版本号
        $data['p8_signtype']    = '1';                         //签名加密方式
        $data['p9_attach']      = 'abc';                         //备注
        $data['p10_appname']    = 'asd';                       //分成标识
        $data['p11_isshow']     = '1';                          //是否显示收银台
        $data['p12_orderip']    = '127.0.0.1';                 //签名加密方式
        if ($data['p2_paytype'] == "FASTPAY" || $data['p2_paytype'] == "UNIONFASTPAY") {
            //快捷支付
            $data['p13_memberid'] = rand() . time(); //商户唯一识别表示
        }
        $signStr      = self::getSignStr($data, false);
        $data['sign'] = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);//dd($data);
        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $rspJsonData = json_decode($response, true);
        if ($rspJsonData['rspCode'] == "1") {
            $rspJsonData['r6_qrcode'] = $rspJsonData['data']['r6_qrcode'];
        }
        return $rspJsonData;
    }
}