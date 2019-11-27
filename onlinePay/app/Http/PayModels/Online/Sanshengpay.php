<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Sanshengpay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        //TODO: do something

        $data = [];
        $data['uid']           = $payConf['business_num'];
        $data['from_order_id'] = $order;
        $data['pay_type']      = $bank;
        $data['money']         = $amount;
        $data['notify_url']    = $ServerUrl;
        $data['return_url']    = $returnUrl;
        $data['beizhu']        = "zhifu";
        $signStr             = self::getSignStr($data, false ,true);
        $data['sign']        = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        self::$reqType = 'curl';
        self::$httpBuildQuery = true;
        self::$isAPP = true;
        self::$resType = 'other';

        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response,true);
        if( (int)$result['status'] === 0 ) {
            $result['pay_params'] = $result["data"]["pay_url"];
        }
        return $result;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $arg = "";
        foreach ($data as $key => $value) {
            if($value || $value == "0"){
                $arg .= "{$key}={$value}&";
            }
        }
        $signStr = substr($arg, 0, count($arg) - 2);
        $signValue = self::getMd5Sign("{$signStr}", $payConf['private_key']);
        if ( $signValue == $sign && $data['status'] == '0' ) {
            return true;
        } else {
            return false;
        }
    }
}