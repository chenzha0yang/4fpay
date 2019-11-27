<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Gaosupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        self::$reqType = 'curl'; // 有即时返回json时，用curl请求
        self::$payWay = $payConf['pay_way']; //生成二维码
        self::$resType = 'other'; //请求支付时，返回来的即时json数据，做特殊处理
        self::$isAPP = true;
        self::$unit = 2;
        self::$method = 'get';
        $data['shop_id'] = $payConf['business_num'];//商户号
        $data['trade_no'] = $order;//订单号
        $data['channel'] = $bank;
        $data['tzurl'] = urlencode($ServerUrl);
        $data['money'] = $amount * 100;//订单金额
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = md5(strtolower("{$signStr}&token={$payConf['md5_private_key']}"));
        unset($reqData);
        return $data;
    }

    /**
     * 二维码、链接处理
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res)
    {
        $result = json_decode($res, true);
        if ($result['status'] == '1') {
            $result['url'] = $result['data']['url'];
        }
        return $result;

    }

    public static function getVerifyResult($request)
    {
        $arr = $request->all();
        $res['money'] = $arr['money'] / 100;
        $res['trade_no'] = $arr['trade_no'];
        return $res;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, false, true);
        $mySign = md5(strtolower("{$signStr}&token={$payConf['md5_private_key']}"));
        if (strtolower($sign) == $mySign && $data['status'] == 1) {
            return true;
        } else {
            return false;
        }
    }

}