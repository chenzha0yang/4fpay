<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;

class Lelifpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData;

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
            $data['clientIp'] = self::getIp();
            $data['sceneBizType'] = 'WAP';
        }
        self::$unit = 2;
        //TODO: do something
        $data['currencyCode'] = '156';
        $data['macKeyId'] = $payConf['business_num'];//密钥识别
        $data['merId'] = $payConf['business_num'];//商户号
        $data['notifyUrl'] = $ServerUrl;
        $data['orderDate'] = date('Ymd',time());
        $data['orderId'] = $order;//订单号
        $data['orderTime'] = date('His',time());
        $data['pageReturnUrl'] = $returnUrl;
        $data['productDesc'] = 'test';
        $data['productTitle'] = 'test';
        $data['secpMode'] = 'perm';
        $data['secpVer'] = 'icp3-1.1';
        $data['timeStamp'] = date('YmdHis',time());
        $data['txnAmt'] = $amount*100;//订单金额
        if((int)$payConf['pay_way'] === 1){
            $data['txnSubType'] = 21;
        }else{
            $data['txnSubType'] = $bank;//银行编码
            self::$resType  = 'other';
            self::$reqType  = 'curl';
            self::$payWay   = $payConf['pay_way'];
            self::$httpBuildQuery = true;
        }
        $data['txnType'] = '01';

        $signStr =  self::getSignStr($data,true,true);
        $data['mac'] = md5($signStr . "&k=" . $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $arr               = json_decode($response, true);
        return $arr;

    }

    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $data['txnAmt'] = $data['txnAmt']/100;
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['mac'];
        unset($data['mac']);
        $signStr =  self::getSignStr($data,false,true);
        $signTrue = md5($signStr . "&k=" . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data["respCode"] == '0000') {
            return true;
        } else {
            return false;
        }
    }
}