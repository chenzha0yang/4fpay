<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Dagzpay extends ApiModel
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

        //TODO: do something
        $data               = [];
        $data['account']    = $payConf['business_num'];
        $data['resqn']      = $order;
        $data['pay_amount'] = $amount;
        $data['notify_url'] = $ServerUrl;
        $data['payway']     = $bank;
        $data['body']       = '';
        $data['pay_ip']     = '';
        $data['version ']   = '';
        $data['return_url'] = '';
        $data['key']        = $payConf['md5_private_key'];
        $signStr            = self::getSignStr($data, true, true);
        $data['sign']       = md5($signStr);
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request)
    {
        $data               = $request->all();
        $data['pay_amount'] = $data['pay_amount'] / 100;
        return $data;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['mer_sign'];
        unset($data['mer_sign']);
        $data['key'] = $payConf['md5_private_key'];
        $signStr     = self::getSignStr($data, true, true);
        $signTrue    = md5($signStr);
        if ($sign == $signTrue && $data['status'] == '200') {
            return true;
        } else {
            return false;
        }
    }
}
