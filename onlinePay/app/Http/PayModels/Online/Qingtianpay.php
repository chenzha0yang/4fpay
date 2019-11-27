<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qingtianpay extends ApiModel
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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];  // 异步通知地址
        $returnUrl = $reqData['returnUrl'];  // 同步通知地址

        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$method = 'header';

        if ($payConf['is_app'] == 1 ) {
            self::$isAPP = true;
        }
        //TODO: do something

        $data = [];
        $data['customerid'] = (int)$payConf['business_num'];
        $data['sdorderno'] = $order;
        $data['total_fee'] = sprintf('%.2f', $amount);
        $data['paytype'] = $bank;
        $data['notifyurl'] = $ServerUrl;
        $data['returnurl'] = $returnUrl;
        $data['remark'] = 'remark';
        $str = "customerid={$data['customerid']}&sdorderno={$data['sdorderno']}&total_fee={$data['total_fee']}&remark={$data['remark']}&paytype={$data['paytype']}&notifyurl={$data['notifyurl']}&returnurl={$data['returnurl']}";
        $data['sign'] = md5($str . '&' . $payConf['md5_private_key']);

        $header = array(
            'Content-Type: application/json; charset=utf-8',
        );
        $post['data'] = json_encode($data);
        $post['httpHeaders'] = $header;
        $post['sdorderno'] = $order;
        $post['total_fee'] = $amount;
        unset($reqData);
        return $post;
    }

    /**
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        $json= file_get_contents("php://input");
        $data = json_decode($json, true);
        $str = "customerid={$data['customerid']}&status={$data['status']}&sdpayno={$data['sdpayno']}&sdorderno={$data['sdorderno']}&order_fee={$data['order_fee']}&total_fee={$data['total_fee']}&paytype={$data['paytype']}";
        $sign = md5($str . '&' . $payConf['md5_private_key']);
        if ($sign == $data['signV2'] && $data['status'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}