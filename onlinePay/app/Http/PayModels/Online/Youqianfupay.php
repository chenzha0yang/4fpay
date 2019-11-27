<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Youqianfupay extends ApiModel
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
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$isAPP = true;

        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址

        $data['api_code'] = $payConf['business_num'];  // 商户号
        $data['morder_code'] = $order;
        $data['order_fee'] = $amount;  // 支付金额
        $data['pay_type'] = $bank;  // 交易类型
        $data['notify_url'] = $ServerUrl;  // 异步通知地址Url

        $signStr = "api_code={$data['api_code']}&morder_code={$data['morder_code']}&order_fee={$data['order_fee']}&pay_type={$data['pay_type']}&notify_url={$data['notify_url']}";
        $data['sign'] = strtolower(self::getMd5Sign($signStr.'&apikey=', $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '0') {
            $data['pay_url']=$data['pay_url'];
        }
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = strtolower($data['sign']);
        unset($data['sign']);
        $signStr = "api_code={$data['api_code']}&morder_code={$data['morder_code']}&order_seq={$data['order_seq']}&pay_fee={$data['pay_fee']}&status={$data['status']}";
        $mySign = strtolower(self::getMd5Sign($signStr.'&apikey=', $payConf['md5_private_key']));
        if ($sign == $mySign && $data['status'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}