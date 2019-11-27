<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fuyouvpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $is_app = '';

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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址

        //TODO: do something
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$unit     = 2;
        self::$postType = true;

        $data            = [];
        $data['merid']   = (string)$payConf['business_num'];
        $data['action']  = (string)'USao';
        $data['orderid'] = (string)($order . 'aa'); //订单号
        $data['txnamt']  = (int)($amount * 100); //订单金额 元
        $data['backurl'] = (string)$ServerUrl; //异步回调地址
        $aa              = base64_encode(json_encode($data));
        $res['data']     = [
            'req'  => $aa,
            'sign' => strtolower(md5($aa . $payConf['md5_private_key']))
        ];
        $res['orderid']  = $data['orderid'];
        $res['txnamt']   = $data['txnamt'];
        unset($reqData);
        return $res;
    }

    public static function getRequestByType($post)
    {
        return http_build_query($post['data']);
    }

    public static function getVerifyResult($request)
    {
        $res            = $request->all();
        $data           = base64_decode($res['resp']);
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $signA = $data['sign'];
        $sign  = strtolower(md5($data['resp'] . $payConf['md5_private_key']));
        if ($sign == $signA && $data['resultcode'] == '0000') {
            return true;
        }
        return false;
    }
}