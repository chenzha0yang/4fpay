<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yixpay extends ApiModel
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
        if($payConf['pay_way'] != 1 || $payConf['pay_way'] != 9)
        {
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$resType = 'other';
        }

        $data['payKey'] = $payConf['business_num'];//商户号
        if($payConf['pay_way'] == 1){
            $data['productType'] = '50000103';
            $data['bankAccountType'] = 'PRIVATE_DEBIT_ACCOUNT';
            $data['bankCode'] = $bank;
        }else{
            $data['productType'] = $bank;//银行编码
        }
        $data['outTradeNo'] = $order;//订单号
        $data['orderPrice'] = sprintf('%.2f',$amount);//订单金额
        $data['orderTime'] = date('YmdHis',time());
        $data['productName'] = 'test';
        $data['orderIp'] = self::getIp();
        $data['returnUrl'] = $returnUrl;
        $data['notifyUrl'] = $ServerUrl;
        $data['remark'] = 'test';
        $signStr =  self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr . "&paySecret=" . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['resultCode'] == '0000') {
            if(isset($data['payMessageType'])){
                echo $data['payMessage'];die;
            }
            $data['qrCode'] = $data['payMessage'];
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtoupper(md5($signStr . "&paySecret=" . $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue) && $data['tradeStatus'] == 'SUCCESS') {
            return true;
        }
        return false;
    }


}