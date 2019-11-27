<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Sanyuanpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $changeUrl = true; // 判断是否跳转APP 默认false

    public static $domain = '';

    public static $array = [];

    /**
     * @param array       $reqData 接口传递的参数
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

        self::$isAPP = true;
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$resType  = 'other';
        self::$method  = 'header';

        $data['app_id'] = $payConf['business_num'];
        $data['ref_number'] = $order;
        $data['amount'] = round($amount,2);
        $data['pay_type'] = (int)$bank;
        $data['nonce'] = 'nonc34w242edse23e2d3e';
        $data['notify_url'] = $ServerUrl;
        $json = json_encode($data);
        $signsha = hash_hmac('sha256', $json, $payConf['md5_private_key']);
        $post['data'] = $json;
        $post['httpHeaders'] = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json),
            'sign:'.$signsha
        ];
        $post['amount'] = $data['amount'];
        $post['ref_number'] = $data['ref_number'];
        self::$domain = $reqData['formUrl'];
        $post['queryUrl'] = $reqData['formUrl'].'/api/order/create';
        unset($reqData);
        return $post;
    }


    public static function getQrCode($req)
    {
        $data = json_decode($req, true);
        if($data['code'] = '1'){
            $pay_url = urldecode($data['data']['pay_url']);
            $data['pay_url'] = self::$domain.$pay_url;
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        self::$array['data'] = $arr;
        self::$array['sign'] = $request->header('sign');
        $data = json_decode($arr,true);
        return $data;
    }

    public static function SignOther($type, $json, $payConf)
    {
        $sign = self::$array['sign'];
        $signTrue = strtolower(hash_hmac('sha256', self::$array['data'], $payConf['md5_private_key']));
        if (strtolower($sign) == $signTrue) {
            return true;
        } else {
            return false;
        }
    }


}
