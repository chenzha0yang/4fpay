<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wxyuanspay extends ApiModel
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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self::$unit = 2 ;   //单位分
        self::$reqType = 'curl' ;
        self::$payWay = $payConf['pay_way'] ;
        self::$resType = 'other';
        self::$postType = true ;

        if ($payConf['is_app'] == 1) {
            self::$isAPP = true ;
        }
        $data['action']   = $bank;#固定
        $data['txnamt']   = (string)($amount*100);
        $data['merid']    = $payConf['business_num']; #商户号
        $data['orderid']  = $order; #商户订单号
        $data['ip']       = '127.0.0.1';
        $data['backurl']  = $ServerUrl; #通知地址
        $data['fronturl'] = $returnUrl;

        $arrayJson   = json_encode($data);
        $sign        = md5(base64_encode($arrayJson).$payConf['md5_private_key']);
        $requestData['data'] = "req=" . urlencode(base64_encode($arrayJson)) . "&sign=" . $sign;
        $requestData['req'] = urlencode(base64_encode($arrayJson));
        $requestData['sign'] = $sign;
        $requestData['orderid'] = $order;
        $requestData['txnamt'] = $data['txnamt'];

        unset($reqData);
        return $requestData;
    }
    public static function getQrcode($req)
    {
        $result   = json_decode($req, true);
        $resp     = base64_decode($result['resp']);
        $res      = json_decode($resp, true);
        $data['orderid'] = $res['orderid'];
        $data['txnamt'] = $res['txnamt'];
        $data['formaction'] = $res['formaction'];
        $data['respcode'] = $res['respcode'];
        $data['respmsg'] = $res['respmsg'];
        //dd($res);
        return $data;
    }
    public static function getRequestByType($data)
    {
        // 最终提交的数据

        return $data['data'];

    }
}