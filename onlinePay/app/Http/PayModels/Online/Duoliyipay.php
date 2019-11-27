<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Duoliyipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $changeUrl = false;

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

        self::$reqType='curl';
        self::$method = 'HEADER';
        self::$isAPP = true;
        self::$payWay = $payConf['pay_way'];

        $data = array(
            'pid'  => $payConf['business_num'],
            'out_trade_no'     => $order,
            'notify_url' => $ServerUrl,
            'name'    => 'pppp',
            'money'   => $amount,
            'pay_type'      => $bank,
        );
        $data['sign'] = md5("pid=".$data['pid']."&out_trade_no=".$data['out_trade_no']."&notify_url=".$data['notify_url']."&name=".$data['name']."&money=".$data['money']."&pay_type=".$data['pay_type']."&key=".$payConf['md5_private_key']);

        $json['data'] = json_encode($data);
        $json['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen(json_encode($data))
        );
        $json['out_trade_no'] = $order;
        $json['money'] = $amount;
        unset($reqData);
        return $json;

    }
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        return $res;
    }
    public static function signOther($model, $result, $payConf)
    {
        $post = file_get_contents('php://input');
        $data = json_decode($post,true);
        $sign = strtoupper($data['sign']);
        unset($data['sign']);
        $mysign = md5("pid=".$data['pid']."&trade_no=".$data['trade_no']."&out_trade_no=".$data['out_trade_no']."&name=".$data['name']."&money=".$data['money']."&pay_money=".$data['pay_money']."&status=".$data['status']."&key=".$payConf['md5_private_key']);
        if ($sign == strtoupper($mysign)&& $data['status'] == '1') {
            return true;
        } else {
            return false;
        }
    }

}