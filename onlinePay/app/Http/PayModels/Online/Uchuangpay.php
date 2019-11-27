<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Uchuangpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

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

        //TODO: do something

        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        $data['action'] = $bank;#固定
        $data['txnamt'] = (string)($amount * 100);
        $data['merid'] = $payConf['business_num']; #商户号
        $data['orderid'] = $order; #商户订单号
        $data['ip'] = self::getIp();
        $data['backurl'] = $ServerUrl; #通知地址
        $data['fronturl'] = $returnUrl;

        $arrayJson = json_encode($data);
        $sign = md5(base64_encode($arrayJson) . $payConf['md5_private_key']);

        unset($reqData);
        if (!self::$isAPP) {

            self::$unit = 2;   //单位分
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
            self::$resType = 'other';
            self::$postType = true;

            $requestData['data'] = "req=" . urlencode(base64_encode($arrayJson)) . "&sign=" . $sign;
            $requestData['req'] = urlencode(base64_encode($arrayJson));
            $requestData['orderid'] = $order;
            $requestData['txnamt'] = $data['txnamt'];
            return $requestData;
        }

        $datas['req'] = base64_encode($arrayJson);
        $datas['sign'] = $sign;
        return $datas;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {

        $result = json_decode($response, true);
        $resp = base64_decode($result['resp']);
        $res = json_decode($resp, true);
        if ($res['respcode'] == '00') {
            $aa['qrcode'] = $res['formaction'];
        } else {
            $aa['respcode'] = $res['respcode'];
            $aa['respmsg'] = $res['respmsg'];
        }
        return $aa;
    }

    //回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $resp = base64_decode($data['resp']);
        $res = json_decode($resp, true);
        $aa['txnamt'] = $res['txnamt'] / 100;
        $aa['orderid'] = $res['orderid'];
        return $aa;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $resp = base64_decode($data['resp']);
        $res = json_decode($resp, true);
        $sign = $data['sign'];
        $Mysign = md5($data['resp'] . $payConf['md5_private_key']);
        if (strtolower($sign) == strtolower($Mysign)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getRequestByType($data)
    {
        // 最终提交的数据
        return $data['data'];

    }
}