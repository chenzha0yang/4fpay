<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Clouldpay extends ApiModel
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
     * @param array $reqData 接口传递的参数
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order  = $reqData['order'];
        $amount = $reqData['amount'];
        $bank   = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址
        //TODO: do something
        self::$isAPP = true;
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType        = 'other';

        $data                     = [];
        $data['mch_id'] = $payConf['business_num'];//商户号
        $data['service'] = $bank;//银行编码
        $data['out_trade_no'] = $order;//订单号
        $data['trade_time'] = date('YmdHis',time());
        $data['subject'] = 'test';
        $data['body'] = 'test';
        $data['total_fee'] = sprintf('%.2f',$amount);//订单金额
        $data['spbill_create_ip'] = self::getIp();
        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;
        $data['url_type'] = '';
        $data['sign_type'] = 'MD5';
        $data['trade_type'] = 'H5';

        $signStr                  = self::getSignStr($data, true, true);
        $data['sign']             = strtoupper(self::getMd5Sign($signStr . "&key=", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['return_code'] == 'success') {
            if(isset($result['mweb_url'])){
                $result['payurl'] = $result['mweb_url'];
            }else{
                $result['payurl'] = $result['pay_url'];
            }
        }
        return $result;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtoupper(self::getMd5Sign($signStr . "&key=", $payConf['md5_private_key']));
        if (strtoupper($sign) == $signTrue && $data['result_code'] == 'success') {
            return true;
        } else {
            return false;
        }
    }
}