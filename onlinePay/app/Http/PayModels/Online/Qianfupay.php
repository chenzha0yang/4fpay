<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Common;


class Qianfupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $data = [];

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
        //        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        self::$unit    = 2;
        self::$reqType = 'curl';
        self::$method  = 'header';
        self::$resType = 'json';
//        self::$isAPP   = true;
        self::$payWay  = $payConf['pay_way'];

        if ($payConf['pay_way'] == '1') {
            $payType = 'getway';
        } else {
            $payType = $bank;
        }
        $data                    = [];
        $data['merNo']           = $payConf['business_num'];
        $data['businessOrderNo'] = $order;
        $data['amount']          = $amount * 100;
        $data['paytype']         = $payType;
        $data['notifyurl']       = $ServerUrl;
        $data['attach']          = $amount * 100;
        $signStr                 = self::getSignStr($data, true, true);
        $data['signature']       = self::getMd5Sign($signStr, $payConf['md5_private_key']);
        if ($payConf['pay_way'] == '1') {
            $data['bankcode'] = $bank;
        }
        $data['ip']                  = $_SERVER['HTTP_REFERER'];
        $dataString                  = json_encode($data);
        $header                      = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($dataString)
        );
        $postData['httpHeaders']     = $header;
        $postData['data']            = $dataString;
        $postData['businessOrderNo'] = $order;
        $postData['amount']          = $data['amount'];
        unset($reqData);
        return $postData;
    }

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $result = trans("backField.{$mod}");
        $json   = file_get_contents('php://input');
        $data   = Common::json_decode($json);
        if (empty($data)) {
            return [
                $result['order']  => null,
                $result['amount'] => null,
            ];
        }
        self::$data = $data;
        $data[$result['amount']] = $data[$result['amount']] / 100;

        return $data;
    }

    /**
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        $result = self::$data;
        $sign   = $result['signature'];
        unset($result['signature']);

        $signStr   = self::getSignStr($result, true, true);
        $signature = self::getMd5Sign($signStr, $payConf['md5_private_key']);

        if (strtoupper($sign) == strtoupper($signature) && $result['respCode'] == '0000') {
            return true;
        } else {
            return false;
        }
    }
}