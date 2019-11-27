<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Laojinzfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $order                = $reqData['order'];
        $amount               = $reqData['amount'];
        $bank                 = $reqData['bank'];
        $ServerUrl            = $reqData['ServerUrl'];// 异步通知地址
        self::$reqType        = 'curl';
        self::$httpBuildQuery = true;
        self::$payWay         = $payConf['pay_way'];
        self::$resType        = 'other';
        self::$isAPP = true;
        $data               = [];
        $data['channel_id'] = $payConf['business_num'];//商户号
        $data['pay_trench'] = $bank;//银行编码
        $data['out_bill_num'] = $order;//订单号
        $data['amount'] = sprintf('%.2f', $amount);//订单金额
        $data['timestamp'] = date('Y-m-d H:i:s',time());
        $signStr =  self::getSignStr($data, true, true);
        $data['sign'] = md5($signStr . $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    /***
     * @param $resp
     * @return mixed
     */
    public static function getQrCode($resp)
    {
        $result = json_decode($resp, true);
        if ($result['response_code'] == '1001') {
            $result['payUrl']  = $result['response_data']['pay_url'];
        }
        return $result;
    }

    /***
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr =  self::getSignStr($data, true, true);
        $signTrue = md5($signStr . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['pay_status'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}