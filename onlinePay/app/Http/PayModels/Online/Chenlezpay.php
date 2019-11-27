<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Chenlezpay extends ApiModel
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
        self::$unit           = 2;// 金额为分
        self::$reqType        = 'curl';
        self::$httpBuildQuery = true;
        self::$payWay         = $payConf['pay_way'];
        self::$resType        = 'other';

        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        $data               = [];
        $data['PayKey']     = $payConf['business_num'];
        $data['orderid']    = $order;
        $data['PayType']    = $bank;
        $data['notify_url'] = $ServerUrl;
        $data['amount']     = $amount * 100;
        $signStr            = self::getSignStr($data, true, true);
        $data['sign']       = self::getMd5Sign($signStr . '&PaySecret=', $payConf['md5_private_key']);
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
        if ($result['status'] == '10000' && !empty($result['data']['list'])) {
            if (self::$isAPP = true) {
                $result['url'] = $result['data']['list']['payurl'];
            } else {
                $result['url'] = $result['data']['list']['Pcpayurl'];
            }
        } else {
            $result['status']  = $result['status'];
            $result['content'] = $result['data']['content'];
        }
        return $result;
    }

    /***
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request)
    {
        $data           = $request->all();
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }


    /***
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $signStr  = "LhSn={$data['LhSn']}&PayKey={$data['PayKey']}&amount={$data['amount']}&orderid={$data['orderid']}&paytime={$data['paytime']}";
        $signTrue = self::getMd5Sign($signStr . '&PaySecret=', $payConf['md5_private_key']);
        if (strtoupper($data['sign']) == strtoupper($signTrue)) {
            return true;
        } else {
            return false;
        }
    }
}