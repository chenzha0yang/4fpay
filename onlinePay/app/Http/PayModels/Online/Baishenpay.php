<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Baishenpay extends ApiModel
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

        self::$unit = 2;
        $data                    = [];
        $data['MerchantId']      = $payConf['business_num']; //商户号
        $data['Timestamp']       = date('Y-m-d H:i:s', time() + 60 * 60 * 12); //发送请求的时间
        $data['PaymentTypeCode'] = $bank; //入款类型
        $data['OutPaymentNo']    = $order; //商户的入款流水号
        $data['PaymentAmount']   = $amount * 100; //入款金额,单位为分
        $data['NotifyUrl']       = $ServerUrl; //入款成功异步通知URL
        $data['PassbackParams']  = 'zhif'; //通知应答时
        $signStr                 = self::getSignStr($data, false, true);
        $data['sign']            = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        if ($payConf['is_app'] != '1') {
            self::$reqType        = 'curl';
            self::$payWay         = $payConf['pay_way'];
            self::$httpBuildQuery = true;
        }
        unset($reqData);
        return $data;
    }

    //回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $data                = $request->all();
        $data['PaymentAmount'] = $data['PaymentAmount'] / 100;
        return $data;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $json, $payConf)
    {
        $sourceSign = $json['Sign'];
        unset($json['Sign']);
        $signStr = self::getSignStr($json, false, true);
        $sign    = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        if (strtoupper($sourceSign) == $sign && $json['PaymentState'] == 'S') {
            return true;
        } else {
            return false;
        }
    }
}
