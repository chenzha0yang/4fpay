<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tongbaozpay extends ApiModel
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
        $returnUrl = $reqData['returnUrl']; // 同步通知地址


        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$isAPP = true;
        self::$postType = true;

        $post                     = $data = [];
        $post['out_trade_no']     = $order;
        $post['order_name']       = $payConf['business_num'];
        $post['total_amount']     = $amount;
        $post['spbill_create_ip'] = self::getIp();
        $post['notify_url']       = $ServerUrl;
        $post['return_url']       = $returnUrl;

        $data['app_id']    = $payConf['business_num'];
        $data['method']    = $bank;
        $data['content']   = json_encode($post);
        $signStr      = self::getSignStr($data, true, true);
        $data['version']   = '1.0';
        $data['sign_type'] = 'MD5';
        ksort($data);
        $data['sign'] = md5($signStr . '&key=' . $payConf['md5_private_key']);
        $data['out_trade_no'] = $post['out_trade_no'];
        $data['total_amount'] = $post['total_amount'];
        unset($reqData);
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {

        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['sign_type']);
        $md5str = self::getSignStr($data, true, true);
        $mySign    = md5($md5str.'&key='.$payConf['md5_private_key']);
        if ($sign == $mySign ) {
            return true;
        } else {
            return false;
        }
    }

    public static function getRequestByType($queryData)
    {
        unset($queryData['out_trade_no'],$queryData['total_amount']);
        return $queryData;
    }
}