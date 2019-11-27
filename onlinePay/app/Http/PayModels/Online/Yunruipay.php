<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yunruipay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method = 'header';
        self::$resType = 'other';

        $data['merchant_no']  = $payConf['business_num'];
        $data['nonce_str']    = rand(100000, 999999);
        $data['request_no']   = $order;
        $data['amount']       = $amount;
        $data['pay_channel']  = $bank;
        $data['request_time'] = time();
        $data['notify_url']   = $ServerUrl;
        $data['goods_name']   = 'huawei';
        $data['return_url']   = $returnUrl;
        $data['ip_addr']      = self::getIp();

        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5(urldecode("{$signStr}&key={$payConf['md5_private_key']}")));

        $post['data']        = json_encode($data);
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen(json_encode($data))
        );
        $post['request_no'] = $data['request_no'];
        $post['amount']     = $data['amount'];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $arr = json_decode($response, true);
        $data = $arr['data'];
        $data['success'] = $arr['success'];
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json,true);
        $hada = $data['data'];
        $sign = $hada['sign'];
        unset($hada['sign']);
        $signStr      = self::getSignStr($hada, true, true);
        $mysign  = strtoupper(md5(urldecode("{$signStr}&key={$payConf['md5_private_key']}")));
        if (strtoupper($sign) == $mysign) {
            return true;
        }
        return false;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if ($arr['success']) {
            $data['request_no'] = $arr['data']['request_no'];
            $data['amount'] = $arr['data']['amount'];
            return $data;
        }
        return $arr;

    }

}