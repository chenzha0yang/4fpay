<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hnapay extends ApiModel
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

        //TODO: do something
        self::$unit   = 2;
        $version      = '2.6'; //版本 version
        $serialID     = $order; //订单号
        $submitTime   = date('YmdHis'); //订单时间
        $failureTime  = ''; //date('YmdHis',strtotime("+1 year"));//订单失效时间
        $customerIP   = ''; //下单IP
        $jine         = $amount * 100;
        $orderDetails = $order . ',' . $jine . ',' . '' . ',' . 'PK' . ',' . '1'; //订单号，订单金额*10，商户名称，商品名称，商品数量
        $totalAmount  = $jine; //订单金额
        $type         = '1000'; //交易类型 1000为即时支付
        $buyerMarked  = $payConf['public_key']; //新生账户号
        $orgCode      = $bank; //银行编码
        if ($bank == "WECHATPAY") {
            $payType = 'QRCODE_B2C'; //微信支付方式
        } else {
            $payType = 'BANK_B2C'; //付款方支付方式
        }
        $currencyCode            = ''; //人民币
        $directFlag              = '1'; //是否直连
        $borrowingMarked         = ''; //资金来源借贷标示
        $couponFlag              = ''; //优惠劵标示
        $platformID              = ''; //平台商ID
        $noticeUrl               = $ServerUrl; //商户通知地址
        $partnerID               = $payConf['business_num']; //商户ID
        $remark                  = ''; //扩展字段
        $charset                 = "1"; //编码格式
        $signType                = '2'; //加密方式
        $str                     = '&';
        $signMsg                 = 'version=' . $version . $str . 'serialID=' . $serialID . $str . 'submitTime=' . $submitTime . $str . 'failureTime=' . $failureTime . $str . 'customerIP=' . $customerIP . $str . 'orderDetails=' . $orderDetails . $str . 'totalAmount=' . $totalAmount . $str . 'type=' . $type . $str . 'buyerMarked=' . $buyerMarked . $str . 'payType=' . $payType . $str . 'orgCode=' . $orgCode . $str . 'currencyCode=' . $currencyCode . $str . 'directFlag=' . $directFlag . $str . 'borrowingMarked=' . $borrowingMarked . $str . 'couponFlag=' . $couponFlag . $str . 'platformID=' . $platformID . $str . 'returnUrl=' . $returnUrl . $str . 'noticeUrl=' . $noticeUrl . $str . 'partnerID=' . $partnerID . $str . 'remark=' . $remark . $str . 'charset=' . $charset . $str . 'signType=' . $signType; //加密字符串
        $data                    = [];
        $data['version']         = $version; //版本 version
        $data['serialID']        = $order; //订单号
        $data['submitTime']      = date('YmdHis'); //订单时间
        $data['failureTime']     = ""; //date('YmdHis',strtotime("+1 year"));//订单失效时间
        $data['customerIP']      = ""; //下单IP
        $data['orderDetails']    = $orderDetails;
        $data['totalAmount']     = $totalAmount;
        $data['type']            = '1000'; //交易类型 1000为即时支付
        $data['buyerMarked']     = $buyerMarked; //新生账户号
        $data['payType']         = 'BANK_B2C'; //付款方支付方式
        $data['orgCode']         = $bank; //银行
        $data['currencyCode']    = ''; //人民币
        $data['directFlag']      = '1'; //是否直连
        $data['borrowingMarked'] = ''; //资金来源借贷标示
        $data['couponFlag']      = ''; //优惠劵标示
        $data['platformID']      = ''; //平台商ID
        $data['returnUrl']       = $returnUrl; //返回地址
        $data['noticeUrl']       = $ServerUrl; //商户通知地址
        $data['partnerID']       = $partnerID; //商户ID
        $data['remark']          = ''; //扩展字段
        $data['charset']         = "1"; //编码格式
        $data['signType']        = '2'; //加密方式
        $data['signMsg']         = self::getMd5Sign($signMsg . '&pkey=', $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request)
    {
        $arr              = $request->all();
        $arr['payAmount'] = $arr['payAmount'] / 100;
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $orderID       = $data["orderID"];
        $resultCode    = $data["resultCode"];
        $stateCode     = $data["stateCode"];
        $orderAmount   = $data["orderAmount"];
        $payAmount     = $data["payAmount"];
        $acquiringTime = $data["acquiringTime"];
        $completeTime  = $data["completeTime"];
        $orderNo       = $data["orderNo"];
        $partnerID     = $data["partnerID"];
        $remark        = $data["remark"];
        $charset       = $data["charset"];
        $signType      = $data["signType"];
        $signMsg       = $data["signMsg"];
        $src           = "orderID=" . $orderID
            . "&resultCode=" . $resultCode
            . "&stateCode=" . $stateCode
            . "&orderAmount=" . $orderAmount
            . "&payAmount=" . $payAmount
            . "&acquiringTime=" . $acquiringTime
            . "&completeTime=" . $completeTime
            . "&orderNo=" . $orderNo
            . "&partnerID=" . $partnerID
            . "&remark=" . $remark
            . "&charset=" . $charset
            . "&signType=" . $signType;
        $src           = $src . "&pkey=" . $payConf['md5_private_key'];
        if ($stateCode == 2 && $signMsg == md5($src)) {
            return true;
        }
        return false;
    }
}